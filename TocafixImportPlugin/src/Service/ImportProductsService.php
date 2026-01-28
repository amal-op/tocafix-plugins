<?php

declare(strict_types=1);

namespace TocafixImportPlugin\Service;

use Psr\Log\LoggerInterface;
use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexerRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteException;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Core\System\Tax\TaxEntity;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 *
 */
class ImportProductsService
{
    /**
     * Name of the import csv file
     */
    private const IMPORT_FILENAME = 'import/product_import.csv';

    /**
     * Tax name for standard rate
     */
    private const TAX_NAME = 'Standard rate';

    /*
     * Amount of entities to execute per round.
     * */
    public const BATCH = 200;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var EntityRepository
     */
    private $productRepository;

    /**
     * @var EntityRepository
     */
    private $productPropertyRepository;

    /**
     * @var EntityRepository
     */
    private $taxRepository;

    /**
     * @var EntityRepository
     */
    private $unitRepository;

    /**
     * @var EntityRepository
     */
    private $salesChannelRepository;

    /**
     * @var EntityRepository
     */
    private $propertyGroupRepository;

    /**
     * @var EntityRepository
     */
    private $productConfiguratorSettingRepository;

    /**
     * @var EntityRepository
     */
    private $currencyRepository;

    /**
     * @var ImageImportService
     */
    protected $imageImportService;

    /**
     * @var ImportHelper
     */
    protected $importHelper;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    protected $shopwareProjectFilesImportDir;

    /**
     * @var string
     */
    protected $taxId;

    /**
     * @var string
     */
    protected $currencyCHFId;

    /**
     * @var string
     */
    protected $currencyEURId;

    public function __construct(
        EntityRepository $productRepository,
        EntityRepository $productPropertyRepository,
        EntityRepository $taxRepository,
        EntityRepository $unitRepository,
        EntityRepository $salesChannelRepository,
        EntityRepository $propertyGroupRepository,
        EntityRepository $productConfiguratorSettingRepository,
        EntityRepository $currencyRepository,
        ImageImportService $imageImportService,
        ImportHelper $importHelper,
        \Doctrine\DBAL\Connection $connection,
        LoggerInterface $logger,
        $shopwareProjectFilesImportDir
    ) {
        $this->productRepository = $productRepository;
        $this->productPropertyRepository = $productPropertyRepository;
        $this->taxRepository = $taxRepository;
        $this->unitRepository = $unitRepository;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->propertyGroupRepository = $propertyGroupRepository;
        $this->productConfiguratorSettingRepository = $productConfiguratorSettingRepository;
        $this->currencyRepository = $currencyRepository;
        $this->imageImportService = $imageImportService;
        $this->importHelper = $importHelper;
        $this->connection = $connection;
        $this->logger = $logger;
        $this->shopwareProjectFilesImportDir = $shopwareProjectFilesImportDir;

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
        // avoid reaching memory limit
        ini_set('memory_limit', '-1');

        $this->input = $input;
        $this->output = $output;

        $output->writeln('<info>Starting to import products from interface...</info>');

        // set tax id
        $this->setTaxId();

        if (empty($this->taxId)) {
            $output->writeln("<error>No taxId was found for '" . self::TAX_NAME . "'</error>");
            return 1;
        }

        // set currencies
        $this->currencyCHFId = $this->getCurrencyId("CHF");
        $this->currencyEURId = $this->getCurrencyId("EUR");
        // read products from import csv file
        $importProducts = $this->loadProducts();
        //Read and insert parent and simple products
        $mainProducts = array_filter($importProducts, function ($product) {
            return !$this->isChildProduct($product['agrzusid']);
        });
        $csvBatchMainProducts = array_chunk($mainProducts, self::BATCH);
        $productNumbers = [];

        $progressBar = new ProgressBar($output, count($importProducts));
        $progressBar->start();
        foreach ($csvBatchMainProducts as $products) {
            $productNumbers[] = $this->processImportProducts($products, $progressBar);
        }
        $progressBar->finish();
        //Read and insert varient products
        $varientProducts = array_filter($importProducts, function ($product) {
            return $this->isChildProduct($product['agrzusid']);
        });

        $csvBatchVarientProducts = array_chunk($varientProducts, self::BATCH);
        $productNumbers = [];

        $progressBar = new ProgressBar($output, count($importProducts));
        $progressBar->start();
        foreach ($csvBatchVarientProducts as $products) {
            $productNumbers[] = $this->processImportProducts($products, $progressBar);
        }
        $progressBar->finish();

        $output->writeln(' ');
        $output->writeln('<info>Product import successful</info>');

        return 0;
    }

