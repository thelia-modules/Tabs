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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Tabs\Controller\Base\BaseTabsController;
use Tabs\Event\TabsEvent;
use Tabs\Form\TabsFolderForm;
use Tabs\Model\FolderAssociatedTabQuery;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Template\ParserContext;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\FolderQuery;

/**
 * Class FolderTabsController
 * @package Tabs\Controller
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
#[Route('/admin/folder', name: 'tabs_folder_')]
class FolderTabsController extends BaseTabsController
{
    #[Route('/update/{folderId}/tabs', name: 'manage_tabs_folder')]
    public function manageTabsFolderAssociation(
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        ParserContext $parserContext,
        $folderId
    ) {

		if (null !== $response = $this->checkAuth(array(), array('Tabs'), AccessManager::UPDATE)) {
			return $response;
		}

		$tabId = $requestStack->getCurrentRequest()->get('tab_id', null);
		if (null === $tabId) {
			return $this->createNewTabFolderAssociation(
                $eventDispatcher,
                $parserContext,
                $folderId
            );
		}
        return $this->updateTabFolderAssociation(
            $eventDispatcher,
            $parserContext,
            $tabId
        );
	}

	public function createNewTabFolderAssociation(
        EventDispatcherInterface $eventDispatcher,
        ParserContext $parserContext,
        $folderId
    ) {
		$tabsFolderForm = $this->createForm(TabsFolderForm::getName());

		$message = false;

		try {
			$folder = FolderQuery::create()->findPk($folderId);

			if (null === $folder) {
				throw new \InvalidArgumentException(sprintf("%d folder id does not exist", $folderId));
			}

			$form = $this->validateForm($tabsFolderForm);

			$event = $this->createEventInstance($form->getData());
			$event->setFolderId($folder->getId());

            $eventDispatcher->dispatch($event, TabsEvent::TABS_FOLDER_CREATE);

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

			$parserContext
				->addForm($tabsFolderForm)
				->setGeneralError($message);
		}

		return $this->updateAction($parserContext);
	}

	public function updateTabFolderAssociation(
        EventDispatcherInterface $eventDispatcher,
        ParserContext $parserContext,
        $tabId
    ) {
		$tabsFolderForm = $this->createForm(TabsFolderForm::getName());

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

            $eventDispatcher->dispatch($event, TabsEvent::TABS_FOLDER_UPDATE);

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

			$parserContext
				->addForm($tabsFolderForm)
				->setGeneralError($message);
		}

		return $this->updateAction($parserContext);
	}
}
