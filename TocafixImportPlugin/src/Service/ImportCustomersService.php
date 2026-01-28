<?php

namespace TocafixImportPlugin\Service;

use Doctrine\DBAL\Connection;
use Exception;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexerRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\Country\CountryCollection;
use Shopware\Core\System\NumberRange\ValueGenerator\NumberRangeValueGeneratorInterface;
use Shopware\Core\System\Salutation\SalutationCollection;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class ImportCustomersService
{
    /**
     * Name of the import xml file
     */
    private const IMPORT_FILENAME = 'import/customer_import.csv';

    public const BATCH = 100;

    /**
     * @var EntityRepository
     */
    private $customerRepository;

    /**
     * @var string
     */
    protected $shopwareProjectFilesImportDir;

    /**
     * @var EntityRepository
     */
    private $countryRepository;

    /**
     * @var EntityRepository
     */
    private $customerGroupRepository;

    /**
     * @var EntityRepository
     */
    private $salutationRepository;

    /**
     * @var EntityRepository
     */
    private $salesChannelRepository;

    /**
     * @var EntityRepository
     */
    private $paymentMethodRepository;

    /**
     * @var EntityRepository
     */
    private $tagRepository;

    /** 
     * @var NumberRangeValueGeneratorInterface
     */
    private $numberRangeValueGenerator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Connection
     */
    private $connection;

    private RequestStack $requestStack;

    public function __construct(
        EntityRepository $customerRepository,
        EntityRepository $countryRepository,
        EntityRepository $customerGroupRepository,
        EntityRepository $salutationRepository,
        EntityRepository $salesChannelRepository,
        EntityRepository $paymentMethodRepository,
        EntityRepository $tagRepository,
        NumberRangeValueGeneratorInterface $numberRangeValueGenerator,
        RequestStack $requestStack,
        LoggerInterface $logger,
        $shopwareProjectFilesImportDir,
        Connection $connection
    ) {
        $this->customerRepository = $customerRepository;
        $this->countryRepository = $countryRepository;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->salutationRepository = $salutationRepository;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->tagRepository = $tagRepository;
        $this->numberRangeValueGenerator = $numberRangeValueGenerator;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
        $this->shopwareProjectFilesImportDir = $shopwareProjectFilesImportDir;
        $this->connection = $connection;
    }

    public function executeCli(InputInterface $input, OutputInterface $output): int
    {
        error_reporting(-1);
        ini_set('memory_limit', '-1');

        $output->writeln('<info>Starting to import customers from interface...</info>');

        $importCustomers = $this->loadCustomers();

        if (empty($importCustomers)) {
            $output->writeln('<warning>No customers found to import.</warning>');
            return 0;
        }

        $progressBar = new ProgressBar($output, count($importCustomers));
        $progressBar->setFormat('very_verbose');
        $progressBar->start();

        $customersBatch = array_chunk($importCustomers, self::BATCH);

        foreach ($customersBatch as $key => $customersData) {
            $this->saveCustomers($customersData, $progressBar, $output);

            sleep(1);
        }
        
        $progressBar->finish();
        $output->writeln('');
        $output->writeln('Completed Saving Customers!');

        // Exit code 0 for success
        return 0;
    }

    public function execute(): int
    {
        error_reporting(-1);
        ini_set('memory_limit', '-1');

        $importCustomers = $this->loadCustomers();

        if (empty($importCustomers)) {
            $this->logger->info('No customers found to import.');
            return 0;
        }


        $customersBatch = array_chunk($importCustomers, self::BATCH);

        foreach ($customersBatch as $key => $customersData) {
            $this->saveCustomers($customersData, null, null);

            sleep(1);
        }

        return 0;
    }

    private function saveCustomers(array $customersData, $progressBar = null, $output = null)
    {
        $context = Context::createDefaultContext();

        $context->addState(EntityIndexerRegistry::USE_INDEXING_QUEUE);

        $countries = $this->countryRepository->search(new Criteria(), $context)->getEntities();
        $salutations = $this->salutationRepository->search(new Criteria(), $context)->getEntities();
        $customerGroupId = $this->fetchCustomerGroupId(new Criteria(), $context);
        $salesChannelId = $this->fetchSalesChannelId(new Criteria(), $context);
        $paymentMethodId = $this->fetchPaymentMethodId(new Criteria(), $context);

        if (!$customerGroupId || !$salesChannelId || !$paymentMethodId) {
            $errorMsg = 'Required entities not found: customerGroupId, salesChannelId, or paymentMethodId';
            $this->logger->error($errorMsg);
            if ($output) {
                $output->writeln('<error>' . $errorMsg . '</error>');
            }
            return;
        }

        $allEmails = array_column($customersData, 'E-Mail');
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('email', $allEmails));
        $existingCustomers = $this->customerRepository->search($criteria, $context)->getEntities();

        $emailMap = [];
        foreach ($existingCustomers as $existing) {
            $emailMap[$existing->getEmail()] = $existing->getId();
        }

        $customers = [];

        foreach ($customersData as $data) {
            if ($progressBar) {
                $progressBar->advance();
            }

            try {
                if (empty($data['E-Mail'])) {
                    throw new Exception('Email is required but missing');
                }

                $customerNumber = $this->numberRangeValueGenerator->getValue(
                    'customer',
                    $context,
                    $salesChannelId
                );
                $email = $data['E-Mail'];
                $customerId = $emailMap[$email] ?? Uuid::randomHex();

                $salutationId = $this->fetchSalutationId($salutations, $data['Salutation'] ?? '');
                $countryId = $this->fetchCountryId($countries, $data['Country'] ?? '');

                if (!$salutationId) {
                    throw new Exception('Could not determine salutation for customer');
                }

                if (!$countryId) {
                    $this->logger->warning('Country not found for: ' . ($data['Country'] ?? 'N/A') . ', using default');
                }

                $customerAddress = [
                    'id' => Uuid::randomHex(),
                    'customerId' => $customerId,
                    'salutationId' => $salutationId,
                    'company' => $data['Firma'] ?? '',
                    'firstName' => !empty($data['Firstname']) ? $data['Firstname'] : "-",
                    'lastName' => !empty($data['Lastname']) ? $data['Lastname'] : "-",
                    'zipcode' => $data['Postcode'] ?? '',
                    'city' => $data['City'] ?? '',
                    'street' => !empty($data['Street']) ? $data['Street'] : "not defined",
                    'phoneNumber' => !empty($data['Phone']) ? $data['Phone'] : "0000000000",
                    'countryId' => $countryId,
                ];

                $customers[] = [
                    'id' => $customerId,
                    'customerNumber' => $customerNumber,
                    'salutationId' => $salutationId,
                    'firstName' => !empty($data['Firstname']) ? $data['Firstname'] : "-",
                    'lastName' => !empty($data['Lastname']) ? $data['Lastname'] : "-",
                    'company' => $data['Firma'] ?? '',
                    'email' => $email,
                    'active' => true,
                    'groupId' => $customerGroupId,
                    'salesChannelId' => $salesChannelId,
                    'defaultBillingAddress' => $customerAddress,
                    'defaultShippingAddress' => $customerAddress,
                    'defaultPaymentMethodId' => $paymentMethodId,
                    'addresses' => [$customerAddress],
                    'tags' => $this->getTags(new Criteria(), $context, $data, $customerId)
                ];
            } catch (Exception $e) {
                $errorMsg = 'Customer with email: ' . ($data['E-Mail'] ?? 'unknown') . ' could not be prepared. Message: ' . $e->getMessage();
                $this->logger->error($errorMsg);
                if ($output) {
                    $output->writeln('<error>' . $errorMsg . '</error>');
                }
            }
        }

        if (!empty($customers)) {
            try {
                $request = new Request();
                $this->requestStack->push($request);
                $this->customerRepository->upsert($customers, $context);
            } catch (Exception $e) {
                $errorMsg = 'Batch customer creation failed: ' . $e->getMessage();
                $this->logger->error($errorMsg);
                if ($output) {
                    $output->writeln('<error>' . $errorMsg . '</error>');
                }
            }
        }
    }

    /**
     * Load products from import xml file and return associative array with products as result
     *
     * @return array
     * @throws \Exception
     */
    private function loadCustomers()
    {
        return $this->readCSV($this->shopwareProjectFilesImportDir . self::IMPORT_FILENAME);
    }

    /**
     * @param string $filePath
     * @return array
     */
    public function readCSV(string $filePath): array
    {
        $orderArray = [];
        $filePath = trim($filePath);
        clearstatcache(true, $filePath);

        if (!file_exists($filePath)) {
            $this->logger->error('CSV file not found: ' . $filePath);
            return [];
        }

        if (($handle = fopen($filePath, "r")) !== false) {
            $keys = fgetcsv($handle, 2000, ',');

            if ($keys === false || empty($keys)) {
                fclose($handle);
                $this->logger->error('CSV file has no header row: ' . $filePath);
                return [];
            }

            $keys = array_map(static function ($key) {
                $key = trim($key);
                $key = preg_replace('/^\x{FEFF}/u', '', $key);
                return $key;
            }, $keys);

            while (($line = fgetcsv($handle, 2000, ",")) !== false) {
                try {
                    if (count($keys) !== count($line)) {
                        $this->logger->warning('Row has different column count than header, skipping');
                        continue;
                    }
                    $data = array_combine($keys, $line);
                    if ($data !== false) {
                        $orderArray[] = $data;
                    }
                } catch (\Exception $e) {
                    $this->logger->warning('Error parsing CSV row: ' . $e->getMessage());
                }
            }
            fclose($handle);
        } else {
            $this->logger->error('Could not open CSV file: ' . $filePath);
        }

        return $orderArray;
    }

    /**
     * @param Criteria $criteria
     * @param Context $context
     * @return string|null
     */
    public function fetchCustomerGroupId(Criteria $criteria, Context $context): ?string
    {
        $criteria->addFilter(new EqualsFilter('name', 'Standard-Kundengruppe'));

        return $this->customerGroupRepository->searchIds($criteria, $context)->firstId();
    }

    public function fetchSalesChannelId(Criteria $criteria, Context $context): ?string
    {
        $criteria->addFilter(new EqualsFilter('name', 'tocafix.ch'));

        return $this->salesChannelRepository->searchIds($criteria, $context)->firstId();
    }

    /**
     * @param SalutationCollection|null $salutations
     * @param string $id
     * @return string|null
     */
    public function fetchSalutationId(?SalutationCollection $salutations, string $id): ?string
    {
        if (!$salutations || $salutations->count() === 0) {
            return null;
        }

        switch ($id) {
            case 'Male':
                $salutation = $salutations->filterByProperty('salutationKey', 'mr')->first();
                return $salutation ? $salutation->getId() : null;
            case 'Female':
                $salutation = $salutations->filterByProperty('salutationKey', 'mrs')->first();
                return $salutation ? $salutation->getId() : null;
            default:
                $salutation = $salutations->filterByProperty('salutationKey', 'not_specified')->first();
                if (!$salutation) {
                    // Fallback to any salutation if 'not_specified' doesn't exist
                    $salutation = $salutations->first();
                }
                return $salutation ? $salutation->getId() : null;
        }
    }

    /**
     * @param CountryCollection|null $countries
     * @param string $name
     * @return string|null
     */
    public function fetchCountryId(?CountryCollection $countries, string $name): ?string
    {
        if (!$countries || empty($name)) {
            return null;
        }

        $country = $countries->filterByProperty('name', $name)->first();
        return $country ? $country->getId() : null;
    }

    /**
     * @param Criteria $criteria
     * @param Context $context
     * @return string|null
     */
    public function fetchPaymentMethodId(Criteria $criteria, Context $context): ?string
    {
        $criteria->addFilter(new EqualsFilter('name', "Direkt vor Ort"));

        return $this->paymentMethodRepository->searchIds($criteria, $context)->firstId();
    }

    public function getTags(Criteria $criteria, Context $context, array $data, string $customerId): array
    {
        $tags = [];
        for ($i = 1; $i <= 4; $i++) {
            if (isset($data["Tag" . $i]) && !empty($data["Tag" . $i])) {
                $tag = $this->getTag($criteria, $context, $data["Tag" . $i]);
                if ($tag) {
                    $tags[] = $tag;
                }
            }
        }

        return $tags;
    }

    /**
     * @param Criteria $criteria
     * @param Context $context
     * @param string $name
     * @return array|null
     */
    public function getTag(Criteria $criteria, Context $context, $name): ?array
    {
        if (empty($name)) {
            return null;
        }

        try {
            $tagId = $this->connection->fetchOne('SELECT id FROM tag WHERE name = :name', ['name' => $name]);
            if ($tagId) {
                $tagId = Uuid::fromBytesToHex($tagId);
            } else {
                $tagId = Uuid::randomHex();
            }

            return ['id' => $tagId, 'name' => $name];
        } catch (Exception $e) {
            $this->logger->error('Error fetching/creating tag: ' . $e->getMessage());
            return null;
        }
    }
}