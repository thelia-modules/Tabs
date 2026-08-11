<?php
namespace Tabs\Form;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Symfony\Component\Validator\Constraints;

class TabsContentForm extends BaseForm{

    protected function buildForm()
    {

        $this->formBuilder
            ->add('title', TextType::class, array(
                    'constraints' => [new Constraints\NotBlank()],
                    'label' => Translator::getInstance()->trans('Title'),
                    'label_attr' => array(
                        'for' => 'tabs_title'
                    )
                ))
            ->add('description', TextType::class, array(
                    'constraints' => [new Constraints\NotBlank()],
                    'label' => Translator::getInstance()->trans('Description'),
                    'label_attr' => array(
                        'for' => 'tabs_description'
                    )
                ))
            ->add('visible', NumberType::class, array(
                    'label' => Translator::getInstance()->trans('Visible ?'),
                    'label_attr' => array(
                        'for' => 'tabs_visible'
                    )
                ))
            ->add("locale", TextType::class, array(
                    "constraints" => [new Constraints\NotBlank()]
                ));
    }

    public static function getName()
    {
        return 'tabs_content';
    }

} 