<?php declare(strict_types=1);

namespace TocafixImportPlugin\Core\System\Currency;

use Shopware\Core\Checkout\Document\Service\DocumentGenerator;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\Framework\Feature;
use Shopware\Core\System\Language\LanguageException;
use Shopware\Core\System\Currency\CurrencyFormatter;
use Shopware\Core\System\Locale\LanguageLocaleCodeProvider;

class TocafixCurrencyFormatter extends CurrencyFormatter
{
    /**
     * @var \NumberFormatter[]
     */
    private array $formatter = [];

    private LanguageLocaleCodeProvider $languageLocaleProvider;

    /**
     * @internal
     */
    public function __construct(LanguageLocaleCodeProvider $languageLocaleProvider)
    {
        $this->languageLocaleProvider = $languageLocaleProvider;
    }

    /**
     * @throws InconsistentCriteriaIdsException
     * @throws LanguageException
     */
    public function formatCurrencyByLanguage(float $price, string $currency, string $languageId, Context $context, ?int $decimals = null): string
    {
        if ($currency === "CHF") {
            return number_format($price, 2, ','). " CHF";
        }

        $decimals = $decimals ?? $context->getRounding()->getDecimals();

        $locale = $this->languageLocaleProvider->getLocaleForLanguageId($languageId);
        $formatter = $this->getFormatter($locale, \NumberFormatter::CURRENCY);
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $decimals);

        // if (Feature::isActive('FEATURE_NEXT_15053')) {
        //     return (string) $formatter->formatCurrency($price, $currency);
        // }

        // if (!$context->hasState(DocumentGenerator::GENERATING_PDF_STATE)) {
        //     return (string) $formatter->formatCurrency($price, $currency);
        // }

        $string = htmlentities((string) $formatter->formatCurrency($price, $currency), \ENT_COMPAT, 'utf-8');
        $content = str_replace('&nbsp;', ' ', $string);

        return html_entity_decode($content);
    }

    private function getFormatter(string $locale, int $format): \NumberFormatter
    {
        $hash = md5(json_encode([$locale, $format], \JSON_THROW_ON_ERROR));

        if (isset($this->formatter[$hash])) {
            return $this->formatter[$hash];
        }

        return $this->formatter[$hash] = new \NumberFormatter($locale, $format);
    }
}
