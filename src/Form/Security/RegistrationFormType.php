<?php

namespace App\Form\Security;

use App\Entity\Security\User;
use App\Form\Information\UserAddressType;
use App\Form\UserInformation\UserInformationFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isAgent',CheckboxType::class,[
                'attr' => [
                    'class' => 'form-check-input',
                ],
                'label'=> 'Agent'
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'email',
                    'autocomplete' => 'email'
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'data' => true,
                'attr' => [
                    'class' => 'form-check-input',

                ],
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                    'label' => 'Password',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'password',
                    ]
                ],

                'second_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'form-control',
                        'placeholder' => 'confirm-password'
                    ],
                    'label' => 'Confirm-password',
                ],
                'invalid_message' => 'The password fields must match.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])

            ->add('userAddress', UserAddressType::class);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
