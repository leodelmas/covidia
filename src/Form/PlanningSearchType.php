<?php

namespace App\Form;

use App\Entity\PlanningSearch;
use App\Entity\TaskCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanningSearchType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('taskCategories', EntityType::class, [
                'required'      => false,
                'label'         => false,
                'class'         => TaskCategory::class,
                'choice_label'  => 'name',
                'multiple'      => true
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class'      => PlanningSearch::class,
            'method'          => 'get',
            'csrf_protection' => false
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string {
        return '';
    }
}
