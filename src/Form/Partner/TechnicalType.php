<?php

namespace App\Form\Partner;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as Type;

/**
 * Class TechnicalType
 *
 * @package App\Form\Partner
 */
class TechnicalType extends PartnerType
{
    /**
     * @var string
     */
    protected $type = 'technical';

    /**
     * @param FormBuilderInterface $builder
     * @param array $option
     */
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        parent::buildForm($builder, $option);

        $builder
            ->add('categories', Type\ChoiceType::class, [
                'required' => false,
                'choices' => array_flip($this->getCategories())
            ]);
    }

    /**
     * @return array
     */
    public function getCountries(): array
    {
        return [
            1 => "Россия",
        ];
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        return [
            '8162' => 'Web-серверы, порталы',
            '37592' => 'Автоматизация операционной, учетной и управленческой деятельности банка и оптимизация выбранной кредитной организацией операционной модели',
            '8150' => 'Аутентификация в сети, Single Sign-On',
            '37418' => 'Аутентификация пользователей',
            '43322' => 'Документооборот',
            '8154' => 'Защита каналов, беспроводных сетей, VPN, Firewall',
            '36542' => 'Защита от несанкционированного доступа к информации',
            '8152' => 'Защита персональных данных',
            '35668' => 'Защищённая почта для iPad',
            '8164' => 'Защищенный доступ к бизнес-приложениям и ERP-системам',
            '36876' => 'Модули для организации банком обслуживания физических и юридических лиц через Интернет',
            '43321' => 'Облачная МИС для частных клиник',
            '8156' => 'Работа с Удостоверяющими центрами',
            '36877' => 'Разработка СКЗИ',
            '23080' => 'Разработка средств криптографической защиты информации (СКЗИ)',
            '37700' => 'Решения для коллегиальных органов управления',
            '36875' => 'Система дистанционного банковского обслуживания',
            '8168' => 'Системная интеграция',
            '33369' => 'Считыватели смарт-карт',
            '8166' => 'Терминальный доступ, тонкие клиенты',
            '36892' => 'Удостоверяющий центр',
            '37419' => 'Хранение ключей и сертификатов пользователей в защищенной памяти устройства',
            '8158' => 'Шифрование данных',
            '8160' => 'ЭП, защита почты, документооборот',
        ];
    }
}