<?php declare(strict_types=1);

namespace VioCustomerPrice;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;

class VioCustomerPrice extends Plugin
{
    public function uninstall(UninstallContext $uninstallContext): void
    {
        if (!$uninstallContext->keepUserData()) {
            $connection = $this->container->get(Connection::class);
            if ($connection) {
                $column = $connection->executeQuery('select COLUMN_NAME from information_schema.columns where table_name = \'product\' and column_name = \'customerPrices\';')
                    ->fetchOne();
                if ($column === 'customerPrices') {
                    $connection->executeStatement('ALTER TABLE product DROP COLUMN customerPrices;');
                }
                $column = $connection->executeQuery('select COLUMN_NAME from information_schema.columns where table_name = \'customer\' and column_name = \'customerPrices\';')
                    ->fetchOne();
                if ($column === 'customerPrices') {
                    $connection->executeStatement('ALTER TABLE customer DROP COLUMN customerPrices;');
                }
                $connection->executeStatement('DROP TABLE IF EXISTS vio_customer_price;');
            }
            /** @var EntityRepository $importExportProfileRepository */
            $importExportProfileRepository = $this->container->get('import_export_profile.repository');
            if ($importExportProfileRepository) {
                $importExportProfileIds = $importExportProfileRepository->searchIds(
                    (new Criteria())
                        ->addFilter(new EqualsFilter('sourceEntity', 'vio_customer_price')),
                    $uninstallContext->getContext()
                )->getIds();
                $importExportProfileIds = array_map(static fn ($id) => ['id' => $id], $importExportProfileIds);
                if (!empty($importExportProfileIds)) {
                    // patch false Data from version < 1.4.2
                    $update = array_map(static function ($idArray) {
                        $idArray['systemDefault'] = false;

                        return $idArray;
                    }, $importExportProfileIds);
                    $importExportProfileRepository->update($update, $uninstallContext->getContext());
                    $importExportProfileRepository->delete($importExportProfileIds, $uninstallContext->getContext());
                }
            }
        }
        parent::uninstall($uninstallContext);
    }

    public function install(InstallContext $installContext): void
    {
        $this->addImportExportProfile($installContext->getContext());
        parent::install($installContext);
    }

    public function update(UpdateContext $updateContext): void
    {
        if (version_compare($updateContext->getCurrentPluginVersion(), '1.3.0', '<')) {
            $this->addImportExportProfile($updateContext->getContext());
        }
        if (version_compare($updateContext->getCurrentPluginVersion(), '1.3.0', '>=')
            && version_compare($updateContext->getCurrentPluginVersion(), '1.4.3', '<')) {
            /** @var EntityRepository $importExportProfileRepository */
            $importExportProfileRepository = $this->container->get('import_export_profile.repository');
            if ($importExportProfileRepository) {
                $importExportProfileIds = $importExportProfileRepository->searchIds(
                    (new Criteria())
                        ->addFilter(new EqualsFilter('sourceEntity', 'vio_customer_price')),
                    $updateContext->getContext()
                )->getIds();
                if (!empty($importExportProfileIds)) {
                    $update = array_map(static fn ($id) => [
                        'id' => $id,
                        'systemDefault' => false,
                    ], $importExportProfileIds);
                    $importExportProfileRepository->update($update, $updateContext->getContext());
                }
            }
        }
        parent::update($updateContext);
    }

    private function addImportExportProfile(Context $context): void
    {
        /** @var EntityRepository $repository */
        $repository = $this->container->get('import_export_profile.repository');
        if ($repository) {
            $profileId = $repository->searchIds(
                (new Criteria())
                    ->addFilter(new EqualsFilter('sourceEntity', 'vio_customer_price')),
                $context
            )->firstId();
            if ($profileId === null) {
                $repository->create([[
                    'name' => 'Default customer prices',
                    'systemDefault' => false,
                    'sourceEntity' => 'vio_customer_price',
                    'fileType' => 'text/csv',
                    'delimiter' => ';',
                    'enclosure' => '"',
                    'technicalName' => 'vio_customer_price_default',
                    'mapping' => [
                        [
                            'key' => 'id',
                            'mappedKey' => 'id',
                        ],
                        [
                            'key' => 'productId',
                            'mappedKey' => 'productId',
                        ],
                        [
                            'key' => 'customerId',
                            'mappedKey' => 'customerId',
                        ],
                        [
                            'key' => 'quantityEnd',
                            'mappedKey' => 'quantityEnd',
                        ],
                        [
                            'key' => 'quantityStart',
                            'mappedKey' => 'quantityStart',
                        ],
                        [
                            'key' => 'price',
                            'mappedKey' => 'price',
                        ],
                    ],
                    'translations' => [
                        Defaults::LANGUAGE_SYSTEM => [
                            'label' => 'Default customer prices',
                        ],
                    ],
                ]], $context);
            }
        }
    }
}
