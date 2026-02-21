<?php

namespace App\Autocomplete\Provider;

use App\Repository\CityRepository;
use Kerrialnewham\Autocomplete\Provider\Contract\AutocompleteProviderInterface;
use Nette\Utils\Strings;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class CityProvider implements AutocompleteProviderInterface
{
    public function __construct(
            private readonly CityRepository      $cityRepository,
            private readonly RequestStack        $requestStack,
            private readonly TranslatorInterface $translator,
    )
    {
    }

    public function search(string $query, int $limit, array $selected): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $locale = $request?->getLocale() ?? 'en';
        $countryId = $request?->query->get('country');

        $qb = $this->cityRepository->createQueryBuilder('c')
                ->leftJoin('c.country', 'country')
                ->orderBy('c.name', 'ASC');

        if ($countryId) {
            $qb->andWhere('country.id = :country')
                    ->setParameter('country', $countryId, 'uuid');
        }

        if (!empty($selected)) {
            $qb->andWhere($qb->expr()->notIn('c.id', ':selected'))
                    ->setParameter('selected', $selected);
        }

        $cities = $qb->getQuery()->getResult();

        $results = [];
        foreach ($cities as $city) {
            $label = $this->translator->trans(
                    'city.' . strtolower($city->getCountry()->getAlpha2()) . '.' . Strings::webalize($city->getName()),
                    [],
                    'cities',
                    $locale,
            );

            // Filter by translated label
            if ($query !== '' && !str_contains(mb_strtolower($label), mb_strtolower($query))) {
                continue;
            }

            $results[] = ['id' => (string) $city->getId(), 'label' => $label];
        }

        usort($results, fn($a, $b) => $a['label'] <=> $b['label']);

        return array_slice($results, 0, $limit);
    }
}