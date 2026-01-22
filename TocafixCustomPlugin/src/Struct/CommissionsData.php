<?php declare(strict_types=1);

namespace TocafixCustomPlugin\Struct;

use Shopware\Core\Framework\Struct\Struct;

class CommissionsData extends Struct
{
    /**
     * @var string
     */
    protected $commissionNumber;

    /**
     * @var string
     */
    protected $commissionName;

    public function __construct(
        $commissionNumber,
        $commissionName
    ) {
        $this->commissionNumber = $commissionNumber;
        $this->commissionName = $commissionName;
    }

    public function getCommissionNumber(): ?string
    {
        return $this->commissionNumber;
    }

    public function getCommissionName(): ?string
    {
        return $this->commissionName;
    }

    public function getApiAlias(): string
    {
        return 'commission_data';
    }
}