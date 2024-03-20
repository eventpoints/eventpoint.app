<?php

declare(strict_types=1);

namespace App\Controller\Controller;

use App\Entity\User\User;
use App\Form\Filter\RegionFilterType;
use App\Model\RegionalConfiguration;
use App\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/region')]
class RegionalConfigurationController extends AbstractController
{
    public function __construct(
        private readonly RegionalConfiguration $regionalSetting,
        private readonly UserRepository $userRepository,
    ) {
    }

    #[Route(path: '/', name: '_app_regional_settings', methods: ['GET', 'POST'])]
    public function index(Request $request, #[CurrentUser] null|User $user): Response
    {
        $session = $request->getSession();
        $regionForm = $this->createForm(RegionFilterType::class, $this->regionalSetting, [
            'request' => $request,
        ]);
        $regionForm->handleRequest($request);
        if ($regionForm->isSubmitted() && $regionForm->isValid()) {
            if ($user instanceof User) {
                $user->setLocale($regionForm->get('locale')->getData());
                $user->setCurrency($regionForm->get('currency')->getData());
                $user->setCountry($regionForm->get('region')->getData());
                $user->setTimezone($regionForm->get('timezone')->getData());
                $this->userRepository->save(entity: $user, flush: true);
            }

            $this->regionalSetting->setLocale($regionForm->get('locale')->getData());
            $this->regionalSetting->setCurrency($regionForm->get('currency')->getData());
            $this->regionalSetting->setRegion($regionForm->get('region')->getData());
            $this->regionalSetting->setTimezone($regionForm->get('timezone')->getData());

            $session->set('_locale', $regionForm->get('locale')->getData());
            $session->set('_currency', $regionForm->get('currency')->getData());
            $session->set('_region', $regionForm->get('region')->getData());
            $session->set('_timezone', $regionForm->get('timezone')->getData());

            return $this->redirectToRoute('events');
        }

        return $this->render('regional-settings/form.html.twig', [
            'regionForm' => $regionForm->createView(),
        ]);
    }
}
