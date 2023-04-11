<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('propertyIsGarage', CheckboxType::class, [
                'attr' => [
                    'placeholder' => 'Area',
                    'class' => 'form-check-input',
                ],
                'label' => 'Is Garage',
                'required' => false,
            ])

            ->add('propertyArea', TextType::class, [
                'attr' => [
                    'placeholder' => 'property Area',
                    'class' => 'form-control',
                ],
                'label' => 'Area',
            ])

            ->add('propertyCategory', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'choices' => [], //dynamic value display by id
                'label' => 'Category',
            ])

            ->add('propertyCity', TextType::class, [
                'attr' => [
                    'placeholder' => 'City',
                    'class' => 'form-control',
                ],
                'label' => 'City',
            ])

            ->add('propertyCountry', countryType::class, [
                'placeholder' => 'Choose an option',
                'required' => true,
                'choices' => [
                    'Choose an option' => ''
                ],
                'preferred_choices' => ['IN'],
                'data' => 'IN',
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Country',
            ])

            ->add('propertyDescription', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Description',
                    'class' => 'form-control',
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

            ->add('propertyGarage', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Number of garage',
                    'class' => 'form-control',
                    'data-change' => "class",
                ],
                'required' => false,
                'label' => 'Garage',
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
                    'class' => 'form-control',
                ],
                'label' => 'Price',
            ])

            ->add('propertyRooms', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Rooms',
                    'class' => 'form-control',
                ],
                'label' => 'Rooms',
                'required' => false,
            ])

            ->add('propertyState', TextType::class, [
                'attr' => [
                    'placeholder' => 'State',
                    'class' => 'form-control',
                ],
                'label' => 'State',
            ])

            ->add('propertyStatus', ChoiceType::class, [
                'placeholder' => 'Select option',
                'attr' => [
                    'class' => 'form-control',
                ],
                'choices' => [
                    'For Sale' => 'sale',
                    'For Rent' => 'rent',
                ],
                'label' => 'Status',
            ])

            ->add('propertyTitle', TextType::class, [
                'attr' => [
                    'placeholder' => 'Title',
                    'class' => 'form-control',
                ],
                'label' => 'Title',
            ])

            ->add('propertyType', ChoiceType::class, [
                'choices' => [
                    'Residential' => 'residential',
                    'Commercial' => 'commercial',
                    'Industrial' => 'industrial',
                    'Agricultural' => 'agricultural',
                    'Vacant land' => 'vacant_land',
                    'Special purpose' => 'special_purpose',
                ],
                'placeholder' => 'Select option',
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => true,
                'label' => 'Type',
            ])

            ->add('roomBed', ChoiceType::class, [
                'placeholder' => 'Select option',
                'attr' => [

                    'class' => 'form-control',
                ],
                'choices' => [
                    '1 Bed /Room' => '1 Bed /Room',
                    '2 Bed /Room' => '2 Bed /Room',
                    '3 Bed /Room' => '3 Bed /Room',
                    '4 Bed /Room' => '4 Bed /Room',
                ],
                'label' => 'Bed',
                'required' => false,
            ])

            ->add('squareType', ChoiceType::class, [
                'attr' => [
                    'placeholder' => 'select option',
                    'class' => 'form-control',
                ],
                'choices' => [
                    'select' => '',
                    'sq ft' => 'sq ft',
                    'm²' => 'm2',
                ],
                'label' => 'square Type',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
