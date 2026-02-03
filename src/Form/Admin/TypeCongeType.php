<?php

namespace App\Form\Admin;

use App\Entity\TypeConge;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeCongeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'Code',
                'attr' => ['class' => 'form-input', 'maxlength' => 10],
            ])
            ->add('libelle', TextType::class, [
                'label' => 'Libellé',
                'attr' => ['class' => 'form-input'],
            ])
            ->add('couleur', ColorType::class, [
                'label' => 'Couleur',
                'attr' => ['class' => 'form-input h-10'],
            ])
            ->add('decompte', CheckboxType::class, [
                'label' => 'Décompte du solde',
                'required' => false,
                'help' => 'Ce type de congé est-il décompté du solde annuel ?',
            ])
            ->add('actif', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TypeConge::class,
        ]);
    }
}
