<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hasher;
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');
        $ingredients = [];
        for ($i = 0; $i < 50; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName($faker->name())
                ->setPrice(mt_rand(0, 800));
            $manager->persist($ingredient);
            $ingredients[] = $ingredient;
        }

        for ($j = 0; $j < 25; $j++) {
            $recipe = new Recipe();
            $recipe->setName($faker->name())
                ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1441) : null)
                ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
                ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
                ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1001) : null)
                ->setDescription($faker->text(15))
                ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false);
            for ($k = 0; $k < mt_rand(5, 15); $k++) {
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
            }
            $manager->persist($recipe);
        }
        //création des utilisateur
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setFullname($faker->name())
                ->setPseudo($faker->firstName())
                ->setEmail($faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPassword(
                    $this->hasher->hashPassword(
                        $user,
                        'password'
                    )
                );
            $manager->persist($user);
        }
        $manager->flush();
    }
}
