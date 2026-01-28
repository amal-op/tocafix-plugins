<?php declare(strict_types=1);

namespace VioCustomerPrice\Core\Content\Product;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Inherited;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use VioCustomerPrice\Entity\CustomerPriceDefinition;

class ProductExtension extends EntityExtension
{
    public const CUSTOMER_PRICES_EXTENSION_KEY = 'vioCustomerPrices';
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToManyAssociationField(
                'customerPrices',
                CustomerPriceDefinition::class,
                'product_id',
                'id'
            ))->addFlags(new Inherited(), new CascadeDelete())
        );
    }

    public function getEntityName(): string
    {
        return ProductDefinition::ENTITY_NAME;
    }
}
