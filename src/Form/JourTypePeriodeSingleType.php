<?php

namespace App\Form;

use App\Entity\Axe1;
use App\Entity\Axe2;
use App\Entity\Axe3;
use App\Entity\JourTypePeriode;
use App\Entity\Section;
use App\Form\Type\SplitTimeType;
use App\Repository\Axe1Repository;
use App\Repository\Axe2Repository;
use App\Repository\Axe3Repository;
use App\Repository\SectionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JourTypePeriodeSingleType extends AbstractType
{
    public function __construct(
        private SectionRepository $sectionRepository,
        private Axe1Repository $axe1Repository,
        private Axe2Repository $axe2Repository,
        private Axe3Repository $axe3Repository
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('heureDebut', SplitTimeType::class, [
                'label' => 'Debut',
                'autofocus' => true,
            ])
            ->add('heureFin', SplitTimeType::class, [
                'label' => 'Fin',
            ])
            ->add('section', EntityType::class, [
                'class' => Section::class,
                'choice_label' => function (Section $section) {
                    return $section->getCode() . ' - ' . $section->getLibelle();
                },
                'query_builder' => fn(SectionRepository $repo) => $repo->createQueryBuilder('s')
                    ->where('s.actif = :actif')
                    ->setParameter('actif', true)
                    ->orderBy('s.ordre', 'ASC')
                    ->addOrderBy('s.libelle', 'ASC'),
                'placeholder' => '-- Choisir une section --',
                'label' => 'Section',
                'attr' => [
                    'class' => 'form-select w-full',
                ],
            ])
            ->add('commentaire', TextType::class, [
                'label' => 'Commentaire',
                'required' => false,
                'attr' => [
                    'class' => 'form-input w-full',
                ],
            ]);

        $formModifier = function (FormInterface $form, ?Section $section, ?Axe1 $axe1, ?Axe2 $axe2): void {
            // Axe1 : filtré par section si sélectionnée, sinon tous les actifs
            $axes1 = $section
                ? $this->axe1Repository->findBySection($section)
                : $this->axe1Repository->findAllActive();
            $form->add('axe1', EntityType::class, [
                'class' => Axe1::class,
                'choice_label' => function (Axe1 $axe1) {
                    return $axe1->getCode() . ' - ' . $axe1->getLibelle();
                },
                'choices' => $axes1,
                'placeholder' => '-- Choisir un axe 1 --',
                'required' => false,
                'label' => 'Axe 1',
                'attr' => [
                    'class' => 'form-select w-full',
                ],
            ]);

            // Axe2 : filtré par axe1 si sélectionné, sinon tous les actifs
            $axes2 = $axe1
                ? $this->axe2Repository->findByAxe1($axe1)
                : $this->axe2Repository->findAllActive();
            $form->add('axe2', EntityType::class, [
                'class' => Axe2::class,
                'choice_label' => function (Axe2 $axe2) {
                    return $axe2->getCode() . ' - ' . $axe2->getLibelle();
                },
                'choices' => $axes2,
                'placeholder' => '-- Choisir un axe 2 --',
                'required' => false,
                'label' => 'Axe 2',
                'attr' => [
                    'class' => 'form-select w-full',
                ],
            ]);

            // Axe3 : filtré par axe2 si sélectionné, sinon tous les actifs
            $axes3 = $axe2
                ? $this->axe3Repository->findByAxe2($axe2)
                : $this->axe3Repository->findAllActive();
            $form->add('axe3', EntityType::class, [
                'class' => Axe3::class,
                'choice_label' => function (Axe3 $axe3) {
                    return $axe3->getCode() . ' - ' . $axe3->getLibelle();
                },
                'choices' => $axes3,
                'placeholder' => '-- Choisir un axe 3 --',
                'required' => false,
                'label' => 'Axe 3',
                'attr' => [
                    'class' => 'form-select w-full',
                ],
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formModifier): void {
            $data = $event->getData();
            $formModifier(
                $event->getForm(),
                $data?->getSection(),
                $data?->getAxe1(),
                $data?->getAxe2()
            );
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($formModifier): void {
            $data = $event->getData();
            $section = isset($data['section']) && $data['section']
                ? $this->sectionRepository->find($data['section'])
                : null;
            $axe1 = isset($data['axe1']) && $data['axe1']
                ? $this->axe1Repository->find($data['axe1'])
                : null;
            $axe2 = isset($data['axe2']) && $data['axe2']
                ? $this->axe2Repository->find($data['axe2'])
                : null;

            $formModifier($event->getForm(), $section, $axe1, $axe2);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JourTypePeriode::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        // Use same prefix as PeriodeType so the API cascade selects work
        return 'periode';
    }
}
