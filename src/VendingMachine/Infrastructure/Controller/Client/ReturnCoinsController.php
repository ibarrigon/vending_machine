<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Controller\Client;

use App\VendingMachine\Application\Client\ReturnCoins\ReturnCoinsCommand;
use App\VendingMachine\Application\Client\ReturnCoins\ReturnCoinsUseCase;
use App\VendingMachine\Infrastructure\Transformer\OutputCentsTransformer;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Return coins')]
final class ReturnCoinsController extends AbstractController
{
    public function __construct(
        private ReturnCoinsUseCase $returnCoins,
        private OutputCentsTransformer $transformer,
    ) {
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
        try {
            $coins = $this->returnCoins->execute(new ReturnCoinsCommand($id));

            return new JsonResponse(['coins' => $this->transformer->transformList($coins)], Response::HTTP_OK);
        } catch (\Throwable $e) {
            return new JsonResponse(
                ['status' => 'ko', 'error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
