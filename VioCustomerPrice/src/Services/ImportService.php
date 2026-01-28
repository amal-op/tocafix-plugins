<?php
/** @noinspection PhpMissingFieldTypeInspection */
declare(strict_types=1);

namespace VioCustomerPrice\Services;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

readonly class ImportService
{
    public function __construct(
        private EntityRepository $customerPricesRepository,
        private EntityRepository $productRepository,
        private EntityRepository $customerRepository
    ) {
    }

    public function import(File $file, Context $context): int
    {
        $changedCount = 0;
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        $data = $serializer->decode(file_get_contents($file->getRealPath()), 'csv', [CsvEncoder::DELIMITER_KEY => ';', CsvEncoder::AS_COLLECTION_KEY => true]);
        $productNumberMap = [];
        $saveStepWidth = 100;
        $i = 0;
        $upsertData = [];
        foreach ($data as $row) { // @phpstan-ignore-line
            if (!\is_array($row)) {
                continue;
            }
            ++$i;
            $productId = null;
            $customerIds = [];
            $price = null;
            $discount = null;
            $quantityStart = 0;
            if (\array_key_exists('productNumber', $row)) {
                if (\array_key_exists($row['productNumber'], $productNumberMap)) {
                    $productId = $productNumberMap[$row['productNumber']];
                }
                if ($productId === null) {
                    $productId = $this->productRepository
                        ->searchIds(
                            (new Criteria())->addFilter(new EqualsFilter('productNumber', $row['productNumber'])),
                            $context
                        )
                        ->firstId();
                    $productNumberMap[$row['productNumber']] = $productId;
                }
            }
            if (\array_key_exists('customerNumber', $row)) {
                $customerIds = $this->customerRepository
                    ->searchIds(
                        (new Criteria())->addFilter(new EqualsFilter('customerNumber', $row['customerNumber'])),
                        $context
                    )
                    ->getIds();
            }
            if (\array_key_exists('quantityStart', $row)) {
                $quantityStart = (int) $row['quantityStart'];
            }
            if (\array_key_exists('price', $row) && $row['price'] !== null && $row['price'] !== '') {
                $price = (float) $row['price'];
            }
            if (\array_key_exists('discount', $row) && $row['discount'] !== null && $row['discount'] !== '') {
                $discount = (float) $row['discount'];
            }
            // skip row
            if ($productId === null || empty($customerIds) || ($price === null && $discount === null)) {
                continue;
            }

            foreach ($customerIds as $customerId) { // @phpstan-ignore-line
                $priceData = [
                    'productId' => $productId,
                    'customerId' => $customerId,
                    'price' => $price,
                    'discount' => $discount,
                    'quantityStart' => $quantityStart,
                ];
                if (\array_key_exists('quantityEnd', $row) && $row['quantityEnd'] !== null && $row['quantityEnd'] !== '') {
                    $priceData['quantityEnd'] = (int) $row['quantityEnd'];
                } else {
                    $priceData['quantityEnd'] = null;
                }

                // check if price already exists
                $priceId = $this->customerPricesRepository->searchIds(
                    (new Criteria())
                        ->addFilter(
                            new EqualsFilter('productId', $productId),
                            new EqualsFilter('customerId', $customerId),
                            new EqualsFilter('quantityStart', $quantityStart)
                        ),
                    $context
                )->firstId();
                if ($priceId !== null) {
                    $priceData['id'] = $priceId;
                }
                $upsertData[] = $priceData;
            }

            // only save data every x times, to reduce overhead
            if ($i % $saveStepWidth === 0) {
                $this->customerPricesRepository->upsert($upsertData, $context);
                $changedCount += \count($upsertData);
                $upsertData = [];
            }
        }
        // save the last data
        if (!empty($upsertData)) {
            $this->customerPricesRepository->upsert($upsertData, $context);
            $changedCount += \count($upsertData);
        }

        return $changedCount;
    }
}
