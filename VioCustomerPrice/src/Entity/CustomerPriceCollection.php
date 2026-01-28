<?php declare(strict_types=1);

namespace VioCustomerPrice\Entity;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<CustomerPriceEntity>
 */
class CustomerPriceCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return CustomerPriceEntity::class;
    }
}
