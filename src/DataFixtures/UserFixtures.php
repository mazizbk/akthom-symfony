<?php

namespace App\DataFixtures;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory as FakerFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\User;

class UserFixtures extends Fixture
{

    protected UserPasswordHasherInterface $userhashPassword;

    /**
     * Class constructor.
     */
    public function __construct(UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->userhashPassword = $userPasswordHasherInterface;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();

        $user = new User();
        $user->setEmail('admin@mail.com');
        $user->setPassword($this->userhashPassword->hashPassword($user, 'password'));
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);


        $user = new User();
        $user->setEmail('aziz.benkhalifa@davidson.fr');
        $user->setPassword($this->userhashPassword->hashPassword($user, 'password'));
        $user->setRoles(['ROLE_USER']);


        $manager->persist($user);

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setPassword($this->userhashPassword->hashPassword($user, 'password'));
            $user->setRoles(['ROLE_USER']);


            $manager->persist($user);
        }

        $manager->flush();
    }
}
