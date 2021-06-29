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
use Tabs\Form\TabsProductForm;
use Tabs\Model\ProductAssociatedTabQuery;
use Thelia\Core\Security\AccessManager;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\ProductQuery;

/**
 * Class ProductTabsController
 * @package Tabs\Controller
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class ProductTabsController extends BaseTabsController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function manageTabsProductAssociation($productId)
	{

		if (null !== $response = $this->checkAuth(array(), array('Tabs'), AccessManager::UPDATE)) {
			return $response;
		}

		$tabId = $this->getRequest()->get('tab_id', null);
		if (null === $tabId) {
			return $this->createNewTabProductAssociation($productId);
		} else {
			return $this->updateTabProductAssociation($tabId);
		}

	}

	public function createNewTabProductAssociation($productId)
	{

		$tabsProductForm = new TabsProductForm($this->getRequest());

		$message = false;

		try {
			$product = ProductQuery::create()->findPk($productId);

			if (null === $product) {
				throw new \InvalidArgumentException(sprintf("%d content id does not exist", $productId));
			}

			$form = $this->validateForm($tabsProductForm);

			$event = $this->createEventInstance($form->getData());
			$event->setProductId($product->getId());

			$this->dispatch(TabsEvent::TABS_PRODUCT_CREATE, $event);

			return $this->generateSuccessRedirect($tabsProductForm);
		} catch (FormValidationException $e) {
			$message = sprintf("Please check your input: %s", $e->getMessage());
		} catch (PropelException $e) {
			$message = $e->getMessage();
		} catch (\Exception $e) {
			$message = sprintf("Sorry, an error occured: %s", $e->getMessage() . " " . $e->getFile());
		}

		if ($message !== false) {
			\Thelia\Log\Tlog::getInstance()->error(sprintf("Error during tabs product association process : %s.", $message));

			$tabsProductForm->setErrorMessage($message);

			$this->getParserContext()
				->addForm($tabsProductForm)
				->setGeneralError($message);
		}

		return $this->updateAction();
	}

	public function updateTabProductAssociation($tabId)
	{

		$tabsProductForm = new TabsProductForm($this->getRequest());

		$message = false;

		try {
			$tab = ProductAssociatedTabQuery::create()->findPk($tabId);

			if (null === $tab) {
				throw new \InvalidArgumentException(sprintf("%d tab id does not exist", $tabId));
			}

			$form = $this->validateForm($tabsProductForm);

			$event = $this->createEventInstance($form->getData());
			$event->setTabId($tab->getId());
			$event->setProductId($tab->getProductId());

			$this->dispatch(TabsEvent::TABS_PRODUCT_UPDATE, $event);

			return $this->generateSuccessRedirect($tabsProductForm);
		} catch (FormValidationException $e) {
			$message = sprintf("Please check your input: %s", $e->getMessage());
		} catch (PropelException $e) {
			$message = $e->getMessage();
		} catch (\Exception $e) {
			$message = sprintf("Sorry, an error occured: %s", $e->getMessage() . " " . $e->getFile());
		}

		if ($message !== false) {
			\Thelia\Log\Tlog::getInstance()->error(sprintf("Error during tabs product association process : %s.", $message));

			$tabsProductForm->setErrorMessage($message);

			$this->getParserContext()
				->addForm($tabsProductForm)
				->setGeneralError($message);
		}

		return $this->updateAction();

	}
}
