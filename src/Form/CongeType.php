<?php

namespace App\Form;

use App\Entity\Conge;
use App\Entity\TypeConge;
use App\Repository\TypeCongeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CongeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', EntityType::class, [
                'class' => TypeConge::class,
                'choice_label' => 'libelle',
                'query_builder' => fn(TypeCongeRepository $repo) => $repo->createQueryBuilder('t')
                    ->where('t.actif = :actif')
                    ->setParameter('actif', true)
                    ->orderBy('t.libelle', 'ASC'),
                'placeholder' => '-- Choisir un type --',
                'label' => 'Type de conge',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Date de debut',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-input'],
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-input'],
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Commentaire',
                'required' => false,
                'attr' => [
                    'class' => 'form-input',
                    'rows' => 3,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Conge::class,
        ]);
    }
}
