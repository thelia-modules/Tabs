<?php
namespace Tabs\Form;


use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class TabsContentForm extends BaseForm{

    protected function buildForm()
    {

        $this->formBuilder
            ->add(
                'title',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank()
                    ],
                    'label' => Translator::getInstance()->trans('Title'),
                    'label_attr' => [
                        'for' => 'tabs_title'
                    ]
                ])
            ->add(
                'description',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank()
                    ],
                    'label' => Translator::getInstance()->trans('Description'),
                    'label_attr' => [
                        'for' => 'tabs_description'
                    ]
                ])
            ->add(
                'visible',
                IntegerType::class,
                [
                    'label' => Translator::getInstance()->trans('Visible ?'),
                    'label_attr' => [
                        'for' => 'tabs_visible'
                    ]
                ])
            ->add(
                "locale",
                TextType::class,
                [
                    "constraints" => [
                        new NotBlank()
                    ]
                ]);
    }

    public static function getName()
    {
        return 'tabs_content';
    }

} 