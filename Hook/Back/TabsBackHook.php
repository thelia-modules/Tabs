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

namespace Tabs\Hook\Back;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tabs\Enum\TabItemType;
use Tabs\Form\TabForm;
use Tabs\Service\TabManager;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Form\FormServiceInterface;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Template\Parser\ParserResolver;

/**
 * Renders the tabs fragment in the "Modules" tab of the product, content, category and
 * folder edition pages.
 */
final class TabsBackHook extends BaseHook
{
    public function __construct(
        private readonly FormServiceInterface $formService,
        private readonly TabManager $tabManager,
        ?EventDispatcherInterface $dispatcher = null,
        ?ParserResolver $parserResolver = null,
    ) {
        parent::__construct($dispatcher, $parserResolver);
    }

    /**
     * @return array<string, list<array{type: string, method: string}>>
     */
    public static function getSubscribedHooks(): array
    {
        return [
            TabItemType::Product->hookName() => [['type' => 'back', 'method' => 'onProductTabContent']],
            TabItemType::Content->hookName() => [['type' => 'back', 'method' => 'onContentTabContent']],
            TabItemType::Category->hookName() => [['type' => 'back', 'method' => 'onCategoryTabContent']],
            TabItemType::Folder->hookName() => [['type' => 'back', 'method' => 'onFolderTabContent']],
        ];
    }

    public function onProductTabContent(HookRenderEvent $event): void
    {
        $this->renderTabs($event, TabItemType::Product);
    }

    public function onContentTabContent(HookRenderEvent $event): void
    {
        $this->renderTabs($event, TabItemType::Content);
    }

    public function onCategoryTabContent(HookRenderEvent $event): void
    {
        $this->renderTabs($event, TabItemType::Category);
    }

    public function onFolderTabContent(HookRenderEvent $event): void
    {
        $this->renderTabs($event, TabItemType::Folder);
    }

    private function renderTabs(HookRenderEvent $event, TabItemType $itemType): void
    {
        $itemId = (int) $event->getArgument($itemType->hookArgument());

        if ($itemId <= 0) {
            return;
        }
        $lang = $this->getSession()->getAdminEditionLang();

        try {
            $form = $this->formService->getFormByName(TabForm::getName(), [
                'item_type' => $itemType->value,
                'item_id' => $itemId,
                'locale' => $lang->getLocale(),
            ]);

            $event->add($this->render('tabs-edit.html.twig', [
                'form' => $form->createView(),
                'form_error' => $form->getErrors(true)->count() > 0,
                'item_type' => $itemType->value,
                'item_id' => $itemId,
                'tabs' => $this->tabManager->findFor($itemType, $itemId, $lang->getLocale()),
                'edit_language_locale' => $lang->getLocale(),
            ]));
        } catch (\Throwable $th) {
            dd($th);
        }
    }
}
