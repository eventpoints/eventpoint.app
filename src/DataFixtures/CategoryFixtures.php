<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\CategoryFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function __construct(
        private readonly CategoryFactory $categoryFactory
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getAllCategories() as $categoryData) {
            $category = $this->categoryFactory->create(title: $categoryData['title']);
            foreach ($categoryData['subcategories'] as $subcategoryData) {
                $subcategory = $this->categoryFactory->create(title: $subcategoryData['title'], parent: $category);
                $category->addSubcategory($subcategory);
            }
            $manager->persist($category);
        }
        $manager->flush();
    }

    /**
     * @return array<string, mixed>[]
     */
    public function getAllCategories(): array
    {
        return [
            [
                'title' => 'category.live-performance',
                'subcategories' => [
                    [
                        'title' => 'category.music-concerts',
                    ],
                    [
                        'title' => 'category.theater-shows',
                    ],
                    [
                        'title' => 'category.dance-shows',
                    ],
                    [
                        'title' => 'category.comedy-shows',
                    ],
                    [
                        'title' => 'category.magic-shows',
                    ],
                    [
                        'title' => 'category.comedy',
                    ],
                    [
                        'title' => 'category.circus',
                    ],
                ],
            ],
            [
                'title' => 'category.celebration',
                'subcategories' => [
                    [
                        'title' => 'category.birthday-party',
                    ],
                    [
                        'title' => 'category.anniversary',
                    ],
                    [
                        'title' => 'category.wedding',
                    ],
                ],
            ],
            [
                'title' => 'category.educational',
                'subcategories' => [
                    [
                        'title' => 'category.educational.presentation',
                    ],
                    [
                        'title' => 'category.educational.coding-bootcamps',
                    ],
                    [
                        'title' => 'category.educational.workshops',
                    ],
                    [
                        'title' => 'category.educational.lectures',
                    ],
                    [
                        'title' => 'category.educational.seminars',
                    ],
                    [
                        'title' => 'category.educational.conferences',
                    ],
                    [
                        'title' => 'category.educational.training_sessions',
                    ],
                    [
                        'title' => 'category.educational.academic_talks',
                    ],
                ],
            ],
            [
                'title' => 'category.exhibition',
                'subcategories' => [
                    [
                        'title' => 'category.exhibition.art_shows',
                    ],
                    [
                        'title' => 'category.exhibition.science_expos',
                    ],
                    [
                        'title' => 'category.exhibition.trade_fairs',
                    ],
                    [
                        'title' => 'category.exhibition.fashion_shows',
                    ],
                    [
                        'title' => 'category.exhibition.auto_exhibitions',
                    ],
                    [
                        'title' => 'category.exhibition.virtual_experiences',
                    ],
                ],
            ],
            [
                'title' => 'category.festival',
                'subcategories' => [
                    [
                        'title' => 'category.festival.music_festivals',
                    ],
                    [
                        'title' => 'category.festival.food_and_drink_festivals',
                    ],
                    [
                        'title' => 'category.festival.cultural_festivals',
                    ],
                    [
                        'title' => 'category.festival.film_festivals',
                    ],
                    [
                        'title' => 'category.festival.literary_festivals',
                    ],
                    [
                        'title' => 'category.festival.seasonal_celebrations',
                    ],
                ],
            ],
            [
                'title' => 'category.food_and_drink',
                'subcategories' => [
                    [
                        'title' => 'category.food_and_drink.food_tastings',
                    ],
                    [
                        'title' => 'category.food_and_drink.wine_tasting_events',
                    ],
                    [
                        'title' => 'category.food_and_drink.beer_festivals',
                    ],
                    [
                        'title' => 'category.food_and_drink.culinary_workshops',
                    ],
                    [
                        'title' => 'category.food_and_drink.restaurant_openings',
                    ],
                    [
                        'title' => 'category.food_and_drink.cooking_classes',
                    ],
                ],
            ],
            [
                'title' => 'category.social_issues.labor_protests',
                'subcategories' => [
                    [
                        'title' => 'category.social_issues.labor_strike',
                    ],
                    [
                        'title' => 'category.social_issues.worker_protest',
                    ],
                    [
                        'title' => 'category.social_issues.union_meeting',
                    ],
                ],
            ],
            [
                'title' => 'category.fundraising',
                'subcategories' => [
                    [
                        'title' => 'category.fundraising.charity_auctions',
                    ],
                    [
                        'title' => 'category.fundraising.walkathons',
                    ],
                    [
                        'title' => 'category.fundraising.crowdfunding_campaigns',
                    ],
                    [
                        'title' => 'category.fundraising.gala_events',
                    ],
                    [
                        'title' => 'category.fundraising.charity_runs',
                    ],
                    [
                        'title' => 'category.fundraising.volunteer_drives',
                    ],
                ],
            ],
            [
                'title' => 'category.gaming',
                'subcategories' => [
                    [
                        'title' => 'category.gaming.video_game_tournaments',
                    ],
                    [
                        'title' => 'category.gaming.board_game_nights',
                    ],
                    [
                        'title' => 'category.gaming.role_playing_game_sessions',
                    ],
                    [
                        'title' => 'category.gaming.esports_events',
                    ],
                    [
                        'title' => 'category.gaming.game_development_meetups',
                    ],
                ],
            ],
            [
                'title' => 'category.health_and_wellness',
                'subcategories' => [
                    [
                        'title' => 'category.health_and_wellness.wellness-coaching',
                    ],
                    [
                        'title' => 'category.health_and_wellness.yoga_sessions',
                    ],
                    [
                        'title' => 'category.health_and_wellness.holistic-health-retreats',
                    ],
                    [
                        'title' => 'category.health_and_wellness.mindfulness_workshops',
                    ],
                    [
                        'title' => 'category.health_and_wellness.fitness_classes',
                    ],
                    [
                        'title' => 'category.health_and_wellness.nutrition_seminars',
                    ],
                    [
                        'title' => 'category.health_and_wellness.meditation_events',
                    ],
                    [
                        'title' => 'category.health_and_wellness.wellness_retreats',
                    ],
                ],
            ],
            [
                'title' => 'category.hobby',
                'subcategories' => [
                    [
                        'title' => 'category.painting',
                    ],
                    [
                        'title' => 'category.drawing',
                    ],
                    [
                        'title' => 'category.photography',
                    ],
                    [
                        'title' => 'category.crafting',
                    ],
                    [
                        'title' => 'category.gardening',
                    ],
                    [
                        'title' => 'category.model_building',
                    ],
                    [
                        'title' => 'category.hobby.model_building',
                    ],
                    [
                        'title' => 'category.hobby.diy',
                    ],
                ],
            ],
            [
                'title' => 'category.holiday',
                'subcategories' => [
                    [
                        'title' => 'category.holiday.christmas_celebrations',
                    ],
                    [
                        'title' => 'category.holiday.new_years_eve_parties',
                    ],
                    [
                        'title' => 'category.holiday.thanksgiving_events',
                    ],
                    [
                        'title' => 'category.holiday.halloween_parties',
                    ],
                    [
                        'title' => 'category.holiday.independence_day_celebrations',
                    ],
                    [
                        'title' => 'category.holiday.valentines_day_specials',
                    ],
                    [
                        'title' => 'category.holiday.national-holiday',
                    ],
                ],
            ],
            [
                'title' => 'category.business_networking',
                'subcategories' => [
                    [
                        'title' => 'category.business.networking_events',
                    ],
                    [
                        'title' => 'category.business.seminars',
                    ],
                    [
                        'title' => 'category.business.product_launches',
                    ],
                    [
                        'title' => 'category.business.career_fairs',
                    ],
                    [
                        'title' => 'category.business.startup_showcases',
                    ],
                ],
            ],
            [
                'title' => 'category.outdoor',
                'subcategories' => [
                    [
                        'title' => 'category.outdoor.biking',
                    ],

                    [
                        'title' => 'category.outdoor.bird-watching',
                    ],
                    [
                        'title' => 'category.outdoor.camping',
                    ],
                    [
                        'title' => 'category.outdoor.stargazing',
                    ],
                    [
                        'title' => 'category.outdoor.cycling',
                    ],
                    [
                        'title' => 'category.outdoor.running',
                    ],
                    [
                        'title' => 'category.outdoor.fishing',
                    ],
                    [
                        'title' => 'category.outdoor.picnics',
                    ],
                ],
            ],
            [
                'title' => 'category.party',
                'subcategories' => [
                    [
                        'title' => 'category.party.birthday',
                    ],
                    [
                        'title' => 'category.party.themed-party',
                    ],
                    [
                        'title' => 'category.party.bbq',
                    ],
                    [
                        'title' => 'category.party.outdoor',
                    ],
                    [
                        'title' => 'category.party.dinner',
                    ],
                    [
                        'title' => 'category.party.bar-hopping',
                    ],
                    [
                        'title' => 'category.party.birthday_parties',
                    ],
                    [
                        'title' => 'category.party.graduation_celebrations',
                    ],
                    [
                        'title' => 'category.party.retirement_parties',
                    ],
                    [
                        'title' => 'category.party.theme_parties',
                    ],
                    [
                        'title' => 'category.party.costume_parties',
                    ],
                    [
                        'title' => 'category.party.house_parties',
                    ],
                ],
            ],
            [
                'title' => 'category.arts_and_culture.writing_and_literature',
                'subcategories' => [
                    [
                        'title' => 'category.writing_and_literature.creative_writing_workshops',
                    ],
                    [
                        'title' => 'category.writing_and_literature.book_club_meetings',
                    ],
                    [
                        'title' => 'category.writing_and_literature.author_meet_and_greets',
                    ],
                    [
                        'title' => 'category.writing_and_literature.poetry_slams',
                    ],
                    [
                        'title' => 'category.screenwriting',
                    ],
                ],
            ],
            [
                'title' => 'category.social',
                'subcategories' => [
                    [
                        'title' => 'category.social.after-work-drink',
                    ],
                    [
                        'title' => 'category.social.movie_night',
                    ],
                    [
                        'title' => 'category.social.mixers',
                    ],
                    [
                        'title' => 'category.social.meet_and_greet_events',
                    ],
                    [
                        'title' => 'category.social.friendship_events',
                    ],
                    [
                        'title' => 'category.social.community_gatherings',
                    ],
                    [
                        'title' => 'category.social.networking_parties',
                    ],
                    [
                        'title' => 'category.social.speed_dating',
                    ],
                ],
            ],
            [
                'title' => 'category.sports',
                'subcategories' => [
                    [
                        'title' => 'category.team_sports',
                    ],
                    [
                        'title' => 'category.individual_sports',
                    ],
                    [
                        'title' => 'category.running_events',
                    ],
                    [
                        'title' => 'category.cycling_tournaments',
                    ],
                    [
                        'title' => 'category.water_sports_competitions',
                    ],
                    [
                        'title' => 'category.sports.extreme_sports',
                    ],
                    [
                        'title' => 'category.competition-sports',
                    ],
                ],
            ],
            [
                'title' => 'category.technology',
                'subcategories' => [
                    [
                        'title' => 'category.technology.co-coding',
                    ],
                    [
                        'title' => 'category.technology.tech_conferences',
                    ],
                    [
                        'title' => 'category.technology.hackathons',
                    ],
                    [
                        'title' => 'category.technology.gaming_events',
                    ],
                    [
                        'title' => 'category.technology.startup_meetups',
                    ],
                    [
                        'title' => 'category.technology.coding_workshops',
                    ],
                    [
                        'title' => 'category.technology.innovation_forums',
                    ],
                    [
                        'title' => 'category.technology.tech-fair',
                    ],
                    [
                        'title' => 'category.technology.demonstration',
                    ],
                ],
            ],
            [
                'title' => 'category.travel',
                'subcategories' => [
                    [
                        'title' => 'category.travel.adventure_tours',
                    ],
                    [
                        'title' => 'category.travel.beach_vacations',
                    ],
                    [
                        'title' => 'category.travel.city_exploration',
                    ],
                    [
                        'title' => 'category.travel.road-trip',
                    ],
                    [
                        'title' => 'category.travel.car-pool',
                    ],
                    [
                        'title' => 'category.travel.cruise',
                    ],
                    [
                        'title' => 'category.travel.hiking_excursions',
                    ],
                    [
                        'title' => 'category.travel.wildlife_safari',
                    ],
                ],
            ],
            [
                'title' => 'category.workshop',
                'subcategories' => [
                    [
                        'title' => 'category.workshop.pottery',
                    ],
                    [
                        'title' => 'category.workshop.painting',
                    ],
                    [
                        'title' => 'category.workshop.sculpture',
                    ],
                    [
                        'title' => 'category.workshop.ceramics',
                    ],
                    [
                        'title' => 'category.workshop.skill_building',
                    ],
                    [
                        'title' => 'category.workshop.crafting_workshops',
                    ],
                    [
                        'title' => 'category.workshop.tech_workshops',
                    ],
                    [
                        'title' => 'category.workshop.business_workshops',
                    ],
                    [
                        'title' => 'category.workshop.personal_development',
                    ],
                    [
                        'title' => 'category.workshop.language_learning',
                    ],
                    [
                        'title' => 'category.workshop.gardening',
                    ],
                    [
                        'title' => 'category.workshop.sustainable-living',
                    ],
                ],
            ],
        ];
    }
}
