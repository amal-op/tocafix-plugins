<?php

namespace TocafixImportPlugin\Service;

use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerRecovery\CustomerRecoveryEntity;
use Shopware\Core\Checkout\Customer\Event\CustomerAccountRecoverRequestEvent;
use Shopware\Core\Checkout\Customer\Event\PasswordRecoveryUrlEvent;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\RepositoryIterator;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexerRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Context\AbstractSalesChannelContextFactory;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CustomerMailService
{
    /*
     * Amount of entities to execute per round.
     * */
    public const BATCH = 500;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var EntityRepository
     */
    private $customerRepository;

    /**
     * @var EntityRepository
     */
    private $customerRecoveryRepository;

    /**
     * @var EventDispatcherInterface
     */
    private  $eventDispatcher;

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    /**
     * @var AbstractSalesChannelContextFactory
     */
    private $salesChannelContextFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        EntityRepository $customerRepository,
        EntityRepository $customerRecoveryRepository,
        EventDispatcherInterface $eventDispatcher,
        SystemConfigService $systemConfigService,
        AbstractSalesChannelContextFactory $salesChannelContextFactory,
        LoggerInterface $logger
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerRecoveryRepository = $customerRecoveryRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->systemConfigService = $systemConfigService;
        $this->salesChannelContextFactory = $salesChannelContextFactory;
        $this->logger = $logger;

        $this->context = Context::createDefaultContext();
        $this->context->addState(EntityIndexerRegistry::USE_INDEXING_QUEUE);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    public function executeCli(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '-1');

        $output->writeln('<info>Starting to Send password recovery mail from interface...</info>');

        $criteria = new Criteria();
        $criteria->setLimit(500);
        $criteria->addAssociation('salutation');
        $criteria->addAssociation('salesChannel');
        $criteria->addFilter(new EqualsFilter('active', 1));
        $criteria->addSorting(new FieldSorting('id'));  

        $iterator = new RepositoryIterator($this->customerRepository, $this->context, $criteria);

        $progressBar = new ProgressBar($output, $iterator->getTotal());
        $progressBar->start();

        while (($result = $iterator->fetch()) !== null) {
            /** @var CustomerCollection */
            $customers = $result->getEntities();

            foreach ($customers as $customer) {
                $progressBar->advance();
                $customerId = $customer->getId();

                $customerIdCriteria = new Criteria();
                $customerIdCriteria->addFilter(new EqualsFilter('customerId', $customerId));
                $customerIdCriteria->addAssociation('customer.salutation');

                $existingRecovery = $this->customerRecoveryRepository->search($customerIdCriteria, $this->context)->first();
                if ($existingRecovery instanceof CustomerRecoveryEntity) {
                    $this->deleteRecoveryForCustomer($existingRecovery, $this->context);
                }
        
                $recoveryData = [
                    'customerId' => $customerId,
                    'hash' => Random::getAlphanumericString(32),
                ];
        
                $this->customerRecoveryRepository->create([$recoveryData], $this->context);
        
                $customerRecovery = $this->customerRecoveryRepository->search($customerIdCriteria, $this->context)->first();
                \assert($customerRecovery instanceof CustomerRecoveryEntity);
        
                $hash = $customerRecovery->getHash();
                $contextId = Uuid::randomHex();
                $salesChannelContext = $this->salesChannelContextFactory->create(
                    $contextId,
                    $customer->getSalesChannelId(),
                );
        
                $recoverUrl = $this->getRecoverUrl($salesChannelContext, $hash, "https://tocafix.ch", $customerRecovery);
        
                $event = new CustomerAccountRecoverRequestEvent($salesChannelContext, $customerRecovery, $recoverUrl);
                $this->eventDispatcher->dispatch($event, CustomerAccountRecoverRequestEvent::EVENT_NAME);
            }
        }
        $progressBar->finish();

        $output->writeln(' ');
        $output->writeln('<info>Send password recovery mail successful</info>');

        return 0;
    }

    private function deleteRecoveryForCustomer(CustomerRecoveryEntity $existingRecovery, Context $context): void
    {
        $recoveryData = [
            'id' => $existingRecovery->getId(),
        ];

        $this->customerRecoveryRepository->delete([$recoveryData], $context);
    }

    private function getRecoverUrl(
        SalesChannelContext $context,
        string $hash,
        string $storefrontUrl,
        CustomerRecoveryEntity $customerRecovery
    ): string {
        $urlTemplate = $this->systemConfigService->get(
            'core.loginRegistration.pwdRecoverUrl',
            $context->getSalesChannelId()
        );
        if (!\is_string($urlTemplate)) {
            $urlTemplate = '/account/recover/password?hash=%%RECOVERHASH%%';
        }

        $urlEvent = new PasswordRecoveryUrlEvent($context, $urlTemplate, $hash, $storefrontUrl, $customerRecovery);
        $this->eventDispatcher->dispatch($urlEvent);

        return rtrim($storefrontUrl, '/') . str_replace(
            '%%RECOVERHASH%%',
            $hash,
            $urlEvent->getRecoveryUrl()
        );
    }
}