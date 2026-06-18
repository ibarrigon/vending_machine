<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Controller\Client;

use App\VendingMachine\Application\Client\SelectProduct\SelectProductCommand;
use App\VendingMachine\Application\Client\SelectProduct\SelectProductUseCase;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Select product')]
final class SelectProductController extends AbstractController
{
    public function __construct(
        private SelectProductUseCase $selectProduct,
    ) {
    }

    #[Route('/machine/{id}/select', methods: ['POST'])]
    #[OA\Post(
        summary: 'Select product',
        description: 'Select a product and receive it with change if needed',
    )]
    #[OA\Tag(name: 'Vending Machine')]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer'),
        example: 1,
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['product'],
            properties: [
                new OA\Property(
                    property: 'product',
                    ref: '#/components/schemas/SelectProductRequest',
                ),
            ],
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Product dispensed successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'product',
                    type: 'string',
                    example: 'WATER',
                ),
                new OA\Property(
                    property: 'change',
                    type: 'array',
                    items: new OA\Items(type: 'integer'),
                    example: [25, 10],
                ),
            ],
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Insufficient funds',
    )]
    #[OA\Response(
        response: 409,
        description: 'Product out of stock',
    )]
    public function select(
        int $id,
        #[MapRequestPayload]
        SelectProductRequest $request,
    ): JsonResponse {
        $result = $this->selectProduct->execute(
            new SelectProductCommand(
                machineId: $id,
                product: $request->product,
            )
        );

        return new JsonResponse([
            'product' => $result->product,
            'change' => $result->change,
            'remain_cash' => 0,
        ], Response::HTTP_OK);
    }
}
