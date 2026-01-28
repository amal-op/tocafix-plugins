<?php
/** @noinspection PhpMissingFieldTypeInspection */
declare(strict_types=1);

namespace VioCustomerPrice\Administration\Controllers;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use VioCustomerPrice\Services\ImportService;

#[Route(defaults: ['_routeScope' => ['api']])]
class VioCustomerPriceImportController extends AbstractController
{
    public function __construct(
        private readonly ImportService $importService,
        private readonly EntityRepository $customerPricesRepository
    ) {
    }

    #[Route(path: '/api/_action/vio_customer_price/import', name: 'api.action.vio_customer_price.import', methods: ['POST'])]
    public function importFile(Request $request, Context $context): Response
    {
        $count = 0;
        /** @var File $file */
        foreach ($request->files as $file) {
            $count += $this->importService->import($file, $context);
            if (!unlink($file->getRealPath())) {
                throw new \RuntimeException(\sprintf('couldn\'t delete file "%d", after import', $file->getRealPath()));
            }
        }

        return new JsonResponse([
            'count' => $count,
        ]);
    }

    /**
     * delete all customer prices from a custom
     */
    #[Route(path: '/api/_action/vio_customer_price/customer/{customerId}', name: 'api.action.vio_customer_price.customer.delete', requirements: ['customerId' => '\w{32}'], methods: ['DELETE'])]
    public function deleteFile(string $customerId, Context $context): Response
    {
        $priceIds = $this->customerPricesRepository->searchIds(
            (new Criteria())
            ->addFilter(new EqualsFilter('customerId', $customerId)),
            $context
        )->getIds();
        if (!empty($priceIds)) {
            $this->customerPricesRepository->delete(
                array_map(static fn (string $id) => ['id' => $id], $priceIds),
                $context
            );
        }

        return new JsonResponse([
            'count' => is_countable($priceIds) ? \count($priceIds) : 0,
        ]);
    }
}
