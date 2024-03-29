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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertyType extends AbstractType
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $request = $this->requestStack->getCurrentRequest();


        $builder
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
                    'data-property' => "category",
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
                    'class' => 'form-control',
                ],
                'label' => 'City',
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
                    'class' => 'form-control',
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
                    'class' => 'form-control',
                    'data-property' => "type"
                ],
                'required' => true,
                'label' => 'Type',
            ])
            ->add('propertyBedRooms', TextType::class, [
                'attr' => [
                    'placeholder' => 'Bed Rooms',
                    'class' => 'form-control',
                ],
                'label' => 'BedRooms',
                'required' => false,
            ])
            ->add('propertyBathRooms', TextType::class, [
                'attr' => [
                    'placeholder' => 'Bath Rooms',
                    'class' => 'form-control',
                ],
                'label' => 'BathRooms',
                'required' => false,
            ])
            ->add('squareType', ChoiceType::class, [
                'attr' => [
                    'placeholder' => 'select option',
                    'class' => 'form-control',
                ],
                'choices' => [
                    'select' => '',
                    'sq.ft.' => 'sq.ft.',
                    'sq.m.' => 'sq.m.',
                ],
                'label' => 'square Type',
            ])

            //            ->add('propertyIsGarage', CheckboxType::class, [
//                'attr' => [
//                    'placeholder' => 'Garage',
//                    'class' => 'form-check-input',
//                ],
//                'label' => 'Is Garage',
//                'required' => false,
//            ])
//            ->add('propertyCountry', countryType::class, [
//                'placeholder' => 'Choose an option',
//                'required' => true,
//                'choices' => [
//                    'Choose an option' => ''
//                ],
//                'preferred_choices' => ['IN'],
//                'data' => 'IN',
//                'attr' => [
//                    'class' => 'form-control'
//                ],
//                'label' => 'Country',
//            ])
//            ->add('propertyGarage', NumberType::class, [
//                'attr' => [
//                    'placeholder' => 'Number of garage',
//                    'class' => 'form-control',
//                    'data-change' => "class",
//                ],
//                'required' => false,
//                'label' => 'Garage',
//            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
        ]);

    }
}
