<?php
namespace Tabs\Form;


use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class TabsCategoryForm extends TabsContentForm
{

    protected function buildForm()
    {
        parent::buildForm();
    }

    public function getName()
    {
        return 'tabs_category';
    }

} 