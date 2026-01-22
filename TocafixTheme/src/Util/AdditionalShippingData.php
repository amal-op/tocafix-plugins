<?php

declare(strict_types=1);

namespace TocafixTheme\Util;

use Shopware\Core\Framework\Struct\Struct;

class AdditionalShippingData extends Struct
{
    /**
     * @var string
     */
    protected $shippingType;

    public function __construct(string $shippingType) {
        $this->shippingType = $shippingType;
    }

    public function getShippingType(): string
    {
        return $this->shippingType;
    }
}