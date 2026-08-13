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

namespace Tabs\Exception;

final class TabNotFoundException extends \RuntimeException
{
    public static function forId(int $tabId): self
    {
        return new self(\sprintf('No tab found with id %d.', $tabId));
    }
}