    /**
     * @param $productsData
     * @param $progressBar
     * @return array
     */
    private function processImportProducts($productsData, $progressBar)
    {
        $products = [];
        $productNumbers = [];

        foreach ($productsData as $product) {
            if ($product['artnr']) {
                if ($this->isChildProduct($product['agrzusid'])) {
                    /* Check if parent product exist */
                    $parentProductData = $this->getParentProduct($product['prdnr']);
                    if (!$parentProductData) {
                        continue;
                    }
                }
                $products[$product['artnr']] = $this->importProducts($product, $products, $progressBar);

                $productNumbers[] = $product['artnr'];
            }
        }
        try {
            $this->cleanProductProperties($products, $this->context);
        } catch (WriteException $exception) {
            $this->logger->info(' ');
            $this->logger->info('<error>Products could not be imported. Message: ' . $exception->getMessage() . '</error>');
        }
        unset($products);

        return $productNumbers;
    }

    /**
     * @param $product
     * @param $progressBar
     * @return array
     */
    private function importProducts($product, $products, $progressBar)
    {
        if ($progressBar) {
            $progressBar->advance();
        }

        $productNumber = (string)$product['artnr'];

        $productId = Uuid::randomHex();

        $existingProduct = $this->importHelper->getProductByProductNumber($productNumber, $this->context);
        $coverImageExist = "";
        $existingMedia = "";
        if ($existingProduct) {
            $productId = $existingProduct->getId();
            /* If already exist product check for media and cover image */
            $existingMedia = array_column((array) $existingProduct->getMedia()->getElements(), 'mediaId');
            if ($existingProduct->getCover()) {
                $coverImageExist = $existingProduct->getCover()->getMediaId();
            }
        }

        $productSalesChannels = $this->getProductSalesChannels($productId);
        $productPropertiesKeys = $this->filterProperties($product, [
            'dim1',
            'dim2',
            'dim3',
            'dim4',
            'dim5',
            'dim6',
            'dim7'
        ]);

        $productPropertyValues = $this->filterProperties($product, [
            'wert1',
            'wert2',
            'wert3',
            'wert4',
            'wert5',
            'wert6',
            'wert7'
        ]);
        $productProperties = null;
        if ($this->isChildProduct($product['agrzusid'])) {
            $productProperties = array_combine(array_values($productPropertiesKeys), array_values($productPropertyValues));
            $productProperties = array_merge($productProperties, [
                "Dimension text" => ['de' => $product['dim_de'], 'fr' => $product['dim_fr']],
                "Designation 1" => ['de' => $product['typ_de'], 'fr' => $product['typ_fr']]
            ]);
            $productProperties = $this->setProductProperties($productProperties);
        }

        $imagePath = $this->shopwareProjectFilesImportDir . "product_images";
        $coverId = $this->getCoverId($coverImageExist, $imagePath, $product);
        $mediaIds = $this->getMediaIds($existingMedia, $imagePath, $product);
        unset($existingMedia, $coverImageExist, $imagePath, $existingProduct);

        $productNames = [];
        $productDescriptions = [];
        if (!$this->isChildProduct($product['agrzusid'])) {
            $productDescriptions['de-DE'] = $product['beschr_de'];
            $productDescriptions['fr-CH'] = $product['beschr_fr'];
            $productDescriptions['en-GB'] = $product['beschr_de'];

            $productNames['de-DE'] = $product['prdname_de'];
            $productNames['fr-CH'] = $product['prdname_fr'];
            $productNames['en-GB'] = $product['prdname_de'];
        } else {
            $productNames['de-DE'] = $product['prdname_de'] . " " . $product['typ_de'];
            $productNames['fr-CH'] = $product['prdname_fr'] . " " . $product['typ_fr'];
            $productNames['en-GB'] = $product['prdname_de'] . " " . $product['typ_de'];
        }

        $priceChfNet = !empty($product['preis']) ? $product['preis'] : 0;

        $productData = [
            'id' => $productId,
            'productNumber' => $productNumber,
            'price' => [
                [
                    'currencyId' => $this->currencyCHFId,
                    'gross' => $this->convertToDecimal($priceChfNet),
                    'net' => $this->convertToDecimal($priceChfNet),
                    'linked' => true
                ],
                [
                    'currencyId' => $this->currencyEURId,
                    'gross' => $this->convertToDecimal($priceChfNet),
                    'net' => $this->convertToDecimal($priceChfNet),
                    'linked' => true
                ]
            ],
            'stock' => 99999,
            'taxId' => $this->taxId,
            'unitId' => $this->getUnitId($product['peh']),
            'name' => $productNames,
            'description' => $productDescriptions,
            'active' =>  true,
        ];

        if ($coverId) {
            $productData['cover'] = [
                'mediaId' => $coverId
            ];
        }

        if (!empty($mediaIds)) {
            $productData['media'] = $mediaIds;
        }

        if ($productProperties) {
            $productData['options'] = $this->setProductOptions($productProperties);
        }

        if ($this->isChildProduct($product['agrzusid'])) {
            $productData['purchaseUnit'] = is_numeric($product['preisab']) ? intval($product['preisab']) : 1;
            $productData['referenceUnit'] = $product['pmenge'] ? intval($product['pmenge']) : 1;
            $productData['minPurchase'] = is_numeric($product['packung']) ? intval($product['packung']) : 1;
            $productData['purchaseSteps'] = $productData['minPurchase'];
            $parentProductData = $this->getParentProduct($product['prdnr']);

            if ($parentProductData) {
                $productData['parentId'] = $parentProductData['parentId'];

                $this->setProductConfigurations($productData, $productProperties);
            }
        }

        if ($productSalesChannels) {
            $productData['visibilities'] = $productSalesChannels;
        }

        if ($productProperties) {
            $productData['properties'] = $productProperties;
        }
        return $productData;
    }

