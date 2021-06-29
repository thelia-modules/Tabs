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
use Tabs\Form\TabsCategoryForm;
use Tabs\Model\CategoryAssociatedTabQuery;
use Thelia\Core\Security\AccessManager;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\CategoryQuery;

/**
 * Class CategoryTabsController
 * @package Tabs\Controller
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class CategoryTabsController extends BaseTabsController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function manageTabsCategoryAssociation($categoryId)
	{

		if (null !== $response = $this->checkAuth(array(), array('Tabs'), AccessManager::UPDATE)) {
			return $response;
		}

		$tabId = $this->getRequest()->get('tab_id', null);
		if (null === $tabId) {
			return $this->createNewTabCategoryAssociation($categoryId);
		} else {
			return $this->updateTabCategoryAssociation($tabId);
		}

	}

	public function createNewTabCategoryAssociation($categoryId)
	{

		$tabsCategoryForm = new TabsCategoryForm($this->getRequest());

		$message = false;

		try {
			$category = CategoryQuery::create()->findPk($categoryId);

			if (null === $category) {
				throw new \InvalidArgumentException(sprintf("%d category id does not exist", $categoryId));
			}

			$form = $this->validateForm($tabsCategoryForm);

			$event = $this->createEventInstance($form->getData());
			$event->setCategoryId($category->getId());

			$this->dispatch(TabsEvent::TABS_CATEGORY_CREATE, $event);

			return $this->generateSuccessRedirect($tabsCategoryForm);

		} catch (FormValidationException $e) {
			$message = sprintf("Please check your input: %s", $e->getMessage());
		} catch (PropelException $e) {
			$message = $e->getMessage();
		} catch (\Exception $e) {
			$message = sprintf("Sorry, an error occured: %s", $e->getMessage() . " " . $e->getFile());
		}

		if ($message !== false) {
			\Thelia\Log\Tlog::getInstance()->error(sprintf("Error during tabs category association process : %s.", $message));

			$tabsCategoryForm->setErrorMessage($message);

			$this->getParserContext()
				->addForm($tabsCategoryForm)
				->setGeneralError($message);
		}

		return $this->updateAction();
	}

	public function updateTabCategoryAssociation($tabId)
	{

		$tabsCategoryForm = new TabsCategoryForm($this->getRequest());

		$message = false;

		try {
			$tab = CategoryAssociatedTabQuery::create()->findPk($tabId);

			if (null === $tab) {
				throw new \InvalidArgumentException(sprintf("%d tab id does not exist", $tabId));
			}

			$form = $this->validateForm($tabsCategoryForm);

			$event = $this->createEventInstance($form->getData());
			$event->setTabId($tab->getId());
			$event->setCategoryId($tab->getCategoryId());

			$this->dispatch(TabsEvent::TABS_CATEGORY_UPDATE, $event);

			return $this->generateSuccessRedirect($tabsCategoryForm);

		} catch (FormValidationException $e) {
			$message = sprintf("Please check your input: %s", $e->getMessage());
		} catch (PropelException $e) {
			$message = $e->getMessage();
		} catch (\Exception $e) {
			$message = sprintf("Sorry, an error occured: %s", $e->getMessage() . " " . $e->getFile());
		}

		if ($message !== false) {
			\Thelia\Log\Tlog::getInstance()->error(sprintf("Error during tabs folder association process : %s.", $message));

			$tabsCategoryForm->setErrorMessage($message);

			$this->getParserContext()
				->addForm($tabsCategoryForm)
				->setGeneralError($message);
		}

		return $this->updateAction();

	}
}
