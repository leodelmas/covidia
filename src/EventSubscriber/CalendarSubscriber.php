<?php

namespace App\EventSubscriber;

use App\Entity\Task;
use App\Entity\User;
use App\Entity\WorkTime;
use App\Repository\TaskRepository;
use App\Repository\WorkTimeRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class CalendarSubscriber implements EventSubscriberInterface
{
    /**
     * @var WorkTimeRepository
     */
    private $workTimeRepository;

    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var User
     */
    private $user;

    /**
     * CalendarSubscriber constructor.
     * @param WorkTimeRepository $workTimeRepository
     * @param TaskRepository $taskRepository
     * @param UrlGeneratorInterface $router
     * @param Security $security
     */
    public function __construct(WorkTimeRepository $workTimeRepository, TaskRepository $taskRepository, UrlGeneratorInterface $router, Security $security) {
        $this->workTimeRepository = $workTimeRepository;
        $this->taskRepository = $taskRepository;
        $this->router = $router;
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    /**
     * @param CalendarEvent $calendar
     * @throws Exception
     */
    public function onCalendarSetData(CalendarEvent $calendar) {
        $user = $this->security->getUser();
        $workTimes = $this->workTimeRepository->findAllByUser($user->getId());
        $tasks = $this->taskRepository->findAllByUser($user->getId());

        $this->planTimes($calendar, $workTimes);
        //$this->planTasks($calendar, $tasks);
    }

    /**
     * @param CalendarEvent $calendar
     * @param WorkTime[] $workTimes
     * @throws Exception
     */
    public function planTimes(CalendarEvent $calendar, array $workTimes) {
        foreach ($workTimes as $workTime) {
            $begin = new DateTime($workTime->getDateStart()->format('Y-m-d'));
            $end   =  new DateTime($workTime->getDateEnd()->modify('+1 day')->format('Y-m-d'));

            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($begin, $interval, $end);

            foreach ($period as $day) {
                $plannedDay = new Event(
                    $workTime->getIsTeleworked() ? 'Télétravail' : 'Présentiel',
                    new DateTime($day->format('Y-m-d'))
                );

                $plannedDay->setOptions([
                    'rendering' => 'background',
                    'backgroundColor' => $workTime->getIsTeleworked() ? 'blue' : 'green'
                ]);

                $plannedDay->addOption(
                    'url',
                    $this->router->generate('workTime.edit', [
                        'id' => $workTime->getId(),
                    ])
                );

                $calendar->addEvent($plannedDay);
            }
        }
    }

    /**
     * @param CalendarEvent $calendar
     * @param Task[] $tasks
     * @throws Exception
     */
    private function planTasks(CalendarEvent $calendar, array $tasks) {
        foreach ($tasks as $task) {

            $plannedTask = new Event(
                'Tâche',
                new DateTime($task->getDateTimeStart()->format('Y-m-d')),
                new DateTime($task->getDateTimeEnd()->format('Y-m-d'))
            );
        }

        $plannedTask->addOption(
            'url',
            $this->router->generate('task.edit', [
                'id' => $task->getId(),
            ])
        );

        $calendar->addEvent($plannedTask);
    }
}