    /**
     * Load products from import xml file and return associative array with products as result
     *
     * @return array
     */
    private function loadProducts()
    {
        return $this->readCSV($this->shopwareProjectFilesImportDir . self::IMPORT_FILENAME);
    }

    private function cleanProductProperties($products, $context)
    {
        $productIds = array_values(array_map(static function ($product) {
            return $product['id'];
        }, $products));

        $productProperties = $this->productPropertyRepository->searchIds(
            (new Criteria())->addFilter(new EqualsAnyFilter('productId', $productIds)),
            $context
        );
        $productRelationsToDelete = [];
        $productPropertiesIds = $productProperties->getIds() ?? [];
        foreach ($productPropertiesIds as $productProperty) {
            $productRelationsToDelete[] = [
                'productId' => $productProperty['product_id'] ?? $productProperty['productId'],
                'optionId' => $productProperty['property_group_option_id'] ?? $productProperty['optionId']
            ];
        }

        $this->productPropertyRepository->delete($productRelationsToDelete, $context);

        unset($productIds, $productProperties, $productRelationsToDelete);
    }

    /**
     * @param string $productId
     * @return array
     */
    private function getProductSalesChannels(string $productId): array
    {
        $productSalesChannels = [];
        $salesChannelNames = ['tocafix.ch'];

        foreach ($salesChannelNames as $salesChannel) {
            $productSalesChannelId = $this->getSalesChannelIdByName($salesChannel);
            if ($productSalesChannelId) {
                $productVisibilityId = $this->importHelper->getProductVisibilityId($productId, $productSalesChannelId, $this->context);

                $productSalesChannels[] = [
                    'id' => $productVisibilityId,
                    'salesChannelId' => $productSalesChannelId,
                    'visibility' => ProductVisibilityDefinition::VISIBILITY_ALL
                ];
            }
        }

        return $productSalesChannels;
    }

