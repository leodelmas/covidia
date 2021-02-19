<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\WorkTime;
use App\Repository\WorkTimeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class WorkTimeType extends AbstractType {

    private $workTimeRepository;
    private $security;

    public function __construct(WorkTimeRepository $workTimeRepository, Security $security) {
        $this->workTimeRepository = $workTimeRepository;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('dateStart', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('dateEnd', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('isTeleworked')
            ->add('plannedWorkTimes', HiddenType::class, [
                'mapped' => false,
                'data' => $this->getPlannedWorkTimesJson()
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => WorkTime::class,
            'translation_domain' => 'WorkTime.forms',
        ]);
    }

    private function getPlannedWorkTimesJson() {
        $workTimes = $this->workTimeRepository->findAllByUser($this->security->getUser()->getId());
        $workTimesJson = [];

        foreach($workTimes as $workTime) {
            $workTimesJson[] = $workTime->getFormattedJsonDates();
        }

        return json_encode($workTimesJson);
    }
}
