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
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\Country\CountryCollection;
use Shopware\Core\System\NumberRange\ValueGenerator\NumberRangeValueGeneratorInterface;
use Shopware\Core\System\Salutation\SalutationCollection;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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


    public function __construct(
        EntityRepository $customerRepository,
        EntityRepository $countryRepository,
        EntityRepository $customerGroupRepository,
        EntityRepository $salutationRepository,
        EntityRepository $salesChannelRepository,
        EntityRepository $paymentMethodRepository,
        EntityRepository $tagRepository,
        NumberRangeValueGeneratorInterface $numberRangeValueGenerator,
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
        $this->logger = $logger;
        $this->shopwareProjectFilesImportDir = $shopwareProjectFilesImportDir;
        $this->connection = $connection;
    }

    // Actual code executed in the command
    public function executeCli(InputInterface $input, OutputInterface $output): int
    {
        error_reporting(-1);
        ini_set('memory_limit', '-1');

        $output->writeln('<info>Starting to import customers from interface...</info>');

        // read products from import xml file
        $importCustomers = $this->loadCustomers();

        // update products descriptions
        $progressBar = new ProgressBar($output, count($importCustomers));
        $progressBar->setFormat('very_verbose');
        $progressBar->start();

        $customersBatch = array_chunk($importCustomers, self::BATCH);

        foreach ($customersBatch as $key => $customersData) {
            $this->saveCustomers($customersData, $progressBar, $output);

            sleep(1);
        }
        $output->writeln('Completed Saving Customers!');

        // Exit code 0 for success
        return 0;
    }

    // Actual code executed in the command
    public function execute(): int
    {
        error_reporting(-1);
        ini_set('memory_limit', '-1');

        // read products from import xml file
        $importCustomers = $this->loadCustomers();

        // update products descriptions

        $customersBatch = array_chunk($importCustomers, self::BATCH);

        foreach ($customersBatch as $key => $customersData) {
            $this->saveCustomers($customersData, false);

            sleep(1);
        }

        // Exit code 0 for success
        return 0;
    }

    private function saveCustomers(array $customersData, $progressBar)
    {
        $context = Context::createDefaultContext();
        $context->addState(EntityIndexerRegistry::USE_INDEXING_QUEUE);

        $countries = $this->countryRepository->search(new Criteria(), $context)->getEntities();
        $salutations = $this->salutationRepository->search(new Criteria(), $context)->getEntities();
        $customerGroupId = $this->fetchCustomerGroupId(new Criteria(), $context);
        $salesChannelId = $this->fetchSalesChannelId(new Criteria(), $context);
        $paymentMethodId = $this->fetchPaymentMethodId(new Criteria(), $context);
        $customers = [];

        foreach ($customersData as $data) {
            if ($progressBar) {
                $progressBar->advance();
            }
            $customerNumber = $this->numberRangeValueGenerator->getValue(
                'customer',
                $context,
                $salesChannelId
            );
            $customerId = Uuid::randomHex();

            $customerAddress = [
                'id' => Uuid::randomHex(),
                'customerId' => $customerId,
                'salutationId' => $this->fetchSalutationId($salutations, $data['Salutation']),
                'company' => $data['Firma'],
                'firstName' => $data['Firstname'] ?: "-",
                'lastName' => $data['Lastname'] ?: "-",
                'zipcode' => $data['Postcode'],
                'city' => $data['City'],
                'street' => $data['Street'] ?: "not defined",
                'phoneNumber' => $data['Phone'] ?: "0000000000",
                'countryId' => $this->fetchCountryId($countries, $data['Country']),
            ];

            $customer = [
                'id' => $customerId,
                'customerNumber' => $customerNumber,
                'salutationId' => $this->fetchSalutationId($salutations, $data['Salutation']),
                'firstName' => $data['Firstname'] ?: "-",
                'lastName' => $data['Lastname'] ?: "-",
                'company' => $data['Firma'],
                'email' => $data['E-Mail'],
                'active' => true,
                'groupId' => $customerGroupId,
                'salesChannelId' => $salesChannelId,
                'defaultBillingAddress' => $customerAddress,
                'defaultShippingAddress' => $customerAddress,
                'defaultPaymentMethodId' => $paymentMethodId,
                'addresses' => [
                    $customerAddress
                ],
                'tags' => $this->getTags(new Criteria(), $context, $data, $customerId)
            ];
            try {
                $this->customerRepository->create([$customer], $context);
            } catch (Exception $e) {
                $this->logger->info('<error>Customer with email: ' . $data['E-Mail'] . ' could not be imported. Message: ' . $e->getMessage() . '</error>');
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

        if (($handle = fopen($filePath, "r")) !== false) {
            $keys = fgetcsv($handle, 2000, ',');

            $keys = array_map(static function ($key) {
                return iconv("UTF-8", "ISO-8859-1//IGNORE", $key);
            }, $keys);

            while (($line = fgetcsv($handle, 2000, ",")) !== false) {
                try {
                    $data = array_combine($keys, $line);
                    $orderArray[] = $data;
                } catch (\Exception $e) {
                }
            }
            fclose($handle);
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
     * @return mixed
     */
    public function fetchSalutationId(?SalutationCollection $salutations, string $id)
    {
        switch ($id) {
            case 'Male':
                return $salutations->filterByProperty('salutationKey', 'mr')->first()->id;
            case 'Female':
                return $salutations->filterByProperty('salutationKey', 'mrs')->first()->id;
            default:
                return $salutations->filterByProperty('salutationKey', 'undefined')->first()->id;
        }
    }

    /**
     * @param CountryCollection|null $countries
     * @param string $iso
     * @return mixed
     */
    public function fetchCountryId(?CountryCollection $countries, string $name)
    {
        $country = $countries->filterByProperty('name', $name)->first();
        return $country->id ?? '';
    }

    /**
     * @param Criteria $criteria
     * @param Context $context
     * @param string $type
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
            if (isset($data["Tag" . $i])) {
                if ($data["Tag" . $i]) {
                    $tags[] = $this->getTag($criteria, $context, $data["Tag" . $i]);
                }
            }
        }

        return $tags;
    }

    /**
     * @param Criteria $criteria
     * @param Context $context
     * @param string $name
     * @return string|null
     */
    public function getTag(Criteria $criteria, Context $context, $name): ?array
    {
        $tagId = $this->connection->fetchOne('SELECT id FROM tag WHERE name = :name', ['name' => $name]);
        if ($tagId) {
            $tagId = Uuid::fromBytesToHex($tagId);
        }
        if (!$tagId) {
            $tagId = Uuid::randomHex();
        }

        return ['id' => $tagId, 'name' => $name];
    }
}
