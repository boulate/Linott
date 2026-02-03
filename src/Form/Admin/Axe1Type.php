<?php

namespace App\Form\Admin;

use App\Entity\Axe1;
use App\Entity\Section;
use App\Repository\SectionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Axe1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isIndependent = $options['is_independent'];

        // Only show parent field if NOT independent
        if (!$isIndependent) {
            $builder->add('section', EntityType::class, [
                'class' => Section::class,
                'choice_label' => fn(Section $s) => $s->getCode() . ' - ' . $s->getLibelle(),
                'query_builder' => fn(SectionRepository $repo) => $repo->createQueryBuilder('s')
                    ->where('s.actif = :actif')
                    ->setParameter('actif', true)
                    ->orderBy('s.ordre', 'ASC'),
                'label' => 'Section',
                'attr' => ['class' => 'form-select'],
            ]);
        }

        $builder->add('code', TextType::class, [
                'label' => 'Code',
                'attr' => ['class' => 'form-input', 'maxlength' => 10],
            ])
            ->add('libelle', TextType::class, [
                'label' => 'Libellé',
                'attr' => ['class' => 'form-input'],
            ])
            ->add('ordre', IntegerType::class, [
                'label' => 'Ordre d\'affichage',
                'attr' => ['class' => 'form-input'],
            ])
            ->add('actif', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Axe1::class,
            'is_independent' => false,
        ]);
    }
}
