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
use Symfony\Component\Form\Form;
use Tabs\Form\TabsContentForm;
use Tabs\Form\TabsProductForm;
use Tabs\Tabs;
use Thelia\Api\Service\DataAccess\LoopDataAccessService;
use Thelia\Core\Event\Hook\HookRenderBlockEvent;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Form\FormServiceInterface;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Template\Parser\ParserResolver;
use Thelia\Model\ContentQuery;
use Thelia\Model\FolderQuery;
use Thelia\Model\ProductQuery;
use Thelia\Tools\URL;

/**
 * Back-office hooks.
 *
 * The Twig back-office renders module fragments outside of any form or loop context, so
 * everything the templates need (form view, tab rows, URLs, parent object identifiers) is
 * resolved here instead of in the template, as the Smarty `{form}` / `{loop}` blocks used to.
 */
class BackHook extends BaseHook
{
    public function __construct(
        private readonly FormServiceInterface $formService,
        private readonly LoopDataAccessService $loopDataAccessService,
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
            'main.top-menu-tools' => [['type' => 'back', 'method' => 'onMainTopMenuTools']],
            'category.tab-content' => [['type' => 'back', 'method' => 'onCategoryTabContent']],
            'product.tab-content' => [['type' => 'back', 'method' => 'onProductTabContent']],
            'folder.tab-content' => [['type' => 'back', 'method' => 'onFolderTabContent']],
            'content.tab-content' => [['type' => 'back', 'method' => 'onContentTabContent']],
            'main.footer-js' => [['type' => 'back', 'method' => 'onMainFooterJs']],
        ];
    }

    /**
     * Add a link to the pop-in configuration page in the tools menu.
     */
    public function onMainTopMenuTools(HookRenderBlockEvent $event): void
    {
        $event->add([
            'title' => $this->trans('Tabs options', [], Tabs::DOMAIN_NAME),
            'url' => URL::getInstance()->absoluteUrl('/admin/module/tags'),
        ]);
    }

    public function onCategoryTabContent(HookRenderEvent $event): void
    {
        $categoryId = (int) $event->getArgument('category');

        $this->renderTabs($event, 'hook/category-edit.html.twig', [
            'form_name' => 'tabs_admin_category',
            'tab_source' => 'category',
            'tab_source_id' => $categoryId,
            'form_action_url' => URL::getInstance()->absoluteUrl('/admin/category/update/'.$categoryId.'/tabs'),
            'form_success_url' => URL::getInstance()->absoluteUrl('/admin/categories/update', [
                'category_id' => $categoryId,
                'current_tab' => 'modules',
            ]),
            'parent_inputs' => ['category_id' => $categoryId],
            'position_params' => ['category_id' => $categoryId],
        ]);
    }

    public function onProductTabContent(HookRenderEvent $event): void
    {
        $productId = (int) $event->getArgument('product');

        $this->renderTabs($event, 'hook/product-edit.html.twig', [
            'form_name' => TabsProductForm::getName(),
            'tab_source' => 'product',
            'tab_source_id' => $productId,
            'form_action_url' => URL::getInstance()->absoluteUrl('/admin/product/update/'.$productId.'/tabs'),
            'form_success_url' => URL::getInstance()->absoluteUrl('/admin/products/update', [
                'product_id' => $productId,
                'current_tab' => 'modules',
            ]),
            'parent_inputs' => [
                'product_id' => $productId,
                'category_id' => ProductQuery::create()->findPk($productId)?->getDefaultCategoryId() ?? 0,
            ],
            'position_params' => ['product_id' => $productId],
        ]);
    }

    public function onFolderTabContent(HookRenderEvent $event): void
    {
        $folderId = (int) $event->getArgument('folder');

        $this->renderTabs($event, 'hook/folder-edit.html.twig', [
            'form_name' => 'tabs_admin_folder',
            'tab_source' => 'folder',
            'tab_source_id' => $folderId,
            'form_action_url' => URL::getInstance()->absoluteUrl('/admin/folder/update/'.$folderId.'/tabs'),
            'form_success_url' => URL::getInstance()->absoluteUrl('/admin/folders/update/'.$folderId, [
                'current_tab' => 'modules',
            ]),
            'parent_inputs' => [
                'folder_id' => $folderId,
                'parent' => FolderQuery::create()->findPk($folderId)?->getParent() ?? 0,
            ],
            'position_params' => ['folder_id' => $folderId],
        ]);
    }

    public function onContentTabContent(HookRenderEvent $event): void
    {
        $contentId = (int) $event->getArgument('content');
        $defaultFolderId = (int) (ContentQuery::create()->findPk($contentId)?->getDefaultFolderId() ?? 0);

        $this->renderTabs($event, 'hook/content-edit.html.twig', [
            'form_name' => TabsContentForm::getName(),
            'tab_source' => 'content',
            'tab_source_id' => $contentId,
            'form_action_url' => URL::getInstance()->absoluteUrl('/admin/content/update/'.$contentId.'/tabs'),
            'form_success_url' => URL::getInstance()->absoluteUrl('/admin/content/update/'.$contentId, [
                'current_tab' => 'modules',
            ]),
            'parent_inputs' => [
                'content_id' => $contentId,
                'folder_id' => $defaultFolderId,
                'parent' => FolderQuery::create()->findPk($defaultFolderId)?->getParent() ?? 0,
            ],
            'position_params' => ['content_id' => $contentId],
        ]);
    }

    public function onMainFooterJs(HookRenderEvent $event): void
    {
        $event->add($this->render('hook/footer_js.html.twig'));
    }

    /**
     * @param array<string, mixed> $context
     */
    private function renderTabs(HookRenderEvent $event, string $template, array $context): void
    {
        $form = $this->resolveForm((string) $context['form_name']);

        // Category and folder associations have a database schema but no form, no action
        // listener and no controller route: the fragment stays empty, as it did in Smarty.
        if (null === $form) {
            return;
        }

        $lang = $this->getSession()->getAdminEditionLang();

        unset($context['form_name']);

        $event->add($this->render($template, array_merge($context, [
            'form' => $form->createView(),
            'form_error' => $form->getErrors(true)->count() > 0,
            'tabs' => $this->findTabs((string) $context['tab_source'], (int) $context['tab_source_id'], $lang->getId()),
            'position_path' => '/admin/tabs/update-position',
            'edit_language_id' => $lang->getId(),
            'edit_language_locale' => $lang->getLocale(),
        ])));
    }

    private function resolveForm(string $name): ?Form
    {
        try {
            return $this->formService->getFormByName($name);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function findTabs(string $source, int $sourceId, int $langId): array
    {
        // The `tabs` loop only accepts a product or a content id.
        $argument = match ($source) {
            'product' => 'product',
            'content' => 'content',
            default => null,
        };

        if (null === $argument) {
            return [];
        }

        return $this->loopDataAccessService->theliaLoop('tabs', 'tabs', [
            $argument => $sourceId,
            'lang' => $langId,
            'order' => 'manual',
        ]);
    }
}
