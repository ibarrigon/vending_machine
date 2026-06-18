<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Controller\Client;

use App\VendingMachine\Application\Client\InsertCoin\InsertCoinCommand;
use App\VendingMachine\Application\Client\InsertCoin\InsertCoinUseCase;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Insert coin')]
final class InsertCoinController extends AbstractController
{
    public function __construct(
        private InsertCoinUseCase $insertCoin,
    ) {
    }

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
        InsertCoinRequest $request,
    ): JsonResponse {
        $this->insertCoin->execute(
            new InsertCoinCommand(
                machineId: $id,
                coin: $request->coin,
            ),
        );

        return new JsonResponse(['status' => 'ok'], Response::HTTP_OK);
    }
}
