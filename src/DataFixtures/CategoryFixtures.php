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
                        'title' => 'category.music-concert',
                    ],
                    [
                        'title' => 'category.theater-show',
                    ],
                    [
                        'title' => 'category.dance-show',
                    ],
                    [
                        'title' => 'category.comedy-show',
                    ],
                    [
                        'title' => 'category.magic-show',
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
                        'title' => 'category.educational.coding-bootcamp',
                    ],
                    [
                        'title' => 'category.educational.workshop',
                    ],
                    [
                        'title' => 'category.educational.lecture',
                    ],
                    [
                        'title' => 'category.educational.seminar',
                    ],
                    [
                        'title' => 'category.educational.conference',
                    ],
                    [
                        'title' => 'category.educational.training_session',
                    ],
                    [
                        'title' => 'category.educational.academic_talk',
                    ],
                ],
            ],
            [
                'title' => 'category.exhibition',
                'subcategories' => [
                    [
                        'title' => 'category.exhibition.art_show',
                    ],
                    [
                        'title' => 'category.exhibition.science_expo',
                    ],
                    [
                        'title' => 'category.exhibition.trade_fair',
                    ],
                    [
                        'title' => 'category.exhibition.fashion_show',
                    ],
                    [
                        'title' => 'category.exhibition.auto_exhibition',
                    ],
                    [
                        'title' => 'category.exhibition.virtual_experience',
                    ],
                ],
            ],
            [
                'title' => 'category.festival',
                'subcategories' => [
                    [
                        'title' => 'category.festival.music_festival',
                    ],
                    [
                        'title' => 'category.festival.food_and_drink_festival',
                    ],
                    [
                        'title' => 'category.festival.cultural_festival',
                    ],
                    [
                        'title' => 'category.festival.film_festival',
                    ],
                    [
                        'title' => 'category.festival.literary_festival',
                    ],
                    [
                        'title' => 'category.festival.seasonal_celebration',
                    ],
                ],
            ],
            [
                'title' => 'category.food_and_drink',
                'subcategories' => [
                    [
                        'title' => 'category.food_and_drink.food_tasting',
                    ],
                    [
                        'title' => 'category.food_and_drink.wine_tasting_event',
                    ],
                    [
                        'title' => 'category.food_and_drink.beer_festival',
                    ],
                    [
                        'title' => 'category.food_and_drink.culinary_workshop',
                    ],
                    [
                        'title' => 'category.food_and_drink.restaurant_opening',
                    ],
                    [
                        'title' => 'category.food_and_drink.cooking_class',
                    ],
                ],
            ],
            [
                'title' => 'category.social_issues.labor_protest',
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
                        'title' => 'category.fundraising.charity_auction',
                    ],
                    [
                        'title' => 'category.fundraising.walkathon',
                    ],
                    [
                        'title' => 'category.fundraising.crowdfunding_campaign',
                    ],
                    [
                        'title' => 'category.fundraising.gala_event',
                    ],
                    [
                        'title' => 'category.fundraising.charity_run',
                    ],
                    [
                        'title' => 'category.fundraising.volunteer_drive',
                    ],
                ],
            ],
            [
                'title' => 'category.gaming',
                'subcategories' => [
                    [
                        'title' => 'category.gaming.video_game_tournament',
                    ],
                    [
                        'title' => 'category.gaming.board_game_night',
                    ],
                    [
                        'title' => 'category.gaming.role_playing_game_session',
                    ],
                    [
                        'title' => 'category.gaming.esports_event',
                    ],
                    [
                        'title' => 'category.gaming.game_development_meetup',
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
                        'title' => 'category.health_and_wellness.yoga_session',
                    ],
                    [
                        'title' => 'category.health_and_wellness.holistic-health-retreat',
                    ],
                    [
                        'title' => 'category.health_and_wellness.mindfulness_workshop',
                    ],
                    [
                        'title' => 'category.health_and_wellness.fitness_class',
                    ],
                    [
                        'title' => 'category.health_and_wellness.nutrition_seminar',
                    ],
                    [
                        'title' => 'category.health_and_wellness.meditation_event',
                    ],
                    [
                        'title' => 'category.health_and_wellness.wellness_retreat',
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
                        'title' => 'category.holiday.christmas_celebration',
                    ],
                    [
                        'title' => 'category.holiday.new_years_eve_party',
                    ],
                    [
                        'title' => 'category.holiday.thanksgiving_event',
                    ],

                    [
                        'title' => 'category.holiday.halloween_party',
                    ],
                    [
                        'title' => 'category.holiday.independence_day_celebration',
                    ],
                    [
                        'title' => 'category.holiday.valentines_day_special',
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
                        'title' => 'category.business.networking_event',
                    ],
                    [
                        'title' => 'category.business.seminar',
                    ],
                    [
                        'title' => 'category.business.product_launch',
                    ],
                    [
                        'title' => 'category.business.career_fair',
                    ],
                    [
                        'title' => 'category.business.startup_showcase',
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
                        'title' => 'category.outdoor.picnic',
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
                        'title' => 'category.party.birthday_party',
                    ],
                    [
                        'title' => 'category.party.graduation_celebration',
                    ],
                    [
                        'title' => 'category.party.retirement_party',
                    ],
                    [
                        'title' => 'category.party.theme_party',
                    ],
                    [
                        'title' => 'category.party.costume_party',
                    ],
                    [
                        'title' => 'category.party.house_party',
                    ],
                ],
            ],
            [
                'title' => 'category.arts_and_culture.writing_and_literature',
                'subcategories' => [
                    [
                        'title' => 'category.writing_and_literature.creative_writing_workshop',
                    ],
                    [
                        'title' => 'category.writing_and_literature.book_club_meeting',
                    ],
                    [
                        'title' => 'category.writing_and_literature.author_meet_and_greet',
                    ],
                    [
                        'title' => 'category.writing_and_literature.poetry_slam',
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
                        'title' => 'category.social.mixer',
                    ],
                    [
                        'title' => 'category.social.meet_and_greet_event',
                    ],
                    [
                        'title' => 'category.social.friendship_event',
                    ],
                    [
                        'title' => 'category.social.community_gathering',
                    ],
                    [
                        'title' => 'category.social.networking_party',
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
                        'title' => 'category.team_sport',
                    ],
                    [
                        'title' => 'category.individual_sport',
                    ],
                    [
                        'title' => 'category.running_event',
                    ],
                    [
                        'title' => 'category.cycling_tournament',
                    ],
                    [
                        'title' => 'category.water_sports_competition',
                    ],
                    [
                        'title' => 'category.sports.extreme_sport',
                    ],
                    [
                        'title' => 'category.competition-sport',
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
                        'title' => 'category.technology.tech_conference',
                    ],
                    [
                        'title' => 'category.technology.hackathon',
                    ],
                    [
                        'title' => 'category.technology.gaming_event',
                    ],
                    [
                        'title' => 'category.technology.startup_meetup',
                    ],
                    [
                        'title' => 'category.technology.coding_workshop',
                    ],
                    [
                        'title' => 'category.technology.innovation_forum',
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
                        'title' => 'category.travel.adventure_tour',
                    ],
                    [
                        'title' => 'category.travel.beach_vacation',
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
                        'title' => 'category.travel.hiking_excursion',
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
                        'title' => 'category.workshop.crafting_workshop',
                    ],
                    [
                        'title' => 'category.workshop.tech_workshop',
                    ],
                    [
                        'title' => 'category.workshop.business_workshop',
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
