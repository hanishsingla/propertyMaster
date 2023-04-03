<?php

namespace App\Form\Information;

use App\Entity\Information\UserInformation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserInformationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'name'
                ],
            ])

            ->add('gender', ChoiceType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'choices' => [
                    'Choose an option' => '',
                    'Male' => "male",
                    'Female' => 'female',
                    'other' => 'other',
                ],
            ])

            ->add('image', FileType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Images',
                'mapped' => false,
                'multiple' => false,
                'required' => false,
            ])

            ->add('phone',NumberType::class,[
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])

            ->add('mobile',NumberType::class,[
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])

            ->add('country', countryType::class, [
                'placeholder' => 'Choose an option',
                'required' => false,
                'choices' => [
                    'Choose an option' => ''
                ],
                'preferred_choices' => ['IN'],
                'data' => 'IN',
                'attr' => [
                    'class' => 'form-control'
                ],
                'label'=> 'Country',
            ])


            ->add('address' ,TextType::class,[
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
            ])


            ->add('address2' ,TextType::class,[
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
            ])

            ->add('city' ,TextType::class,[
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
            ])

            ->add('zip' ,TextType::class,[
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
            ])

            ->add('state' ,TextType::class,[
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserInformation::class,
        ]);
    }
}