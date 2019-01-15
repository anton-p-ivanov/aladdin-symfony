<?php

namespace App\Form\Partner;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as Type;

/**
 * Class BusinessType
 *
 * @package App\Form\Partner
 */
class BusinessType extends PartnerType
{
    /**
     * @var string
     */
    protected $type = 'business';

    /**
     * @param FormBuilderInterface $builder
     * @param array $option
     */
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        parent::buildForm($builder, $option);

        $builder
            ->add('product', Type\ChoiceType::class, [
                'required' => false,
                'choices' => array_flip($this->getProducts())
            ])
            ->add('statuses', Type\ChoiceType::class, [
                'required' => false,
                'choices' => array_flip($this->getStatuses())
            ])
            ->add('method', Type\ChoiceType::class, [
                'required' => false,
                'choices' => array_flip($this->getMethods())
            ])
            ->add('recommended', Type\CheckboxType::class, [
                'required' => false,
                'label' => 'Только рекомендованные партнёры'
            ]);

        $builder->get('recommended')->addModelTransformer(new CallbackTransformer(
            function (?string $value) { return (bool) $value; },
            function (bool $value) { return $value; })
        );
    }

    /**
     * @return array
     */
    private function getProducts(): array
    {
        return [
            39536 => 'JaCarta',
            42154 => 'JaCarta Management System (JMS)',
            44535 => 'JaCarta Authentication Server (JAS)',
            4987 => 'Secret Disk',
            24464 => 'Secret Disk Server',
            4989 => '"Крипто БД"',
            4993 => 'Карт-ридеры ASEDrive',
            4997 => 'iButton',
        ];
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return [
            4999 => 'Только продажа',
            5001 => 'Продажа и внедрение',
            5003 => 'Внедрение только в составе проектов'
        ];
    }

    /**
     * @return array
     */
    public function getStatuses(): array
    {
        return [
            4972 => 'Серебряный партнёр',
            4974 => 'Золотой партнёр',
            4970 => 'Платиновый партнёр',
            4976 => 'Реселлер',
            31272 => 'Премиум реселлер',
            5060 => 'Дилер',
            4968 => 'Дистрибьютор',
            31282 => 'Сервисный партнёр',
        ];
    }
}