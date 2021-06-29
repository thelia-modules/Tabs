<?php

namespace Tabs\Model;

use Tabs\Model\Base\ContentAssociatedTab as BaseContentAssociatedTab;
use Thelia\Model\Tools\ModelEventDispatcherTrait;
use Thelia\Model\Tools\PositionManagementTrait;

class ContentAssociatedTab extends BaseContentAssociatedTab
{
	use PositionManagementTrait;
	use ModelEventDispatcherTrait;

}