    /**
     * Get sales channel id by name
     *
     * @param string $name
     * @return null
     */
    private function getSalesChannelIdByName(string $name)
    {
        $salesChannelCriteria = new Criteria();
        $salesChannelCriteria->addFilter(new EqualsFilter('translations.name', $name));

        $salesChannelSearch = $this->salesChannelRepository->search(
            $salesChannelCriteria,
            $this->context
        );

        /** @var SalesChannelEntity */
        $salesChannel = $salesChannelSearch->getEntities()->first();

        if ($salesChannel) {
            return $salesChannel->getId();
        }

        return null;
    }

    /**
     * Set tax id class variable
     */
    private function setTaxId()
    {
        $taxSearch = $this->taxRepository->search(
            (new Criteria())->addFilter(new EqualsFilter('name', self::TAX_NAME)),
            $this->context
        );

        /** @var TaxEntity */
        $tax = $taxSearch->getEntities()->first();

        if ($tax) {
            $this->taxId = $tax->getId();
        }
    }

    /**
     * Get product variation ids
     *
     * @param string $productNumber
     * @return string
     */
    private function getParentProduct(string $productNumber)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter("productNumber", $productNumber));

        /** @var ProductEntity */
        $parentProductId = $this->productRepository->searchIds($criteria, $this->context)->firstId();

        if ($parentProductId) {
            return [
                "parentId" => $parentProductId
            ];
        }

        return null;
    }

    /**
     * @param array $product
     * @param array $systemProperties
     * @return array
     */
    private function filterProperties(array $product, array $systemProperties): array
    {
        $productPropertyKeys = array_filter(array_keys($product), function ($key) use ($systemProperties) {
            return in_array($key, $systemProperties);
        });

        return array_filter($product, function ($key) use ($productPropertyKeys) {
            return in_array($key, $productPropertyKeys);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param array $productProperties
     * @return array
     */
    private function setProductOptions($productProperties): array
    {
        $options = [];
        foreach ($productProperties as $option) {
            if ($option['configuratable'] === true) {
                $options[] = [
                    'id' => $option['id'],
                ];
            }
            $option = null;
        }
        unset($productProperties);

        return $options;
    }

    /**
     * Search for property and option in properties array
     *
     * @param array $productProperties
     * @return array
     */
    private function setProductProperties(array $productProperties): array
    {
        $renderedProductPropertyGroups = [];
        $renderedProductProperties = [];

        // iterate over $productProperties array and search for existing property groups and options
        foreach ($productProperties as $propertyName => $propertyValue) {
            if ($propertyName && $propertyValue) {
                if ($propertyName === "Dimension text" && !$propertyValue['de']) {
                    continue;
                }
                if ($propertyName === "Designation 1" && !$propertyValue['de']) {
                    continue;
                }
                $existingPropertyGroup = $this->importHelper->getExistingPropertyGroup((string) $propertyName, $this->context);

                $propertyGroupId = Uuid::randomHex();
                $propertyOptionId = Uuid::randomHex();

                if ($existingPropertyGroup) {
                    $existingPropertyGroupId = $existingPropertyGroup->getId();

                    $propertyOptionId = $this->importHelper->getPropertyOptionId($existingPropertyGroupId, in_array($propertyName, ["Dimension text", "Designation 1"]) ? $propertyValue['de'] : $propertyValue, $this->context);

                    $propertyGroupId = $existingPropertyGroupId;
                }

                $renderedProductPropertyGroups[] = [
                    'id' => $propertyGroupId,
                    'name' => (string) $propertyName,
                    'displayType' => 'text',
                    'sortingType' => 'alphanumeric',
                    'filterable' => $propertyName === "Dimension text" ? true : false,
                    'comparable' => true,
                    'position' => 1,
                    'active' => true,
                    'options' => [
                        [
                            'id' => $propertyOptionId,
                            'groupId' => $propertyGroupId,
                            'name' => in_array($propertyName, ["Dimension text", "Designation 1"]) ? [
                                'de-DE' => $propertyValue['de'],
                                'en-GB' => $propertyValue['de'],
                                'fr-CH' => $propertyValue['fr']
                            ] : (string) $propertyValue,
                        ]
                    ]
                ];

                $renderedProductProperties[] = [
                    'id' => $propertyOptionId,
                    'groupId' => $propertyGroupId,
                    'name' => $propertyValue,
                    'configuratable' => in_array($propertyName, ["Dimension text", "Designation 1"]) ? true : false
                ];
            }

            $propertyName = null;
            $propertyValue = null;
        }

        $this->propertyGroupRepository->upsert($renderedProductPropertyGroups, $this->context);

        unset($productProperties);

        return $renderedProductProperties;
    }

    /**
     * @param array $productData
     * @param array $productProperties
     */
    private function setProductConfigurations(array $productData, array $productProperties)
    {
        $configuratorSettings = [];
        foreach ($productProperties as $option) {
            if ($option['configuratable'] === true) {
                $configuratorSetting = [
                    'optionId' => $option['id'],
                    'productId' => $productData['parentId'],
                ];

                $id = $this->importHelper->getConfiguratorSettingId($productData['parentId'], $option['id'], $this->context);
                // if the configurator setting already exists, update or skip
                if ($id) {
                    $configuratorSetting['id'] = $id;
                }
                $configuratorSettings[] = $configuratorSetting;
                $configuratorSetting = null;
            }

            $option = null;
        }

        unset($productData, $productProperties);

        $this->productConfiguratorSettingRepository->upsert($configuratorSettings, $this->context);
    }

    private function getCurrencyId(string $currency)
    {
        $criteria = (new Criteria())->addFilter(new EqualsFilter('isoCode', $currency));

        return $this->currencyRepository->searchIds($criteria, $this->context)->firstId();
    }

    /**
     * @param string $filePath
     * @return array
     */
    public function readCSV(string $filePath): array
    {
        $orderArray = [];
        $filePath = trim($filePath);

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

    private function convertToDecimal($value)
    {
        $value = (string)$value;
        $value = str_replace(',', '.', $value);

        return floatval($value);
    }

    /**
     * get unit id
     */
    private function getUnitId($unitName)
    {
        if ($unitName === "Stk") {
            $unitName = "Stk.";
        }
        $criteria = (new Criteria())->addFilter(new EqualsFilter('shortCode', $unitName));

        return $this->unitRepository->searchIds($criteria, $this->context)->firstId();
    }

    private function isChildProduct(string $productType)
    {
        if (strip_tags($productType) !== "PARENT") {
            return true;
        }

        return false;
    }

    private function getCoverID($coverImageExist, $imagePath, $product)
    {
        $coverId = '';

        if (!$this->isChildProduct($product['agrzusid'])) {
            $coverImageName = $product['image1'];

            if ($coverImageName) {
                if (file_exists($imagePath . "/" . $coverImageName)) {
                    $imageId = $this->imageImportService->addImageToMediaFromFile($coverImageName, $imagePath, $this->context);

                    if ($coverImageExist != $imageId) {
                        $coverId = $imageId;
                    }
                }
            }
        }

        unset($coverImageExist, $product, $imagePath);

        return $coverId;
    }

    private function getMediaIds($existingMedia, $imagePath, $product)
    {
        $mediaIds = [];

        if (!$this->isChildProduct($product['agrzusid'])) {
            for ($i = 2; $i <= 8; $i++) {
                if (isset($product["image" . $i])) {
                    $imageName = $product["image" . $i];
                    if ($imageName && file_exists($imagePath . "/" . $imageName)) {
                        $mediaId = $this->imageImportService->addImageToMediaFromFile($imageName, $imagePath, $this->context);
                        if ($mediaId) {
                            $mediaIds[] = $mediaId;
                        }
                    }
                }
            }

            if (!empty($mediaIds) && !empty($existingMedia)) {
                $mediaIds = array_filter($mediaIds, function ($mediaId) use ($existingMedia) {
                    return !in_array($mediaId, $existingMedia);
                });
            }
        }

        unset($existingMedia, $product, $imagePath);

        return array_map(function ($mediaId) {
            return  ["mediaId" => $mediaId];
        }, $mediaIds);
    }
}
