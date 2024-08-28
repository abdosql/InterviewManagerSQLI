<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FroalaEditorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comment', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Location',
                    'class' => 'froala-editor appreciation-contents',

                ],
                'label_attr' => ['class' => 'form-label fw-bold pt-3'],
            ])
            ->add('score', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control mb-3 appreciation-score',
                    'placeholder' => 'Score',
                ],
                'label_attr' => ['class' => 'form-label fw-bold pt-4'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'label' => null,
            'compound' => true,  // Allows adding child fields
        ]);
    }
}