<?php

namespace App\Form\Profile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ProfileType
 *
 * @package App\Form\Profile
 */
class ProfileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $option
     */
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('email', Type\EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email()
                ]
            ])
            ->add('fname', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 100])
                ]
            ])
            ->add('lname', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 100])
                ]
            ])
            ->add('sname', Type\TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 100])
                ]
            ])
            ->add('phone', Type\TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 100])
                ]
            ])
            ->add('skype', Type\TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 100])
                ]
            ])
            ->add('password', Type\RepeatedType::class, [
                'required' => false,
                'type' => Type\PasswordType::class,
                'invalid_message' => 'Пароли не совпадают.',
                'constraints' => [
                    new Assert\Length(['min' => 8])
                ]
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => false
        ]);
    }
}