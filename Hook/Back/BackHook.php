<?php

namespace Tabs\Hook\Back;

use Symfony\Component\Routing\RouterInterface;
use Tabs\Tabs;
use Thelia\Core\Event\Hook\HookRenderBlockEvent;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Tools\URL;

/**
 * Back-office hooks.
 */
class BackHook extends BaseHook
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add a link to the pop-in configuration page in the tools menu.
     * @param HookRenderBlockEvent $event
     */
    public function onMainTopMenuTools(HookRenderBlockEvent $event)
    {
        $event->add([
            'title' => $this->trans('Tabs options', [], Tabs::MESSAGE_DOMAIN_BO),
            'url' => URL::getInstance()->absoluteUrl('/admin/module/tags'),
        ]);
    }

    public function onCategoryTabContent(HookRenderEvent $event)
    {
        $event->add($this->render('hook/category-edit.html'));
    }

    public function onProductTabContent(HookRenderEvent $event)
    {
        $event->add($this->render('hook/product-edit.html'));
    }

    public function onFolderTabContent(HookRenderEvent $event)
    {
        $event->add($this->render('hook/folder-edit.html'));
    }

    public function onContentTabContent(HookRenderEvent $event)
    {
        $event->add($this->render('hook/content-edit.html'));
    }

    public function onMainFooterJs(HookRenderEvent $event)
    {
        $event->add($this->render('hook/footer_js.html'));
    }
}
