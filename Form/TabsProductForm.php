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

namespace Tabs\Form;

class TabsProductForm extends TabsContentForm
{
    protected function buildForm(): void
    {
        parent::buildForm();
    }

    public static function getName(): string
    {
        return 'tabs_product';
    }
}
