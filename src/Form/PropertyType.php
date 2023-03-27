<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('propertyTitle', TextType::class, [
                'attr' => [
                    'placeholder' => 'Title',
                    'class' => 'form-control',
                ],
            ])
            ->add('propertyPrice', TextType::class, [
                'attr' => [
                    'placeholder' => 'Price',
                    'class' => 'form-control',
                ],
            ])
            ->add('propertyArea', TextType::class, [
                'attr' => [
                    'placeholder' => 'Area',
                    'class' => 'form-control',
                ],
            ])
            ->add('propertyAddress', TextType::class, [
                'attr' => [
                    'placeholder' => 'Address',
                    'class' => 'form-control',
                ],
            ])
            ->add('shortDescription', TextType::class, [
                'attr' => [
                    'placeholder' => 'Short Description',
                    'class' => 'form-control',
                ],
            ])
            ->add('propertyDescription', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Description',
                    'class' => 'form-control',
                ],
            ])
            ->add('propertyRooms', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Rooms',
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
            ->add('propertyStatus', ChoiceType::class, [
                'choices' => [
                    'Open this select menu' => '',
                    'For Sale' => 'sale',
                    'For Rent' => 'rent',
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('propertyImage', FileType::class, [
                'label' => 'Property Image',
                'mapped' => false,
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
