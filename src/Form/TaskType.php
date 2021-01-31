<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\TaskCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateTimeStart', DateTimeType::class, [
                'widget' => 'single_text'
            ])
            ->add('dateTimeEnd', DateTimeType::class, [
                'widget' => 'single_text'
            ])
            ->add('taskCategory', EntityType::class, [
                'class' => TaskCategory::class,
                'required' => true,
                'choice_label' => 'name'
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
