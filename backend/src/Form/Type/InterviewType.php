<?php

namespace App\Form\Type;

use App\Entity\Candidate;
use App\Entity\Evaluator;
use App\Entity\Interview;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('interview_location', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Location',
                ],
                'label' => 'Interview Location',
                'label_attr' => ['class' => 'form-label fw-bold'],
            ])
            ->add('candidate', EntityType::class, [
                'class' => Candidate::class,
                'choice_label' => 'fullName',
                'attr' => ['class' => 'form-control mb-3'],
                'label' => 'Candidate',
                'label_attr' => ['class' => 'form-label fw-bold'],
            ])
            ->add('evaluators', EntityType::class, [
                'class' => Evaluator::class,
                'choice_label' => 'fullName',
                'attr' => [
                    'class' => 'chosen-select form-control mb-3',
                    'multiple' => 'multiple',
                    'data-placeholder' => 'Select evaluators',
                ],
                'label' => 'Evaluators',
                'label_attr' => ['class' => 'form-label fw-bold'],
                'multiple' => true,
            ])
            ->add('interview_date', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control mb-3'],
                'label' => 'Interview Date',
                'label_attr' => ['class' => 'form-label fw-bold'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Interview::class,
        ]);
    }
}