<?php

namespace App\Form;

use App\Entity\JourType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JourTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du modele',
                'attr' => [
                    'class' => 'form-input',
                    'placeholder' => 'Ex: Journee bureau standard',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-input',
                    'rows' => 2,
                    'placeholder' => 'Description optionnelle du modele',
                ],
            ])
            ->add('ordre', IntegerType::class, [
                'label' => 'Ordre d\'affichage',
                'required' => false,
                'attr' => [
                    'class' => 'form-input',
                    'min' => 0,
                ],
                'empty_data' => '0',
            ])
            ->add('actif', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
                'attr' => ['class' => 'form-checkbox'],
            ]);

        // Note: periodes are managed via HTMX, not embedded in this form

        if ($options['is_admin']) {
            $builder->add('partage', CheckboxType::class, [
                'label' => 'Partager avec tous les utilisateurs',
                'required' => false,
                'attr' => ['class' => 'form-checkbox'],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JourType::class,
            'is_admin' => false,
        ]);
    }
}
