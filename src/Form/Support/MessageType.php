<?php

namespace App\Form\Support;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class MessageType
 *
 * @package App\Form\Support
 */
class MessageType extends AbstractType
{
    /**
     * @var array
     */
    public static $subjects = [
        'Дополнительная информация к обращению' => 'Дополнительная информация к обращению',
        'Закрытие обращения' => 'Закрытие обращения',
    ];

    /**
     * @param FormBuilderInterface $builder
     * @param array $option
     */
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('subject', Type\ChoiceType::class, [
                'choices' => self::$subjects,
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('text', Type\TextareaType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 1000])
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