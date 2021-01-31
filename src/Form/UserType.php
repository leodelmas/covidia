<?php

namespace App\Form;

use App\Entity\Job;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname')
            ->add('lastname')
            ->add('password', PasswordType::class)
            ->add('birthDate', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('hiringDate', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('email')
            ->add('phone')
            ->add('isExecutive')
            ->add('isAdmin')
            ->add('job', EntityType::class, [
                'class' => Job::class,
                'required' => true,
                'choice_label' => 'name'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'Users.forms',
        ]);
    }
}
