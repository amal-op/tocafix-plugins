<?php declare(strict_types=1);

namespace TocafixCustomPlugin\Struct;

use Shopware\Core\Framework\Struct\Struct;

class DeliveryDateDate extends Struct
{
    /**
     * @var string
     */
    protected $deliveryDate;

    public function __construct(
        $deliveryDate
    ) {
        $this->deliveryDate = $deliveryDate;
    }

    public function getDeliveryDate(): ?string
    {
        return $this->deliveryDate;
    }

    public function getApiAlias(): string
    {
        return 'delivery_date_data';
    }
}