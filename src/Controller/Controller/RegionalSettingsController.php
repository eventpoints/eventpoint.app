<?php

declare(strict_types=1);

namespace App\Controller\Controller;

use App\Entity\User;
use App\Form\Filter\RegionFilterType;
use App\Repository\UserRepository;
use App\Service\RegionalSettingsService\RegionalSettingsService;
use App\ValueObject\RegionalSettingValueObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/region')]
class RegionalSettingsController extends AbstractController
{
    public function __construct(
        private readonly RegionalSettingsService $regionalSettingsService,
        private readonly UserRepository          $userRepository,
    ) {
    }

    #[Route(path: '/', name: '_app_regional_settings', methods: ['GET', 'POST'])]
    public function index(Request $request, #[CurrentUser] null|User $user): Response
    {
        $regionalSetting = new RegionalSettingValueObject();
        $regionForm = $this->createForm(RegionFilterType::class, $regionalSetting, [
            'request' => $request,
        ]);
        $regionForm->handleRequest($request);
        if ($regionForm->isSubmitted() && $regionForm->isValid()) {
            if ($user instanceof User) {
                $user->setLocale($regionalSetting->getLocale());
                $user->setCurrency($regionalSetting->getCurrency());
                $user->setCountry($regionalSetting->getRegion());
                $user->setTimezone($regionalSetting->getTimezone());
                $this->userRepository->save(entity: $user, flush: true);
            }
            $this->regionalSettingsService->configure($regionalSetting);
            return $this->redirectToRoute('events');
        }

        return $this->render('regional-settings/form.html.twig', [
            'regionForm' => $regionForm->createView(),
        ]);
    }
}
