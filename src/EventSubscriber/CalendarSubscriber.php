<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\WorkingDayRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class CalendarSubscriber implements EventSubscriberInterface
{
    /**
     * @var WorkingDayRepository
     */
    private $workingDayRepository;

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
     * @param WorkingDayRepository $workingDayRepository
     * @param UrlGeneratorInterface $router
     * @param Security $security
     */
    public function __construct(WorkingDayRepository $workingDayRepository, UrlGeneratorInterface $router, Security $security) {
        $this->workingDayRepository = $workingDayRepository;
        $this->router = $router;
        $this->security = $security;
    }

    public static function getSubscribedEvents() {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    /**
     * @param CalendarEvent $calendar
     * @throws Exception
     */
    public function onCalendarSetData(CalendarEvent $calendar)
    {
        $user = $this->security->getUser();
        $workingDays = $this->workingDayRepository->findAllByUser($user->getId());

        foreach ($workingDays as $workingDay) {
            $plannedDay = new Event(
                $workingDay->getIsTeleworked() ? 'Télétravail' : 'Présentiel',
                new \DateTime($workingDay->getDate()->format('Y-m-d'))
            );

            /*$plannedDay->setOptions([
                'backgroundColor' => 'red',
                'borderColor' => 'red',
            ]);*/

            $plannedDay->addOption(
                'url',
                $this->router->generate('working_day.edit', [
                    'id' => $workingDay->getId(),
                ])
            );

            $calendar->addEvent($plannedDay);
        }
    }
}