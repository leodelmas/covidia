<?php


namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => false
            ])
            ->add('lastname', TextType::class, [
                'label' => false
            ])
            ->add('password', PasswordType::class, [
                'label' => false
            ])
            ->add('birthDate', DateType::class, [
                'label' => false,
                'widget' => 'single_text'
            ])
            ->add('email', EmailType::class, [
                'label' => false
            ])
            ->add('phone', TextType::class, [
                'label' => false
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