<?php


namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ProfileType extends AbstractType {

    private $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => false
            ])
            ->add('lastname', TextType::class, [
                'label' => false
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'options' => ['always_empty' => false],
                'first_options'  => ['label' => 'Password'],
                'second_options'  => ['label' => 'ConfirmPassword'],
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
            ])
            ->add('imageFile',FileType::class,[
                'required' => false,
                'label' => $this->security->getUser()->getFileName(),
                'attr' => [
                    'placeholder' => $this->security->getUser()->getFileName()
                ]
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