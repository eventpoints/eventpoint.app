<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Service\NominatimService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class CityBoundaryController extends AbstractController
{
    #[Route(path: '/api/city/{id}/boundary', name: 'api_city_boundary')]
    public function __invoke(City $city, NominatimService $nominatimService): JsonResponse
    {
        $boundary = $nominatimService->getCityBoundary(
            $city->getName(),
            $city->getCountry()->getName(),
        );

        if ($boundary === null) {
            return $this->json([
                'error' => 'Boundary not found',
            ], 404);
        }

        return $this->json($boundary);
    }
}
