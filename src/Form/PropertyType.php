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
            ->add('propertyAdvance', CheckboxType::class, [
                'attr' => [
                    'placeholder' => 'Area',
                    'class' => 'form-check-input',
                ],
                'required' => false,
            ])

            ->add('propertyArea', TextType::class, [
                'attr' => [
                    'placeholder' => 'Area',
                    'class' => 'form-control',
                ],
            ])

            ->add('propertyCategory', ChoiceType::class, [
                'attr' => [
                    'placeholder' => 'Area',
                    'class' => 'form-control',
                ],
                'choices' => [
                    'Open this select menu' => '',
                    'For Sale' => 'sale',
                    'For Rent' => 'rent',
                ]
            ])

            ->add('propertyCity', TextType::class, [
                'attr' => [
                    'placeholder' => 'City',
                    'class' => 'form-control',
                ],
            ])

            ->add('propertyCountry', countryType::class, [
                'placeholder' => 'Choose an option',
                'required' => true,
                'disabled' => true,
                'choices' => [
                    'Choose an option' => ''
                ],
                'preferred_choices' => ['IN'],
                'data' => 'IN',
                'attr' => [
                    'class' => 'form-control'
                ],
            ])

            ->add('propertyDescription', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Description',
                    'class' => 'form-control',
                ],
            ])

            ->add('propertyGarage', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Number of garage',
                    'class' => 'form-control',
                ],
                'required' => false,
            ])

            ->add('propertyImage', FileType::class, [
                'label' => 'Property Image',
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
            ])

            ->add('propertyRooms', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Rooms',
                    'class' => 'form-control',
                ],
            ])

            ->add('propertyState', TextType::class, [
                'attr' => [
                    'placeholder' => 'State',
                    'class' => 'form-control',
                ],
            ])

            ->add('propertyTitle', TextType::class, [
                'attr' => [
                    'placeholder' => 'Title',
                    'class' => 'form-control',
                ],
            ])

            ->add('propertyType', ChoiceType::class, [
                'attr' => [
                    'placeholder' => 'Type',
                    'class' => 'form-control',
                ],

                'choices' => [
                    'Open this select menu' => '',
                    'Home' => 'home',
                    'Shop' => 'shop',
                    'Garage' => 'garage',
                    'Villa' => 'villa',
                    'Office' => 'office',
                    'Building' => 'building',
                    'Town House' => 'townhouse',
                    'Apartment' => 'apartment',

                ],
            ])

            ->add('roomBed', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Room Bed',
                    'class' => 'form-control',
                ],
            ])

            ->add('shortDescription', TextType::class, [
                'attr' => [
                    'placeholder' => 'Short Description',
                    'class' => 'form-control',
                ],
            ])

            ->add('squareType', ChoiceType::class, [
                'attr' => [
                    'placeholder' => 'select option',
                    'class' => 'form-control',
                ],
                'choices' => [
                    'select' => '',
                    'sq ft' => 'sq_ft',
                    'm²' => 'm2',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
