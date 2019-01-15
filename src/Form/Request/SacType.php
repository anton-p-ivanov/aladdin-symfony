<?php

namespace App\Form\Request;

use App\Validator\Constraints\ReCaptcha;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class SacType
 *
 * @package App\Form\Request
 */
class SacType extends AbstractType
{
    /**
     * @var string
     */
    public static $form_uuid = "83f07e77-4dfe-4400-ab49-3b7ae057e656";

    /**
     * @param FormBuilderInterface $builder
     * @param array $option
     */
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('form_field_email', Type\EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email()
                ]
            ])
            ->add('form_field_first_name', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('form_field_last_name', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('form_field_company_name', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('form_field_company_position', Type\TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('captcha', Type\HiddenType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new ReCaptcha()
                ]
            ])
            ->add('agreement', Type\CheckboxType::class, [
                'constraints' => [
                    new Assert\EqualTo(['value' => 1, 'message' => 'Вы должны принять условия скачивания программного обеспечения.'])
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