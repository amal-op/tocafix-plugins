<?php declare(strict_types=1);

namespace TocafixCustomPlugin\Setup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\CustomField\CustomFieldTypes;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Installer
{
    /**
     * Installer constructor.
     *
     * @param Connection    $connection
     */
    protected $connection;

    /**
     * @var ContainerInterface
     */

    protected $container;

    /**
     * Constructor
     *
     * @param Connection $connection
     * @param ContainerInterface $container
     */
    public function __construct(
        Connection $connection,
        ContainerInterface $container
    ) {
        $this->connection = $connection;
        $this->container = $container;
    }

    /**
     * Install
     *
     * @return void
     */
    public function install($installContext)
    {
        $this->customFieldsForOrder();

        $this->customFieldsForCommissions();

        $this->customFieldsForProduct();

        $this->customFieldsForCustomerReference();
    }

    /**
     * Fetch custom field
     *
     * @param string  $technicalName
     *  
     */
    private function fetchCustomFieldSetId($technicalName)
    {
        $customFieldSetRepo = $this->container->get('custom_field_set.repository');
        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('custom_field_set.name', $technicalName));
        $result = $customFieldSetRepo->search($criteria, Context::createDefaultContext());
        $customFieldSetDetails = $result->first();

        return ($customFieldSetDetails) ? $customFieldSetDetails->getId() : null;
    }
    private function customFieldsForOrder(): void
    {
        $customFieldSetId = $this->fetchCustomFieldSetId('tocafix_delivery_date_set');
        if (!$customFieldSetId) {
            $repository = $this->container->get('custom_field_set.repository');
            $id = Uuid::randomHex();
            $attributeSet = [
                'id' => $id,
                'name' => 'tocafix_delivery_date_set',
                'config' => ['label' => [
                    'en-GB' => 'Delivery Date Set',
                    'de-DE' => 'Lieferdatum',
                    'fr-FR' => 'Date de livraison'
                ]],
                'customFields' => [
                    [
                        'id' => Uuid::randomHex(),
                        'name' => 'tocafix_delivery_date',
                        'type' => CustomFieldTypes::DATETIME,
                    ]
                ],
                'relations' => [
                    [
                        'entityName' => 'order',
                    ],
                ],
            ];

            $result = $repository->create([$attributeSet], Context::createDefaultContext());
        }
    }

    private function customFieldsForCommissions(): void
    {
        $customFieldSetId = $this->fetchCustomFieldSetId('tocafix_commissions_set');
        if (!$customFieldSetId) {
            $repository = $this->container->get('custom_field_set.repository');
            $id = Uuid::randomHex();
            $attributeSet = [
                'id' => $id,
                'name' => 'tocafix_commissions_set',
                'config' => ['label' => [
                    'en-GB' => 'Commission',
                    'de-DE' => 'Kommission'
                ]],
                'customFields' => [
                    [
                        'id' => Uuid::randomHex(),
                        'name' => 'tocafix_commissions_name',
                        'type' => CustomFieldTypes::TEXT,
                        'config' => [
                            'type' => 'text',
                            'label' => [
                                'en-GB' => 'Commission Name',
                                'de-DE' => 'Kommissionsname',
                            ],
                            'componentName' => 'sw-field',
                            'customFieldType' => 'text',
                            'customFieldPosition' => 3
                        ]
                        ],
                        [
                            'id' => Uuid::randomHex(),
                            'name' => 'tocafix_commissions_number',
                            'type' => CustomFieldTypes::TEXT,
                            'config' => [
                                'type' => 'text',
                                'label' => [
                                    'en-GB' => 'Commission Number',
                                    'de-DE' => 'Kommissionsnummer',
                                ],
                                'componentName' => 'sw-field',
                                'customFieldType' => 'text',
                                'customFieldPosition' => 3
                            ]
                        ]
                ],
                'relations' => [
                    [
                        'entityName' => 'order',
                    ],
                ],
            ];

            $result = $repository->create([$attributeSet], Context::createDefaultContext());
        }
    }

    private function customFieldsForCustomerReference(): void
    {
        $customFieldSetId = $this->fetchCustomFieldSetId('tocafix_customer_reference_set');
        if (!$customFieldSetId) {
            $repository = $this->container->get('custom_field_set.repository');
            $id = Uuid::randomHex();
            $attributeSet = [
                'id' => $id,
                'name' => 'tocafix_customer_reference_set',
                'config' => ['label' => [
                    'en-GB' => 'Customer Reference Set',
                    'de-DE' => 'Kundenreferenz'
                ]],
                'customFields' => [
                    [
                        'id' => Uuid::randomHex(),
                        'name' => 'tocafix_customer_reference',
                        'type' => CustomFieldTypes::TEXT,
                        'config' => [
                            'type' => 'text',
                            'label' => [
                                'en-GB' => 'Customer Reference',
                                'de-DE' => 'Kundenreferenz',
                            ],
                            'componentName' => 'sw-field',
                            'customFieldType' => 'text',
                            'customFieldPosition' => 3
                        ]
                    ],
                ],
                'relations' => [
                    [
                        'entityName' => 'order',
                    ],
                ],
            ];

            $result = $repository->create([$attributeSet], Context::createDefaultContext());
        }
    }

    private function customFieldsForProduct(): void
    {
        $customFieldSetId = $this->fetchCustomFieldSetId('tocafix_product_set');
        if (!$customFieldSetId) {
            $repository = $this->container->get('custom_field_set.repository');
            $id = Uuid::randomHex();
            $attributeSet = [
                'id' => $id,
                'name' => 'tocafix_product_set',
                'config' => ['label' => [
                    'en-GB' => 'Product Set',
                    'de-DE' => 'Produktset',
                    'fr-FR' => 'Ensemble de produits'
                ]],
                'customFields' => [
                    [
                        'id' => Uuid::randomHex(),
                        'name' => 'tocafix_dimension_text',
                        'type' => CustomFieldTypes::HTML,
                        'config' => [
                            'componentName' => 'sw-text-editor',
                            'customFieldType' => 'textEditor',
                            'label' => [
                                'en-GB' => 'Dimension Text',
                                'de-DE' => 'Varianten Beschreibung'
                            ],
                            'customFieldPosition' => 1
                        ]
                    ]
                ],
                'relations' => [
                    [
                        'entityName' => 'product',
                    ],
                ],
            ];

            $result = $repository->create([$attributeSet], Context::createDefaultContext());
        }
    }
}