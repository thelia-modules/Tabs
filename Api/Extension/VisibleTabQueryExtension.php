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

namespace Tabs\Api\Extension;

use ApiPlatform\Metadata\Operation;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Tabs\Api\Resource\Tab;
use Tabs\Model\ItemAssociatedTabQuery;
use Thelia\Api\Bridge\Propel\Extension\QueryCollectionExtensionInterface;
use Thelia\Api\Bridge\Propel\Extension\QueryItemExtensionInterface;

final readonly class VisibleTabQueryExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function applyToCollection(ModelCriteria $query, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->restrictToVisibleTabs($query, $resourceClass, $operation);
    }

    public function applyToItem(ModelCriteria $query, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->restrictToVisibleTabs($query, $resourceClass, $operation);
    }

    private function restrictToVisibleTabs(ModelCriteria $query, string $resourceClass, ?Operation $operation): void
    {
        if (Tab::class !== $resourceClass || !$query instanceof ItemAssociatedTabQuery) {
            return;
        }

        $normalizationContext = $operation?->getNormalizationContext() ?? [];

        if (!\in_array(Tab::GROUP_FRONT_READ, $normalizationContext['groups'] ?? [], true)) {
            return;
        }

        $query->filterByVisible(1);
    }
}
