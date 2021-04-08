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
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
    'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
    'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
    'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
    'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );

    private $passwordEncoder;
    protected $faker;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager)
    {
        $jobs = $this->create_Jobs($manager);

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
            ->setJob($jobs[0]);
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
            ->setJob($jobs[0]);

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

        $this->create_Users($manager, $jobs);
    }

    private function create_Jobs(ObjectManager $manager)
    {
        $jobs = array();

        $job = new Job();
        $job
            ->setName("Développeur");
        $manager->persist($job);
        $manager->flush();

        array_push($jobs, $job);

        return $jobs;
    }

    private function create_Users(ObjectManager $manager, Array $jobs)
    {
        for ($i = 0; $i < 30; $i++) {
            $phoneNumber = str_replace(" ","",$this->faker->phoneNumber);
            $phoneNumber = str_replace("+33", "0", $phoneNumber);
            $phoneNumber = str_replace("(0)", "", $phoneNumber);

            $user = new User();
            $user
                ->setFirstname($this->faker->firstName)
                ->setLastname($this->faker->lastName)
                ->setEmail(strtr( $user->getFirstname(), $this->unwanted_array ).'@'.strtr( $user->getFirstname(), $this->unwanted_array ).'.'.strtr( $user->getFirstname(), $this->unwanted_array ))
                ->setPhone($phoneNumber)
                ->setBirthDate($this->faker->dateTimeBetween('-60 years', '-20 years'))
                ->setHiringDate($this->faker->dateTimeBetween('-20 years', 'now'))
                ->setPassword($this->passwordEncoder->encodePassword($user, strtr( $user->getFirstname(), $this->unwanted_array )))
                ->setIsExecutive($this->faker->boolean)
                ->setIsPsychologist($this->faker->boolean)
                ->setIsAdmin(false)
                ->setJob($jobs[$this->faker->numberBetween(0, (count($jobs) - 1) )]);

            $manager->persist($user);
            $manager->flush();
        }
    }
}
