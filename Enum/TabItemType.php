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

namespace Tabs\Enum;

use Propel\Runtime\ActiveQuery\ModelCriteria;
use Thelia\Model\CategoryQuery;
use Thelia\Model\ContentQuery;
use Thelia\Model\FolderQuery;
use Thelia\Model\ProductQuery;

/**
 * The kind of Thelia item a tab is attached to, stored in item_associated_tab.item_type.
 *
 * Pure mapping: every method here is a lookup table. Anything that touches the database
 * belongs to Tabs\Service\TabManager.
 */
enum TabItemType: string
{
    case Product = 'product';
    case Content = 'content';
    case Category = 'category';
    case Folder = 'folder';

    /**
     * A fresh query on the Thelia table this kind of tab hangs off.
     */
    public function createQuery(): ModelCriteria
    {
        return match ($this) {
            self::Product => ProductQuery::create(),
            self::Content => ContentQuery::create(),
            self::Category => CategoryQuery::create(),
            self::Folder => FolderQuery::create(),
        };
    }

    /**
     * The back-office hook whose fragment renders this item's tabs.
     */
    public function hookName(): string
    {
        return $this->value.'.tab-content';
    }

    /**
     * The hook event argument carrying the item id.
     */
    public function hookArgument(): string
    {
        return $this->value;
    }

    /**
     * The back-office route showing this item, used to send the admin back where they were.
     *
     * @return array{0: string, 1: array<string, int>}
     */
    public function adminEditRoute(int $itemId): array
    {
        return match ($this) {
            self::Product => ['admin.products.update', ['product_id' => $itemId]],
            self::Content => ['admin.content.update', ['content_id' => $itemId]],
            self::Category => ['admin.categories.update', ['category_id' => $itemId]],
            self::Folder => ['admin.folders.update', ['folder_id' => $itemId]],
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $case): string => $case->value, self::cases());
    }
}
