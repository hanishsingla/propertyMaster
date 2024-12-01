<?php

namespace App\Form\Security;

use App\Entity\Security\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => 'name',
                ],
            ])

            ->add('image', FileType::class, [
                'label' => 'Images',
                'mapped' => false,
                'multiple' => false,
                'required' => false,
            ])
            ->add('phone', NumberType::class)
            ->add('mobile', NumberType::class)

            ->add('address', TextType::class)
            ->add('address2', TextType::class)
            ->add('city', TextType::class)
            ->add('state', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'required' => false,
        ]);
    }
}
