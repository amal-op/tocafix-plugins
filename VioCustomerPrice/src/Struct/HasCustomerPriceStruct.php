<?php
declare(strict_types=1);

namespace VioCustomerPrice\Struct;

use Shopware\Core\Framework\Struct\Struct;

class HasCustomerPriceStruct extends Struct
{
    public function __construct(
        private readonly bool $hasCustomerPrice
    ) {
    }

    public function getHasCustomerPrice(): bool
    {
        return $this->hasCustomerPrice;
    }
}
