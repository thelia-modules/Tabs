<?php

namespace Tabs\Model;

use Tabs\Model\Base\FolderAssociatedTab as BaseFolderAssociatedTab;
use Thelia\Model\Tools\ModelEventDispatcherTrait;
use Thelia\Model\Tools\PositionManagementTrait;

/**
 * Skeleton subclass for representing a row from the 'folder_associated_tab' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class FolderAssociatedTab extends BaseFolderAssociatedTab
{
	use PositionManagementTrait;
	use ModelEventDispatcherTrait;
}
