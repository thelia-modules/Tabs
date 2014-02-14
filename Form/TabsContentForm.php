<?php
namespace Tabs\Form;


use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class TabsContentForm extends BaseForm{

    protected function buildForm()
    {

        $this->formBuilder
            ->add('title', 'text', array(
                    'constraints' => array(
                        new NotBlank()
                    ),
                    'label' => Translator::getInstance()->trans('Title'),
                    'label_attr' => array(
                        'for' => 'tabs_title'
                    )
                ))
            ->add('description', 'text', array(
                    'constraints' => array(
                        new NotBlank()
                    ),
                    'label' => Translator::getInstance()->trans('Description'),
                    'label_attr' => array(
                        'for' => 'tabs_description'
                    )
                ))
            ->add('visible', 'integer', array(
                    'label' => Translator::getInstance()->trans('Visible ?'),
                    'label_attr' => array(
                        'for' => 'tabs_visible'
                    )
                ))
            ->add("locale", "text", array(
                    "constraints" => array(
                        new NotBlank()
                    )
                ));
    }

    public function getName()
    {
        return 'tabs_content';
    }

} 