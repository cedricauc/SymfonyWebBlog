<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Post;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $password = $this->hasher->hashPassword($admin, '1234');
        $admin->setPassword($password);

        $manager->persist($admin);

        $user = new User();
        $user->setUsername('user');
        $user->setRoles(['ROLE_USER']);
        $password = $this->hasher->hashPassword($user, '1234');
        $user->setPassword($password);

        $manager->persist($user);

        $reader = new User();
        $reader->setUsername('reader');
        $reader->setRoles(['ROLE_READER']);
        $password = $this->hasher->hashPassword($reader, '1234');
        $reader->setPassword($password);

        $manager->persist($reader);

        for ($h = 0; $h < 3; $h++) {
            $post = new Post();
            $post->setTitle($faker->title)
                ->setPublished($faker->boolean)
                ->setContent($faker->text);

            $manager->persist($post);
        }

        for ($k = 0; $k < 10; $k++) {
            $product = new Product();
            $productName = $faker->word;
            $product->setProductName($productName)
                    ->setShortDescription($faker->paragraph(1))
                    ->setDescription($faker->text)
                    ->setSku($productName)
                    ->setStock($faker->randomNumber(4))
                    ->setUnitPrice($faker->randomFloat(2,1, 9999))
                    ->setCreatedAt($faker->dateTime);

            $manager->persist($product);
        }

        $manager->flush();

    }


}
