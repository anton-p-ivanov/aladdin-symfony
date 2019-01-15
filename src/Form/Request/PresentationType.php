<?php

namespace App\Form\Request;

use App\Validator\Constraints\ReCaptcha;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PresentationType
 *
 * @package App\Form\Request
 */
class PresentationType extends AbstractType
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
            ->add('fullName', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('phone', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('product', Type\ChoiceType::class, [
                'choices' => $this->getProducts()
            ])
            ->add('captcha', Type\HiddenType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new ReCaptcha()
                ]
            ])
            ->add('company', Type\TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('position', Type\TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('comments', Type\TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 2000])
                ]
            ])
            ->add('agreement', Type\CheckboxType::class, [
                'constraints' => [
                    new Assert\EqualTo(['value' => 1, 'message' => 'Вы должны принять условия использования сайта.'])
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

    /**
     * @return array
     */
    protected function getProducts(): array
    {
        return [];
    }
}