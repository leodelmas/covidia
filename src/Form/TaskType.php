<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\TaskCategory;
use App\Repository\TaskCategoryRepository;
use App\Repository\WorkTimeRepository;
use App\Entity\WorkTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Security\Core\Security;

class TaskType extends AbstractType
{
    private $workTimeRepository;
    private $security;

    public function __construct(WorkTimeRepository $workTimeRepository, Security $security) {
        $this->workTimeRepository = $workTimeRepository;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('workTime', EntityType::class, [
                'class' => WorkTime::class,
                'required' => true,
                'placeholder' => 'Choose a worktime',
                'choice_label' => 'displayLabel',
                'query_builder' => $this->workTimeRepository->findAllByUserQuery($this->security->getUser()->getId()),
                'choice_attr' => function(WorkTime $workTime) {
                    return [
                        'data-date-start' => $workTime->getDateStart()->format('Y-m-d H:i:s'),
                        'data-date-end' => $workTime->getDateEnd()->format('Y-m-d H:i:s'),
                        'data-is-teleworked' => $workTime->getIsTeleworked()
                    ];
                }
            ])
            ->add('dateTimeStart', DateTimeType::class, [
                'widget' => 'single_text'
            ])
            ->add('dateTimeEnd', DateTimeType::class, [
                'widget' => 'single_text'
            ])
            ->add('taskCategory', EntityType::class, [
                'class' => TaskCategory::class,
                'required' => true,
                'placeholder' => 'Choose a category',
                'choice_label' => 'name',
                'choice_attr' => function(TaskCategory $taskCategory) {
                    return [
                        'data-is-physical' => $taskCategory->getIsPhysical(),
                        'data-is-remote' => $taskCategory->getIsRemote()
                    ];
                }
            ])
            ->add('comment');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'translation_domain' => 'Task.forms',
        ]);
    }
}
