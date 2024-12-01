<?php

namespace App\Form\Property;

use App\Entity\Property\Property;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('propertyArea', TextType::class, [
                'attr' => [
                    'placeholder' => 'property Area',
                ],
                'label' => 'Area',
            ])
            ->add('propertyCategory', ChoiceType::class, [
                'attr' => [
                    'data-property' => 'category',
                ],
                'placeholder' => 'Select option',
                'choices' => [
                    'Villa' => 'villa',
                    'Apartment' => 'apartment',
                    'Floor' => 'floor',
                    'Office' => 'office',
                    'Shop' => 'Shop',
                    'Hotel' => 'hotel',
                    'Warehouse' => 'warehouse',
                    'Agricultural/ Farm Land' => 'agricultural_farm_land',
                ],

                'label' => 'Category',
            ])
            ->add('propertyCity', TextType::class, [
                'attr' => [
                    'placeholder' => 'City',
                ],
                'label' => 'City',
            ])
            ->add('propertyDescription', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Description',
                ],
                'label' => 'Description',
            ])
            ->add('propertyDirection', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'choices' => [
                    'North' => 'north',
                    'South' => 'south',
                    'East' => 'east',
                    'West' => 'west',
                ],
                'placeholder' => 'Select option',
                'label' => 'Direction',
            ])
            ->add('propertyImage', FileType::class, [
                'label' => 'Images',
                'mapped' => false,
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('propertyPrice', TextType::class, [
                'attr' => [
                    'placeholder' => 'Price',
                ],
                'label' => 'Price',
            ])
            ->add('propertyRooms', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Rooms',
                ],
                'label' => 'Rooms',
                'required' => false,
            ])
            ->add('propertyState', TextType::class, [
                'attr' => [
                    'placeholder' => 'State',
                ],
                'label' => 'State',
            ])
            ->add('propertyStatus', ChoiceType::class, [
                'choices' => [
                    'Sale' => 'sale',
                    'Rent' => 'rent',
                ],
                'choice_attr' => function () {
                    return ['class' => 'mx-2'];
                },
                'data' => 'sale',
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('propertyTitle', TextType::class, [
                'attr' => [
                    'placeholder' => 'Title',
                ],
                'label' => 'Title',
            ])
            ->add('propertyType', ChoiceType::class, [
                'choices' => [
                    'Residential' => 'residential',
                    'Commercial' => 'commercial',
                ],
                'placeholder' => 'Select option',
                'attr' => [
                    'data-property' => 'type',
                ],
                'required' => true,
                'label' => 'Type',
            ])
            ->add('propertyBedRooms', TextType::class, [
                'attr' => [
                    'placeholder' => 'Bed Rooms',
                ],
                'label' => 'BedRooms',
                'required' => false,
            ])
            ->add('propertyBathRooms', TextType::class, [
                'attr' => [
                    'placeholder' => 'Bath Rooms',
                ],
                'label' => 'BathRooms',
                'required' => false,
            ])
            ->add('squareType', ChoiceType::class, [
                'attr' => [
                    'placeholder' => 'select option',
                ],
                'choices' => [
                    'select' => '',
                    'sq.ft.' => 'sq.ft.',
                    'sq.m.' => 'sq.m.',
                ],
                'label' => 'square Type',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
        ]);
    }
}
