<?php

namespace App\Form\Partner;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PartnerType
 *
 * @package App\Form\Partner
 */
class PartnerType extends AbstractType
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * PartnerType constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $option
     */
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $data = $builder->getData();

        $builder
            ->add('name', Type\TextType::class, [
                'required' => false
            ])
            ->add('country', Type\ChoiceType::class, [
                'required' => false,
                'choices' => array_flip($this->getCountries()),
            ])
            ->add('city', Type\ChoiceType::class, [
                'required' => false,
                'choices' => array_flip($this->getCities((int) $data['country'])),
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => false,
        ]);
    }

    /**
     * @return array
     */
    public function getCountries(): array
    {
        return [
            2 => "Азербайджан",
            4 => "Беларусь",
            5 => "Грузия",
            6 => "Казахстан",
            1 => "Россия",
            13 => "Узбекистан",
        ];
    }

    /**
     * @param int $country
     *
     * @return array
     */
    public function getCities(int $country): array
    {
        $projectDir = $this->container->getParameter('kernel.project_dir');
        $filename = "$projectDir/templates/partners/cities_$this->type.json.twig";

        if (is_readable($filename)) {
            $cities = json_decode(file_get_contents($filename), true);
            if (array_key_exists($country, $cities)) {
                return array_combine($cities[$country], $cities[$country]);
            }
        }

        return [];
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return "";
    }
}