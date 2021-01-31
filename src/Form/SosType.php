<?php

namespace App\Form;

use App\Entity\Sos;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sujet')
            ->add('email', ChoiceType::class, [
                'choices' => $this->getChoices()
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sos::class,
        ]);
    }

    private function getChoices()
    {
        $choices = Sos::TYPE_EMAIL;
        $output = [];
        foreach ($choices as $k => $v)
        {
            $output[$v] = $k;
        }

        return $output;
    }
}
