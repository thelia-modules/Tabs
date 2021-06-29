<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia                                                                       */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace Tabs\Controller;

use Propel\Runtime\Exception\PropelException;
use Tabs\Controller\Base\BaseTabsController;
use Tabs\Event\TabsEvent;
use Tabs\Form\TabsFolderForm;
use Tabs\Model\FolderAssociatedTabQuery;
use Thelia\Core\Security\AccessManager;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\FolderQuery;

/**
 * Class FolderTabsController
 * @package Tabs\Controller
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class FolderTabsController extends BaseTabsController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function manageTabsFolderAssociation($folderId)
	{

		if (null !== $response = $this->checkAuth(array(), array('Tabs'), AccessManager::UPDATE)) {
			return $response;
		}

		$tabId = $this->getRequest()->get('tab_id', null);
		if (null === $tabId) {
			return $this->createNewTabFolderAssociation($folderId);
		} else {
			return $this->updateTabFolderAssociation($tabId);
		}

	}

	public function createNewTabFolderAssociation($folderId)
	{

		$tabsFolderForm = new TabsFolderForm($this->getRequest());

		$message = false;

		try {
			$folder = FolderQuery::create()->findPk($folderId);

			if (null === $folder) {
				throw new \InvalidArgumentException(sprintf("%d folder id does not exist", $folderId));
			}

			$form = $this->validateForm($tabsFolderForm);

			$event = $this->createEventInstance($form->getData());
			$event->setFolderId($folder->getId());

			$this->dispatch(TabsEvent::TABS_FOLDER_CREATE, $event);

			return $this->generateSuccessRedirect($tabsFolderForm);

		} catch (FormValidationException $e) {
			$message = sprintf("Please check your input: %s", $e->getMessage());
		} catch (PropelException $e) {
			$message = $e->getMessage();
		} catch (\Exception $e) {
			$message = sprintf("Sorry, an error occured: %s", $e->getMessage() . " " . $e->getFile());
		}

		if ($message !== false) {
			\Thelia\Log\Tlog::getInstance()->error(sprintf("Error during tabs folder association process : %s.", $message));

			$tabsFolderForm->setErrorMessage($message);

			$this->getParserContext()
				->addForm($tabsFolderForm)
				->setGeneralError($message);
		}

		return $this->updateAction();
	}

	public function updateTabFolderAssociation($tabId)
	{

		$tabsFolderForm = new TabsFolderForm($this->getRequest());

		$message = false;

		try {
			$tab = FolderAssociatedTabQuery::create()->findPk($tabId);

			if (null === $tab) {
				throw new \InvalidArgumentException(sprintf("%d tab id does not exist", $tabId));
			}

			$form = $this->validateForm($tabsFolderForm);

			$event = $this->createEventInstance($form->getData());
			$event->setTabId($tab->getId());
			$event->setFolderId($tab->getFolderId());

			$this->dispatch(TabsEvent::TABS_FOLDER_UPDATE, $event);

			return $this->generateSuccessRedirect($tabsFolderForm);

		} catch (FormValidationException $e) {
			$message = sprintf("Please check your input: %s", $e->getMessage());
		} catch (PropelException $e) {
			$message = $e->getMessage();
		} catch (\Exception $e) {
			$message = sprintf("Sorry, an error occured: %s", $e->getMessage() . " " . $e->getFile());
		}

		if ($message !== false) {
			\Thelia\Log\Tlog::getInstance()->error(sprintf("Error during tabs folder association process : %s.", $message));

			$tabsFolderForm->setErrorMessage($message);

			$this->getParserContext()
				->addForm($tabsFolderForm)
				->setGeneralError($message);
		}

		return $this->updateAction();

	}
}
