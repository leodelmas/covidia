<?php

namespace App\DataFixtures;

use App\Entity\Job;
use App\Entity\Task;
use App\Entity\TaskCategory;
use App\Entity\User;
use App\Entity\WorkTime;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
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
            ->setIsPsychologist(false)
            ->setIsAdmin(true)
            ->setIsPsychologist(true)
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
            ->setIsPsychologist(false)
            ->setIsAdmin(false)
            ->setJob($job);

        $manager->persist($user);
        $manager->flush();

        $typeTache = new TaskCategory();
        $typeTache
            ->setName('Commercial')
            ->setIsRemote(false)
            ->setIsPhysical(true)
            ->setColor('#F7AF00');
        $manager->persist($typeTache);
        $manager->flush();

        $typeTache2 = new TaskCategory();
        $typeTache2
            ->setName('Réunion externe en vidéoconférence')
            ->setIsRemote(true)
            ->setIsPhysical(false)
            ->setColor('#748E54');
        $manager->persist($typeTache2);
        $manager->flush();

        $typeTache3 = new TaskCategory();
        $typeTache3
            ->setName('Maintenance')
            ->setIsRemote(true)
            ->setIsPhysical(true)
            ->setColor('#DECAF1');
        $manager->persist($typeTache3);
        $manager->flush();

        $worktime = new WorkTime();
        $worktime 
            ->setDateStart(new DateTime('2021-02-01'))
            ->setDateEnd(new DateTime('2021-02-05'))
            ->setIsTeleworked(true)
            ->setUser($user);
        $manager->persist($worktime);
        $manager->flush();

        $task = new Task();
        $task 
            ->setDateTimeStart(new DateTime('2021-02-01 08:30'))
            ->setDateTimeEnd(new DateTime('2021-02-01 10:30'))
            ->setWorkTime($worktime)
            ->setUser($user)
            ->setTaskCategory($typeTache2)
            ->setComment("Je suis le test de l'ajout d'une catégorie");
        $manager->persist($task);
        $manager->flush();
    }
}
