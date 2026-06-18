<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Controller;

use App\VendingMachine\Application\Client\InsertCoin\InsertCoinCommand;
use App\VendingMachine\Application\Client\InsertCoin\InsertCoinUseCase;
use App\VendingMachine\Application\Client\ReturnCoins\ReturnCoinsCommand;
use App\VendingMachine\Application\Client\ReturnCoins\ReturnCoinsUseCase;
use App\VendingMachine\Application\Client\SelectProduct\SelectProductCommand;
use App\VendingMachine\Application\Client\SelectProduct\SelectProductUseCase;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Vending Machine')]
final class VendingMachineController extends AbstractController
{
    public function __construct(
        private InsertCoinUseCase $insertCoin,
        private SelectProductUseCase $selectProduct,
        private ReturnCoinsUseCase $returnCoins,
    ) {}

    #[Route('/machine/{id}/coin', methods: ['POST'])]
    #[OA\Post(
        summary: 'Insert coin',
        description: 'Insert a valid coin into the vending machine',
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
            required: ['coin'],
            properties: [
                new OA\Property(
                    property: 'coin',
                    ref: '#/components/schemas/InsertCoinRequest',
                ),
            ],
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Coin inserted successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'status',
                    type: 'string',
                    example: 'ok',
                ),
            ],
        ),
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid coin',
    )]
    public function insertCoin(
        int $id,
        #[MapRequestPayload]
        InsertCoinRequest $request
    ): JsonResponse {
        $this->insertCoin->execute(
            new InsertCoinCommand(
                machineId: $id,
                coin: $request->coin,
            ),
        );

        return new JsonResponse(['status' => Request::HTTP_OK]);
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
        SelectProductRequest $request
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
        ]);
    }

    #[Route('/machine/{id}/return', methods: ['POST'])]
    #[OA\Post(
        summary: 'Return inserted coins',
        description: 'Returns all inserted coins back to user',
    )]
    #[OA\Tag(name: 'Vending Machine')]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer'),
        example: 1,
    )]
    #[OA\Response(
        response: 200,
        description: 'Coins returned successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'coins',
                    type: 'array',
                    items: new OA\Items(type: 'integer'),
                    example: [100, 25],
                ),
            ],
        )
    )]
    public function returnCoins(int $id): JsonResponse
    {
        $coins = $this->returnCoins->execute(new ReturnCoinsCommand($id));

        return new JsonResponse(['coins' => $coins]);
    }
}
