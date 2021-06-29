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
use Tabs\Form\TabsContentForm;
use Tabs\Model\ContentAssociatedTabQuery;
use Thelia\Core\Security\AccessManager;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\ContentQuery;

/**
 * Class ContentTabsController
 * @package Tabs\Controller
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class ContentTabsController extends BaseTabsController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function manageTabsContentAssociation($contentId)
	{

		if (null !== $response = $this->checkAuth(array(), array('Tabs'), AccessManager::UPDATE)) {
			return $response;
		}

		$tabId = $this->getRequest()->get('tab_id', null);
		if (null === $tabId) {
			return $this->createNewTabContentAssociation($contentId);
		} else {
			return $this->updateTabContentAssociation($tabId);
		}

	}

	public function createNewTabContentAssociation($contentId)
	{

		$tabsContentForm = new TabsContentForm($this->getRequest());

		$message = false;

		try {
			$content = ContentQuery::create()->findPk($contentId);

			if (null === $content) {
				throw new \InvalidArgumentException(sprintf("%d content id does not exist", $contentId));
			}

			$form = $this->validateForm($tabsContentForm);

			$event = $this->createEventInstance($form->getData());
			$event->setContentId($content->getId());

			$this->dispatch(TabsEvent::TABS_CONTENT_CREATE, $event);

			return $this->generateSuccessRedirect($tabsContentForm);

		} catch (FormValidationException $e) {
			$message = sprintf("Please check your input: %s", $e->getMessage());
		} catch (PropelException $e) {
			$message = $e->getMessage();
		} catch (\Exception $e) {
			$message = sprintf("Sorry, an error occured: %s", $e->getMessage() . " " . $e->getFile());
		}

		if ($message !== false) {
			\Thelia\Log\Tlog::getInstance()->error(sprintf("Error during tabs content association process : %s.", $message));

			$tabsContentForm->setErrorMessage($message);

			$this->getParserContext()
				->addForm($tabsContentForm)
				->setGeneralError($message);
		}

		return $this->updateAction();
	}

	public function updateTabContentAssociation($tabId)
	{

		$tabsContentForm = new TabsContentForm($this->getRequest());

		$message = false;

		try {
			$tab = ContentAssociatedTabQuery::create()->findPk($tabId);

			if (null === $tab) {
				throw new \InvalidArgumentException(sprintf("%d tab id does not exist", $tabId));
			}

			$form = $this->validateForm($tabsContentForm);

			$event = $this->createEventInstance($form->getData());
			$event->setTabId($tab->getId());
			$event->setContentId($tab->getContentId());

			$this->dispatch(TabsEvent::TABS_CONTENT_UPDATE, $event);

			return $this->generateSuccessRedirect($tabsContentForm);

		} catch (FormValidationException $e) {
			$message = sprintf("Please check your input: %s", $e->getMessage());
		} catch (PropelException $e) {
			$message = $e->getMessage();
		} catch (\Exception $e) {
			$message = sprintf("Sorry, an error occured: %s", $e->getMessage() . " " . $e->getFile());
		}

		if ($message !== false) {
			\Thelia\Log\Tlog::getInstance()->error(sprintf("Error during tabs content association process : %s.", $message));

			$tabsContentForm->setErrorMessage($message);

			$this->getParserContext()
				->addForm($tabsContentForm)
				->setGeneralError($message);
		}

		return $this->updateAction();

	}
}
