<?php

namespace App\DataFixtures;

use App\Entity\Job;
use App\Entity\TaskCategory;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $job = new Job();
        $job
            ->setName("Développeur");

        $manager->persist($job);
        $manager->flush();

        $admin = new User();
        $admin
            ->setFirstname('admin')
            ->setLastname('admin')
            ->setEmail('admin@admin.admin')
            ->setPhone('0000000000')
            ->setBirthDate(new \DateTime())
            ->setHiringDate(new \DateTime())
            ->setPassword($this->passwordEncoder->encodePassword($admin, 'admin'))
            ->setIsExecutive(false)
            ->setIsAdmin(true)
            ->setJob($job);
        $manager->persist($admin);

        $user = new User();
        $user
            ->setFirstname('user')
            ->setLastname('user')
            ->setEmail('user@user.user')
            ->setPhone('0000000000')
            ->setBirthDate(new \DateTime())
            ->setHiringDate(new \DateTime())
            ->setPassword($this->passwordEncoder->encodePassword($user, 'user'))
            ->setIsExecutive(true)
            ->setIsAdmin(false)
            ->setJob($job);

        $manager->persist($user);
        $manager->flush();

        $typeTache = new TaskCategory();
        $typeTache
            ->setName('Commercial')
            ->setIsRemote(false)
            ->setIsPhysical(true);
        $manager->persist($typeTache);
        $manager->flush();

        $typeTache2 = new TaskCategory();
        $typeTache2
            ->setName('Réunion externe en vidéoconférence')
            ->setIsRemote(true)
            ->setIsPhysical(false);
        $manager->persist($typeTache2);
        $manager->flush();

        $typeTache3 = new TaskCategory();
        $typeTache3
            ->setName('Maintenance')
            ->setIsRemote(true)
            ->setIsPhysical(true);
        $manager->persist($typeTache3);
        $manager->flush();
    }
}
