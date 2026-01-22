<?php declare(strict_types=1);

namespace TocafixCustomPlugin\Core\Checkout\Cart\Error;

use Shopware\Core\Checkout\Cart\Error\Error;

class CustomCommissionBlockedError extends Error
{
    private const KEY = 'custom-commission-blocked';

    private string $lineItemId;

    public function __construct(string $lineItemId)
    {
        $this->lineItemId = $lineItemId;
        parent::__construct();
    }

    public function getId(): string
    {
        return $this->lineItemId;
    }

    public function getMessageKey(): string
    {
        return self::KEY;
    }

    public function getLevel(): int
    {
//        return self::LEVEL_NOTICE;
//        return self::LEVEL_WARNING;
        return self::LEVEL_ERROR;
    }

    public function blockOrder(): bool
    {
        return false;
    }

    public function getParameters(): array
    {
        return [ 'id' => $this->lineItemId ];
    }
}