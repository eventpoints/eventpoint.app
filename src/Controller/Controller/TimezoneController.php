<?php

declare(strict_types=1);

namespace App\Controller\Controller;

use App\Model\BrowserRegionalData;
use App\Model\Timezone;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TimezoneController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route(path: '/set/browser/timezone', name: 'set_browser_timezone', methods: [Request::METHOD_POST])]
    public function timezone(Request $request): JsonResponse
    {
        $session = $request->getSession();
        $rc = $session->get('regional_configuration');
        dump($rc);
        $name = json_decode($request->getContent())->timezone;
        $timezone = new Timezone(name: $name);

        $browserZone = new DateTimeZone($timezone->getName());
        $utcOffsetInMinutes = $browserZone->getOffset(new DateTimeImmutable());
        $location = $browserZone->getLocation();
        $countryCode = $location['country_code'];
        $latitude = (string) $location['latitude'];
        $longitude = (string) $location['longitude'];

        $browserRegionalData = new BrowserRegionalData(timezone: $timezone->getName(), offsetInMinutes: $utcOffsetInMinutes, countryCode: $countryCode, latitude: $latitude, longitude: $longitude);
        $session->set('browser_regional_data', $browserRegionalData);

        return $this->json([], 200);
    }
}
