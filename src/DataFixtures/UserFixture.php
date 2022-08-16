<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('admin@admin.com');
        $user->setPassword('$2y$13$mF7yH3UE2u9sm8pjqD6Sa.W3U90sHS/NucXMYioqR1wwV5Gxs3rba');
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);
        
        $user1 = new User();
        $user1->setEmail('manager@manager.com');
        $user1->setPassword('$2y$13$1OREOb3M1.JdrsU7TkmbGeCcTg7nkhyHTqB8wmmsgebOGTnA/Lmoa');
        $user1->setRoles(['ROLE_MANAGER']);
        $manager->persist($user1);
        
        $user2= new User();
        $user2->setEmail('user@user.com');
        $user2->setPassword('$2y$13$NDWQthembYkJYiz/lpG0w.yOTXecdbl5rVvO.qbVrUnOLoC2baqmy');
        $user2->setRoles(['ROLE_USER']);
        $manager->persist($user2);

        $manager->flush();
    }

    /**
     * Nous permet de classer les fixtures pour pouvoir les éxecuter séparement
     *
     * @return array
     */
    public static function getGroups(): array
    {
        return ['userGroup'];
    }
}
