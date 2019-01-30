<?php

namespace App\Form\Support;

use App\Validator\Constraints\ReCaptcha;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TicketType
 *
 * @package App\Form\Support
 */
class TicketType extends AbstractType
{
    /**
     * @var array
     */
    public static $drivers = [
        'Единый Клиент JaCarta' => 'Единый Клиент JaCarta',
        'JC-Client' => 'JC-Client',
        'JC-WebClient' => 'JC-WebClient',
        'eToken PKI Client' => 'eToken PKI Client',
        'SafeNet Authentication Client' => 'SafeNet Authentication Client',
    ];

    /**
     * @var array
     */
    public static $products = [
        'JaCarta' => [
            'JaCarta PKI' => 'JaCarta PKI',
            'JaCarta PKI/BIO' => 'JaCarta PKI/BIO',
            'JaCarta PKI/ГОСТ' => 'JaCarta PKI/ГОСТ',
            'JaCarta PKI/Flash' => 'JaCarta PKI/Flash',
            'JaCarta PKI/ГОСТ/Flash' => 'JaCarta PKI/ГОСТ/Flash',
            'JaCarta ГОСТ' => 'JaCarta ГОСТ',
            'JaCarta ГОСТ/Flash' => 'JaCarta ГОСТ/Flash',
            'JaCarta PRO' => 'JaCarta PRO',
            'JaCarta PRO/ГОСТ' => 'JaCarta PRO/ГОСТ',
            'JaCarta WebPass' => 'JaCarta WebPass',
            'JaCarta U2F' => 'JaCarta U2F',
            'JaCarta LT' => 'JaCarta LT',
            'JaCarta SF/ГОСТ' => 'JaCarta SF/ГОСТ'
        ],
        'eToken' => [
            'eToken PRO (Java)' => 'eToken PRO (Java)',
            'eToken ГОСТ' => 'eToken ГОСТ',
            'eToken NG-FLASH (Java)' => 'eToken NG-FLASH (Java)',
            'eToken NG-OTP (Java)' => 'eToken NG-OTP (Java)',
            'eToken PASS' => 'eToken PASS'
        ],
        'Secret Disk' => [
            'Secret Disk 5' => 'Secret Disk 5',
            'Secret Disk 4' => 'Secret Disk 4',
            'Secret Disk 4 Workgroup Edition' => 'Secret Disk 4 Workgroup Edition',
            'Secret Disk Server' => 'Secret Disk Server',
            'Secret Disk Enterprise' => 'Secret Disk Enterprise',
        ],
        'Системы управления жизненным циклом' => [
            'JaCarta Management System' => 'JaCarta Management System',
            'SafeNet Authentication Manager' => 'SafeNet Authentication Manager',
        ],
        'Другие продукты' => [
            '"Крипто БД"' => '"Крипто БД"',
            'Прочие продукты' => 'Прочие продукты'
        ]
    ];

    /**
     * @var array
     */
    public static $oses = [
        'Microsoft Windows' => 'Microsoft Windows',
        'Linux' => 'Linux',
        'Mac OS' => 'Mac OS'
    ];

    /**
     * @param FormBuilderInterface $builder
     * @param array $option
     */
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('fname', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('lname', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('email', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email()
                ]
            ])
            ->add('phone', Type\TextType::class, [
                'constraints' => [
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('company', Type\TextType::class, [
                'constraints' => [
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('product', Type\ChoiceType::class, [
                'choices' => self::$products,
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('driver', Type\ChoiceType::class, [
                'choices' => self::$drivers,
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('version', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('partner', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('os', Type\ChoiceType::class, [
                'choices' => self::$oses,
                'expanded' => true,
                'data' => 'Microsoft Windows',
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('linux_distributive', Type\TextType::class, [
                'constraints' => [
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('linux_kernel', Type\TextType::class, [
                'constraints' => [
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('subject', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('content', Type\TextareaType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 1000])
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
                    new Assert\EqualTo(['value' => 1, 'message' => 'Вы должны принять Правила оказания услуг Технической поддержки.'])
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