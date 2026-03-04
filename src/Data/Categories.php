<?php

declare(strict_types=1);

namespace App\Data;

class Categories
{
    /**
     * Returns a flat list of translation keys for all categories.
     * Keys must exist in categories+intl-icu.{locale}.php translation files.
     *
     * @return array<string>
     */
    public function getCategories(): array
    {
        return array_merge(
            $this->social(),
            $this->artsAndCulture(),
            $this->festivals(),
            $this->celebrations(),
            $this->foodAndDrink(),
            $this->learningAndGrowth(),
            $this->outdoorsAndNature(),
            $this->sportsAndFitness(),
            $this->gamesAndHobbies(),
            $this->wellbeing(),
            $this->charityAndCommunity(),
            $this->collectiveAction(),
            $this->travelAndAdventure(),
            $this->technology(),
        );
    }

    /** @return array<string> */
    private function social(): array
    {
        return [
            'category.social.casual_meetup',
            'category.social.networking',
            'category.social.social_mixer',
            'category.social.speed_dating',
            'category.social.after_work_drinks',
            'category.social.community_gathering',
            'category.social.meet_and_greet',
            'category.social.friendship_event',
            'category.social.movie_night',
        ];
    }

    /** @return array<string> */
    private function artsAndCulture(): array
    {
        return [
            'category.arts_and_culture.live_music',
            'category.arts_and_culture.concert',
            'category.arts_and_culture.theatre',
            'category.arts_and_culture.comedy',
            'category.arts_and_culture.dance_show',
            'category.arts_and_culture.art_exhibition',
            'category.arts_and_culture.film_screening',
            'category.arts_and_culture.circus',
            'category.arts_and_culture.magic_show',
            'category.arts_and_culture.poetry',
        ];
    }

    /** @return array<string> */
    private function festivals(): array
    {
        return [
            'category.festival.music_festival',
            'category.festival.food_and_drink_festival',
            'category.festival.cultural_festival',
            'category.festival.film_festival',
            'category.festival.literary_festival',
            'category.festival.seasonal_celebration',
            'category.festival.street_fair',
        ];
    }

    /** @return array<string> */
    private function celebrations(): array
    {
        return [
            'category.celebration.birthday_party',
            'category.celebration.anniversary',
            'category.celebration.graduation',
            'category.celebration.house_party',
            'category.celebration.themed_party',
            'category.celebration.bbq',
        ];
    }

    /** @return array<string> */
    private function foodAndDrink(): array
    {
        return [
            'category.food_and_drink.dinner',
            'category.food_and_drink.wine_tasting',
            'category.food_and_drink.beer_and_craft',
            'category.food_and_drink.cooking_class',
            'category.food_and_drink.food_market',
            'category.food_and_drink.food_tasting',
        ];
    }

    /** @return array<string> */
    private function learningAndGrowth(): array
    {
        return [
            'category.learning.workshop',
            'category.learning.talk_and_lecture',
            'category.learning.conference',
            'category.learning.seminar',
            'category.learning.book_club',
            'category.learning.language_learning',
            'category.learning.creative_writing',
            'category.learning.personal_development',
        ];
    }

    /** @return array<string> */
    private function outdoorsAndNature(): array
    {
        return [
            'category.outdoor.hiking',
            'category.outdoor.cycling',
            'category.outdoor.camping',
            'category.outdoor.picnic',
            'category.outdoor.bird_watching',
            'category.outdoor.stargazing',
            'category.outdoor.fishing',
        ];
    }

    /** @return array<string> */
    private function sportsAndFitness(): array
    {
        return [
            'category.sports.running',
            'category.sports.yoga',
            'category.sports.fitness_class',
            'category.sports.team_sports',
            'category.sports.swimming',
            'category.sports.martial_arts',
            'category.sports.climbing',
        ];
    }

    /** @return array<string> */
    private function gamesAndHobbies(): array
    {
        return [
            'category.hobby.board_games',
            'category.hobby.video_games',
            'category.hobby.role_playing',
            'category.hobby.photography',
            'category.hobby.art_and_craft',
            'category.hobby.pottery',
            'category.hobby.gardening',
        ];
    }

    /** @return array<string> */
    private function wellbeing(): array
    {
        return [
            'category.wellbeing.meditation',
            'category.wellbeing.mindfulness',
            'category.wellbeing.wellness_retreat',
            'category.wellbeing.health_talk',
        ];
    }

    /** @return array<string> */
    private function charityAndCommunity(): array
    {
        return [
            'category.charity.volunteering',
            'category.charity.fundraiser',
            'category.charity.charity_run',
        ];
    }

    /** @return array<string> */
    private function collectiveAction(): array
    {
        return [
            'category.collective_action.strike',
            'category.collective_action.protest',
            'category.collective_action.union_meeting',
            'category.collective_action.rally',
        ];
    }

    /** @return array<string> */
    private function travelAndAdventure(): array
    {
        return [
            'category.travel.day_trip',
            'category.travel.group_travel',
            'category.travel.road_trip',
        ];
    }

    /** @return array<string> */
    private function technology(): array
    {
        return [
            'category.technology.hackathon',
            'category.technology.tech_meetup',
            'category.technology.startup_event',
            'category.technology.innovation_forum',
            'category.technology.demo_day',
        ];
    }
}
