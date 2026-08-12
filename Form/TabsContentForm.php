<?php

declare(strict_types=1);

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tabs\Form;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class TabsContentForm extends BaseForm
{
    protected function buildForm(): void
    {
        $this->formBuilder
            ->add('title', TextType::class, [
                'constraints' => [new Constraints\NotBlank()],
                'label' => Translator::getInstance()->trans('Title'),
                'label_attr' => [
                    'for' => 'tabs_title',
                ],
            ])
            ->add('description', TextType::class, [
                'constraints' => [new Constraints\NotBlank()],
                'label' => Translator::getInstance()->trans('Description'),
                'label_attr' => [
                    'for' => 'tabs_description',
                ],
            ])
            ->add('visible', NumberType::class, [
                'label' => Translator::getInstance()->trans('Visible ?'),
                'label_attr' => [
                    'for' => 'tabs_visible',
                ],
            ])
            ->add('locale', TextType::class, [
                'constraints' => [new Constraints\NotBlank()],
            ]);
    }

    public static function getName(): string
    {
        return 'tabs_content';
    }
}
