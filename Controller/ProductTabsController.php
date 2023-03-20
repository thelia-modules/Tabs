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
use Tabs\Form\TabsProductForm;
use Tabs\Model\ProductAssociatedTabQuery;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Template\ParserContext;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\ProductQuery;

/**
 * Class ProductTabsController
 * @package Tabs\Controller
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
#[Route('/admin/product', name: 'tabs_product_')]
class ProductTabsController extends BaseTabsController
{
    #[Route('/update/{productId}/tabs', name: 'manage_tabs_product')]
	public function manageTabsProductAssociation(
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        ParserContext $parserContext,
        $productId)
	{

		if (null !== $response = $this->checkAuth(array(), array('Tabs'), AccessManager::UPDATE)) {
			return $response;
		}

		$tabId = $requestStack->getCurrentRequest()->get('tab_id', null);

		if (null === $tabId) {
			return $this->createNewTabProductAssociation(
                $eventDispatcher,
                $parserContext,
                $productId
            );
		}
        return $this->updateTabProductAssociation(
            $eventDispatcher,
            $parserContext,
            $tabId
        );
	}

	public function createNewTabProductAssociation(
        EventDispatcherInterface $eventDispatcher,
        ParserContext $parserContext,
        $productId
    ) {

		$tabsProductForm = $this->createForm(TabsProductForm::getName());

		$message = false;

		try {
			$product = ProductQuery::create()->findPk($productId);

			if (null === $product) {
				throw new \InvalidArgumentException(sprintf("%d content id does not exist", $productId));
			}

			$form = $this->validateForm($tabsProductForm);

			$event = $this->createEventInstance($form->getData());
			$event->setProductId($product->getId());

            $eventDispatcher->dispatch($event, TabsEvent::TABS_PRODUCT_CREATE);

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

			$parserContext
				->addForm($tabsProductForm)
				->setGeneralError($message);
		}

		return $this->updateAction($parserContext);
	}

	public function updateTabProductAssociation(
        EventDispatcherInterface $eventDispatcher,
        ParserContext $parserContext,
        $tabId
    ) {
		$tabsProductForm = $this->createForm(TabsProductForm::getName());

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

            $eventDispatcher->dispatch($event, TabsEvent::TABS_PRODUCT_UPDATE);

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

			$parserContext
				->addForm($tabsProductForm)
				->setGeneralError($message);
		}

		return $this->updateAction($parserContext);
	}
}
