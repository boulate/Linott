<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class SplitTimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('hour', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'split-time-hour',
                    'min' => 0,
                    'max' => 23,
                    'placeholder' => 'HH',
                    'autocomplete' => 'off',
                ],
            ])
            ->add('minute', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'split-time-minute',
                    'min' => 0,
                    'max' => 59,
                    'placeholder' => 'MM',
                    'autocomplete' => 'off',
                ],
            ]);

        $builder->addModelTransformer(new CallbackTransformer(
            // Transform DateTimeImmutable to array
            function ($dateTime) {
                if ($dateTime === null) {
                    return ['hour' => null, 'minute' => null];
                }

                return [
                    'hour' => (int) $dateTime->format('G'),
                    'minute' => (int) $dateTime->format('i'),
                ];
            },
            // Transform array to DateTimeImmutable
            function ($data) {
                if (!isset($data['hour']) || $data['hour'] === null || $data['hour'] === '') {
                    return null;
                }

                $hour = (int) $data['hour'];
                $minute = isset($data['minute']) && $data['minute'] !== null && $data['minute'] !== ''
                    ? (int) $data['minute']
                    : 0;

                return \DateTimeImmutable::createFromFormat('G:i', sprintf('%d:%02d', $hour, $minute));
            }
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['autofocus'] = $options['autofocus'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound' => true,
            'autofocus' => false,
        ]);

        $resolver->setAllowedTypes('autofocus', 'bool');
    }

    public function getBlockPrefix(): string
    {
        return 'split_time';
    }
}
