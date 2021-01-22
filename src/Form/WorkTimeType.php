<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\WorkTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkTimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateStart', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('dateEnd', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('isTeleworked')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'required' => true,
                'choice_label' => 'email'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WorkTime::class,
        ]);
    }
}
