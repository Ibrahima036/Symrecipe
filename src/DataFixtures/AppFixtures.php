<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $ingredients = [];
        for ($i = 0; $i < 50; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName('ingredient ' . $i)
                ->setPrice(mt_rand(0, 800));
            $manager->persist($ingredient);
            $ingredients[] = $ingredient;
        }

        for ($j = 0; $j < 25; $j++) {
            $recipe = new Recipe();
            $recipe->setName('recette' . $j)
                ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1441) : null)
                ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
                ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
                ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1001) : null)
                ->setDescription('une belle description')
                ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false);
            for ($k = 0; $k < mt_rand(5, 15); $k++) {
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
            }
            $manager->persist($recipe);
        }

        $manager->flush();
    }
}
