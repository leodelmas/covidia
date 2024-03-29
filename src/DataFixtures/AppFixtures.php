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
    private $faker;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager)
    {
        $jobs = $this->create_Jobs($manager);
        $users = $this->create_Users($manager, $jobs);
        $taskCategs = $this->create_TaskCategs($manager);
        $work_times = $this->create_WorkTimes($manager, $users);
        $tasks = $this->create_Tasks($manager, $work_times, $taskCategs);
    }

    private function create_Tasks(ObjectManager $manager, Array $work_times, Array $taskCategs)
    {
        $tasks = array();

        foreach ($work_times as &$work_time) {
            for ($i = 0; $i < $this->faker->numberBetween(2, 8); $i++) {
                do{
                    $taskCateg = $taskCategs[$this->faker->numberBetween(0, count($taskCategs)-1)];
                }while($taskCateg->getIsRemote() == 0 && $work_time->getIsTeleworked() == 1 ||
                        $taskCateg->getIsPhysical() == 0 && $work_time->getIsTeleworked() == 0);

                $task = new Task();
                $task
                    ->setDateTimeStart($this->faker->dateTimeBetween($work_time->getDateStart(), $work_time->getDateEnd()))
                    ->setDateTimeEnd($this->faker->dateTimeBetween($task->getDateTimeStart(), $work_time->getDateEnd()))
                    ->setWorkTime($work_time)
                    ->setUser($work_time->getUser())
                    ->setTaskCategory($taskCateg)
                    ->setComment($this->faker->text($this->faker->numberBetween(20,100)));
                $manager->persist($task);
            }
        }

        $manager->flush();

        return $tasks;
    }

    private function create_WorkTimes(ObjectManager $manager, Array $users)
    {
        $work_times = array();

        foreach ($users as &$user) {
            for ($i = 0; $i < $this->faker->numberBetween(2, 15); $i++) {
                array_push($work_times, new WorkTime());
                $work_times[count($work_times) - 1]
                    ->setDateStart($this->faker->dateTimeBetween('-2 months', 'now'))
                    ->setDateEnd($this->faker->dateTimeBetween($work_times[count($work_times) - 1]->getDateStart(), 'now'))
                    ->setIsTeleworked($this->faker->boolean)
                    ->setUser($user);
                $manager->persist($work_times[count($work_times) - 1]);
            }
        }

        $manager->flush();
        return $work_times;
    }

    private function create_TaskCategs(ObjectManager $manager)
    {
        $taskCategs = array();

        array_push($taskCategs, new TaskCategory());
        $taskCategs[count($taskCategs) - 1]
            ->setName('Sécuriser')
            ->setIsRemote(false)
            ->setIsPhysical(true)
            ->setColor($this->faker->hexColor);
        $manager->persist( $taskCategs[count($taskCategs) - 1]);

        array_push($taskCategs, new TaskCategory());
        $taskCategs[count($taskCategs) - 1]
            ->setName('Réunion externe en vidéoconférence')
            ->setIsRemote(true)
            ->setIsPhysical(false)
            ->setColor($this->faker->hexColor);
        $manager->persist( $taskCategs[count($taskCategs) - 1]);

        array_push($taskCategs, new TaskCategory());
        $taskCategs[count($taskCategs) - 1]
            ->setName('Maintenance')
            ->setIsRemote(true)
            ->setIsPhysical(true)
            ->setColor($this->faker->hexColor);
        $manager->persist( $taskCategs[count($taskCategs) - 1]);

        array_push($taskCategs, new TaskCategory());
        $taskCategs[count($taskCategs) - 1]
            ->setName('Développement')
            ->setIsRemote(true)
            ->setIsPhysical(true)
            ->setColor($this->faker->hexColor);
        $manager->persist( $taskCategs[count($taskCategs) - 1]);

        array_push($taskCategs, new TaskCategory());
        $taskCategs[count($taskCategs) - 1]
            ->setName('Nettoyage')
            ->setIsRemote(false)
            ->setIsPhysical(true)
            ->setColor($this->faker->hexColor);
        $manager->persist( $taskCategs[count($taskCategs) - 1]);

        array_push($taskCategs, new TaskCategory());
        $taskCategs[count($taskCategs) - 1]
            ->setName('Nettoyage')
            ->setIsRemote(false)
            ->setIsPhysical(true)
            ->setColor($this->faker->hexColor);
        $manager->persist( $taskCategs[count($taskCategs) - 1]);

        array_push($taskCategs, new TaskCategory());
        $taskCategs[count($taskCategs) - 1]
            ->setName('Designer')
            ->setIsRemote(true)
            ->setIsPhysical(true)
            ->setColor($this->faker->hexColor);
        $manager->persist( $taskCategs[count($taskCategs) - 1]);

        array_push($taskCategs, new TaskCategory());
        $taskCategs[count($taskCategs) - 1]
            ->setName('Dessiner')
            ->setIsRemote(true)
            ->setIsPhysical(true)
            ->setColor($this->faker->hexColor);
        $manager->persist( $taskCategs[count($taskCategs) - 1]);

        array_push($taskCategs, new TaskCategory());
        $taskCategs[count($taskCategs) - 1]
            ->setName('Formation')
            ->setIsRemote(true)
            ->setIsPhysical(true)
            ->setColor($this->faker->hexColor);
        $manager->persist( $taskCategs[count($taskCategs) - 1]);

        $manager->flush();
        return $taskCategs;
    }

    private function create_Jobs(ObjectManager $manager)
    {
        $jobs = array();

        array_push($jobs, (new Job())->setName("Développeur"));
        $manager->persist($jobs[count($jobs) - 1]);
        array_push($jobs, (new Job())->setName("Secrétaire"));
        $manager->persist($jobs[count($jobs) - 1]);
        array_push($jobs, (new Job())->setName("Stagiaire"));
        $manager->persist($jobs[count($jobs) - 1]);
        array_push($jobs, (new Job())->setName("Graphiste"));
        $manager->persist($jobs[count($jobs) - 1]);
        array_push($jobs, (new Job())->setName("Web designer"));
        $manager->persist($jobs[count($jobs) - 1]);
        array_push($jobs, (new Job())->setName("Formateur"));
        $manager->persist($jobs[count($jobs) - 1]);
        array_push($jobs, (new Job())->setName("Chef de projet"));
        $manager->persist($jobs[count($jobs) - 1]);
        array_push($jobs, (new Job())->setName("Avocat"));
        $manager->persist($jobs[count($jobs) - 1]);
        array_push($jobs, (new Job())->setName("Agent de Nettoyage"));
        $manager->persist($jobs[count($jobs) - 1]);
        array_push($jobs, (new Job())->setName("Agent de sécurité"));
        $manager->persist($jobs[count($jobs) - 1]);

        $manager->flush();

        return $jobs;
    }

    private function create_Users(ObjectManager $manager, Array $jobs)
    {
        $users = array();

        array_push($users, new User());
        $users[0]
            ->setFirstname('admin')
            ->setLastname('admin')
            ->setEmail('admin@admin.admin')
            ->setPhone('0000000000')
            ->setBirthDate(new \DateTime())
            ->setHiringDate(new \DateTime())
            ->setPassword($this->passwordEncoder->encodePassword($users[0], 'admin'))
            ->setIsExecutive(false)
            ->setIsPsychologist(false)
            ->setIsAdmin(true)
            ->setIsPsychologist(true)
            ->setJob($jobs[0]);
        $manager->persist($users[0]);

        array_push($users, new User());
        $users[1]
            ->setFirstname('user')
            ->setLastname('user')
            ->setEmail('user@user.user')
            ->setPhone('0000000000')
            ->setBirthDate(new \DateTime())
            ->setHiringDate(new \DateTime())
            ->setPassword($this->passwordEncoder->encodePassword($users[1], 'user'))
            ->setIsExecutive(true)
            ->setIsPsychologist(false)
            ->setIsAdmin(false)
            ->setJob($jobs[0]);
        $manager->persist($users[1]);

        for ($i = 0; $i < 30; $i++) {
            $phoneNumber = str_replace(" ","",$this->faker->phoneNumber);
            $phoneNumber = str_replace("+33", "0", $phoneNumber);
            $phoneNumber = str_replace("(0)", "", $phoneNumber);

            array_push($users, new User());
            $users[count($users) - 1]
                ->setFirstname($this->faker->firstName)
                ->setLastname($this->faker->lastName)
                ->setEmail(
                    strtr( $users[count($users) - 1]->getFirstname(), $this->unwanted_array ).'@'.
                    strtr( $users[count($users) - 1]->getFirstname(), $this->unwanted_array ).'.'.
                    strtr( $users[count($users) - 1]->getFirstname(), $this->unwanted_array ))
                ->setPhone($phoneNumber)
                ->setBirthDate($this->faker->dateTimeBetween('-60 years', '-20 years'))
                ->setHiringDate($this->faker->dateTimeBetween('-20 years', 'now'))
                ->setPassword($this->passwordEncoder->encodePassword($users[count($users) - 1], strtr( $users[count($users) - 1]->getFirstname(), $this->unwanted_array )))
                ->setIsExecutive($this->faker->boolean)
                ->setIsPsychologist($this->faker->boolean)
                ->setIsAdmin(false)
                ->setJob($jobs[$this->faker->numberBetween(0, (count($jobs) - 1) )]);

            $manager->persist($users[count($users) - 1]);
        }

        $manager->flush();
        return $users;
    }
}
