<?php

namespace App\Form\Admin;

use App\Entity\Equipe;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'Code',
                'attr' => [
                    'class' => 'form-input font-mono uppercase',
                    'maxlength' => 50,
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-input'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-input',
                    'rows' => 3,
                ],
            ])
            ->add('couleur', ChoiceType::class, [
                'label' => 'Couleur',
                'required' => false,
                'placeholder' => 'Aucune',
                'choices' => array_combine(
                    array_map(fn($c) => $c['label'], Equipe::COULEURS),
                    array_keys(Equipe::COULEURS)
                ),
            ])
            ->add('actif', CheckboxType::class, [
                'label' => 'Active',
                'required' => false,
            ])
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => fn(User $user) => $user->getFullName() . ' (' . $user->getEmail() . ')',
                'query_builder' => fn(UserRepository $repo) => $repo->createQueryBuilder('u')
                    ->where('u.actif = :actif')
                    ->setParameter('actif', true)
                    ->orderBy('u.nom', 'ASC')
                    ->addOrderBy('u.prenom', 'ASC'),
                'label' => 'Membres',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}
