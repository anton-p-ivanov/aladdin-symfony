<?php

namespace App\Form\Profile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PasswordType
 *
 * @package App\Form\Profile
 */
class PasswordType extends AbstractType
{
    const CODE_MAX_LENGTH = 8;

    /**
     * @param FormBuilderInterface $builder
     * @param array $option
     */
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('code', Type\TextType::class, [
                'attr' => ['maxlength' => self::CODE_MAX_LENGTH],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => self::CODE_MAX_LENGTH, 'max' => self::CODE_MAX_LENGTH])
                ]
            ])
            ->add('password', Type\RepeatedType::class, [
                'type' => Type\PasswordType::class,
                'invalid_message' => 'Пароли не совпадают.'
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