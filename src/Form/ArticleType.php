<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Title', TextType::class, [
                'label' => 'Title',
                'attr' => [
                    'placeholder' => 'Title'
                ]
            ])
            ->add('Description', TextType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Description'
                ]
            ])
            ->add('Content', TextType::class, [
                'label' => 'Content',
                'attr' => [
                    'placeholder' => 'Content'
                ]
            ])
            ->add('Quantity', NumberType::class, [
                'label' => 'Quantity',
                'attr' => [
                    'placeholder' => 'Quantity'
                ]
            ])
            ->add('Size', ChoiceType::class, [
                'label' => 'Size',
                'choices' => [
                    'Small' => 'Small',
                    'Medium' => 'Medium',
                    'Large' => 'Large',
                    'Extra Large' => 'XL',
                    'Double Extra Large' => 'XXL',
                    'Uniform' => 'Normal'
                ],
                'multiple' => false,
                'expanded' => true,
                'attr' => [
                    'placeholder' => 'Size'
                ]
            ])
            ->add('Color', ChoiceType::class, [
                'label' => 'Color',
                'choices' => [
                    'Red' => 'Red',
                    'Blue' => 'Blue',
                    'Green' => 'Green',
                    'Yellow' => 'Yellow',
                    'Orange' => 'Orange',
                    'Purple' => 'Purple',
                    'Black' => 'Black',
                    'White' => 'White',
                    'Brown' => 'Brown',
                    'Gray' => 'Gray',
                    'Pink' => 'Pink',
                    'Gold' => 'Gold',
                    'Silver' => 'Silver',
                    'Copper' => 'Copper',
                    'Bronze' => 'Bronze',
                    'Multi' => 'Multi'
                ],
                'multiple' => false,
                'expanded' => true,
                'attr' => [
                    'placeholder' => 'Color'
                ]
            ])
            ->add('Category', ChoiceType::class, [
                'label' => 'Category',
                'choices' => [
                    'Shirt' => 'Shirt',
                    'Pants' => 'Pants',
                    'Shoes' => 'Shoes',
                    'Accessories' => 'Accessories',
                    'Pull' => 'Pull',
                    'Tee-Shirt' => 'Tee-Shirt',
                ],
                'multiple' => false,
                'expanded' => true,
                'attr' => [
                    'placeholder' => 'Category'
                ]
            ])
            ->add('Price', NumberType::class, [
                'label' => 'Price',
                'attr' => [
                    'placeholder' => 'Price'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
