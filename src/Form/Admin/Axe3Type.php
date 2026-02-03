<?php

namespace App\Form\Admin;

use App\Entity\Axe2;
use App\Entity\Axe3;
use App\Repository\Axe2Repository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Axe3Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('axe2', EntityType::class, [
                'class' => Axe2::class,
                'choice_label' => fn(Axe2 $a) => $a->getAxe1()->getSection()->getCode() . ' > ' . $a->getAxe1()->getCode() . ' > ' . $a->getCode() . ' - ' . $a->getLibelle(),
                'query_builder' => fn(Axe2Repository $repo) => $repo->createQueryBuilder('a')
                    ->join('a.axe1', 'a1')
                    ->join('a1.section', 's')
                    ->where('a.actif = :actif')
                    ->setParameter('actif', true)
                    ->orderBy('s.ordre', 'ASC')
                    ->addOrderBy('a1.ordre', 'ASC')
                    ->addOrderBy('a.ordre', 'ASC'),
                'label' => 'Axe 2',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('code', TextType::class, [
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
            'data_class' => Axe3::class,
        ]);
    }
}
