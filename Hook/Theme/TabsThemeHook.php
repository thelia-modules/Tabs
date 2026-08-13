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

namespace Tabs\Hook\Theme;

use Tabs\Enum\TabItemType;
use Thelia\Core\Hook\Theme\ThemeHookInterface;
use Twig\Environment;

final readonly class TabsThemeHook implements ThemeHookInterface
{
    public function __construct(
        private Environment $twig,
    ) {
    }

    public function supports(string $hookName): bool
    {
        return \in_array($hookName, ['product.details.bottom', 'category.bottom', 'folder.bottom', 'content.bottom'], true);
    }

    public function render(string $hookName, array $parameters): string
    {
        return match ($hookName) {
            'product.details.bottom' => $this->renderTabs(TabItemType::Product->value, $parameters),
            'category.bottom' => $this->renderTabs(TabItemType::Category->value, $parameters),
            'folder.bottom' => $this->renderTabs(TabItemType::Folder->value, $parameters),
            'content.bottom' => $this->renderTabs(TabItemType::Content->value, $parameters),
            default => '',
        };
    }

    private function renderTabs(string $itemType, array $parameters): string
    {
        $item = $parameters[$itemType] ?? null;

        if (!\is_array($item) || !isset($item['id'])) {
            return '';
        }

        return $this->twig->render('@TabsModule/theme-hook/tabs.html.twig', [
            'itemType' => $itemType,
            'itemId' => $item['id'],
        ]);
    }
}
