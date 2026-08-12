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

namespace Tabs\Api\Resource;

use Symfony\Component\Serializer\Attribute\Groups;
use Thelia\Api\Resource\I18n;

class TabI18n extends I18n
{
    #[Groups([
        Tab::GROUP_ADMIN_READ,
        Tab::GROUP_ADMIN_WRITE,
        Tab::GROUP_FRONT_READ,
    ])]
    protected ?string $title = null;

    #[Groups([
        Tab::GROUP_ADMIN_READ,
        Tab::GROUP_ADMIN_WRITE,
        Tab::GROUP_FRONT_READ,
    ])]
    protected ?string $description = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
