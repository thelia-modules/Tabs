<?php
namespace Tabs\Form;


use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class TabsProductForm extends TabsContentForm{

    protected function buildForm()
    {
        parent::buildForm();
    }

    public static function getName()
    {
        return 'tabs_product';
    }

} 