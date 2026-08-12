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

namespace Tabs\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tabs\Enum\TabItemType;
use Tabs\Service\TabManager;
use Thelia\Core\Event\Category\CategoryDeleteEvent;
use Thelia\Core\Event\Content\ContentDeleteEvent;
use Thelia\Core\Event\Folder\FolderDeleteEvent;
use Thelia\Core\Event\Product\ProductDeleteEvent;
use Thelia\Core\Event\TheliaEvents;

/**
 * Drops the tabs of a deleted item.
 *
 * item_associated_tab.item_id is polymorphic and therefore cannot carry the foreign key
 * that gave the four former per-type tables their ON DELETE CASCADE. This subscriber is
 * that cascade.
 *
 * It runs at a negative priority so the core delete actions (registered at 128) have
 * already run, and it double-checks that the item is really gone: an aborted deletion must
 * not take the tabs down with it.
 */
final class ItemDeleteListener implements EventSubscriberInterface
{
    private const PRIORITY = -128;

    public function __construct(
        private readonly TabManager $tabManager,
    ) {
    }

    /**
     * @return array<string, array{0: string, 1: int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            TheliaEvents::PRODUCT_DELETE => ['onProductDelete', self::PRIORITY],
            TheliaEvents::CONTENT_DELETE => ['onContentDelete', self::PRIORITY],
            TheliaEvents::CATEGORY_DELETE => ['onCategoryDelete', self::PRIORITY],
            TheliaEvents::FOLDER_DELETE => ['onFolderDelete', self::PRIORITY],
        ];
    }

    public function onProductDelete(ProductDeleteEvent $event): void
    {
        $this->deleteTabs(TabItemType::Product, (int) $event->getProductId());
    }

    public function onContentDelete(ContentDeleteEvent $event): void
    {
        $this->deleteTabs(TabItemType::Content, (int) $event->getContentId());
    }

    public function onCategoryDelete(CategoryDeleteEvent $event): void
    {
        $this->deleteTabs(TabItemType::Category, (int) $event->getCategoryId());
    }

    public function onFolderDelete(FolderDeleteEvent $event): void
    {
        $this->deleteTabs(TabItemType::Folder, (int) $event->getFolderId());
    }

    private function deleteTabs(TabItemType $itemType, int $itemId): void
    {
        if ($itemId <= 0 || $this->tabManager->itemExists($itemType, $itemId)) {
            return;
        }

        $this->tabManager->deleteAllFor($itemType, $itemId);
    }
}
