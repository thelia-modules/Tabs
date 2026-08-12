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

namespace Tabs\Service;

use Propel\Runtime\ActiveQuery\Criteria;
use Tabs\Enum\TabItemType;
use Tabs\Enum\TabPositionMode;
use Tabs\Exception\TabNotFoundException;
use Tabs\Model\ItemAssociatedTab;
use Tabs\Model\ItemAssociatedTabQuery;

/**
 * Every read and write on item_associated_tab goes through here.
 *
 * Positions are handled by the Propel sortable behavior, scoped on (item_type, item_id):
 * a new row gets the next free rank in its own scope on save, and moveUp()/moveDown()
 * only ever swap rows sharing that scope.
 */
final readonly class TabManager
{
    /**
     * @return list<ItemAssociatedTab>
     */
    public function findFor(
        TabItemType $itemType,
        int $itemId,
        string $locale,
        bool $visibleOnly = false,
    ): array {
        $query = ItemAssociatedTabQuery::create()
            ->filterByItemType($itemType->value)
            ->filterByItemId($itemId)
            ->joinWithI18n($locale)
            ->orderByPosition(Criteria::ASC);

        if ($visibleOnly) {
            $query->filterByVisible(1);
        }

        $tabs = [];

        foreach ($query->find() as $tab) {
            $tab->setLocale($locale);
            $tabs[] = $tab;
        }

        return $tabs;
    }

    public function create(
        TabItemType $itemType,
        int $itemId,
        string $locale,
        string $title,
        ?string $description,
        bool $visible,
    ): ItemAssociatedTab {
        $tab = (new ItemAssociatedTab())
            ->setItemType($itemType->value)
            ->setItemId($itemId)
            ->setVisible($visible ? 1 : 0);
        $tab
            ->setLocale($locale)
            ->setTitle($title)
            ->setDescription($description);
        $tab->save();

        return $tab;
    }

    public function update(
        int $tabId,
        string $locale,
        string $title,
        ?string $description,
        bool $visible,
    ): ItemAssociatedTab {
        $tab = $this->get($tabId);

        $tab->setVisible($visible ? 1 : 0);

        $tab
            ->setLocale($locale)
            ->setTitle($title)
            ->setDescription($description);

        $tab->save();

        return $tab;
    }

    public function delete(int $tabId): ItemAssociatedTab
    {
        $tab = $this->get($tabId);

        $tab->delete();

        return $tab;
    }

    public function move(int $tabId, TabPositionMode $mode): ItemAssociatedTab
    {
        $tab = $this->get($tabId);

        match ($mode) {
            TabPositionMode::Up => $tab->moveUp(),
            TabPositionMode::Down => $tab->moveDown(),
        };

        return $tab;
    }

    /**
     * Drop every tab attached to an item. Used when the item itself is deleted, since a
     * polymorphic column cannot carry the ON DELETE CASCADE the former per-type tables had.
     */
    public function deleteAllFor(TabItemType $itemType, int $itemId): int
    {
        return ItemAssociatedTabQuery::create()
            ->filterByItemType($itemType->value)
            ->filterByItemId($itemId)
            ->delete();
    }

    /**
     * Whether the Thelia item a tab would be attached to still exists.
     */
    public function itemExists(TabItemType $itemType, int $itemId): bool
    {
        return null !== $itemType->createQuery()->findPk($itemId);
    }

    public function get(int $tabId): ItemAssociatedTab
    {
        $tab = ItemAssociatedTabQuery::create()->findPk($tabId);

        if (null === $tab) {
            throw TabNotFoundException::forId($tabId);
        }

        return $tab;
    }
}
