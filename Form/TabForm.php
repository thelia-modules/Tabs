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

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints;
use Tabs\Enum\TabItemType;
use Tabs\Tabs;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

/**
 * The single tab form, for all four item types and for both creation and update.
 *
 * `tab_id` tells the two apart: empty means creation.
 */
final class TabForm extends BaseForm
{
    protected function buildForm(): void
    {
        $this->formBuilder
            ->add('tab_id', HiddenType::class, [
                'required' => false,
            ])
            ->add('item_type', HiddenType::class, [
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Choice(choices: TabItemType::values()),
                ],
            ])
            ->add('item_id', HiddenType::class, [
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\GreaterThan(0),
                ],
            ])
            ->add('title', TextType::class, [
                'constraints' => [new Constraints\NotBlank()],
                'label' => Translator::getInstance()->trans('Title', [], Tabs::DOMAIN_NAME),
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [new Constraints\NotBlank()],
                'label' => Translator::getInstance()->trans('Description', [], Tabs::DOMAIN_NAME),
                'attr' => [
                    'class' => 'wysiwyg',
                ],
            ])
            ->add('visible', CheckboxType::class, [
                'required' => false,
                'label' => Translator::getInstance()->trans('Visible', [], Tabs::DOMAIN_NAME),
            ])
            ->add('locale', HiddenType::class, [
                'constraints' => [new Constraints\NotBlank()],
            ]);
    }

    public static function getName(): string
    {
        return 'tabs_tab';
    }
}
