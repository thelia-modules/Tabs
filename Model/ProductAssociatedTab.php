<?php

namespace Tabs\Model;

use Tabs\Model\Base\ProductAssociatedTab as BaseProductAssociatedTab;
use Thelia\Model\Tools\PositionManagementTrait;

class ProductAssociatedTab extends BaseProductAssociatedTab
{
    use PositionManagementTrait;
}