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
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Validator\ValidatorBuilder;
use Tabs\Controller\Base\BaseTabsController;
use Tabs\Event\TabsEvent;
use Tabs\Form\TabsContentForm;
use Tabs\Model\ContentAssociatedTabQuery;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Template\ParserContext;
use Thelia\Core\Translation\Translator;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\ContentQuery;

/**
 * Class ContentTabsController
 * @package Tabs\Controller
 * @author Michaël Espeche <mespeche@openstudio.fr>
 * @Route("/admin/content", name="tabs_content_")
 */
class ContentTabsController extends BaseTabsController
{
	public function __construct(
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        Translator $translator,
        FormFactoryBuilderInterface $formFactoryBuilder,
        ValidatorBuilder $validationBuilder,
        TokenStorageInterface $tokenStorage
    )
	{
		parent::__construct(
            $requestStack,
            $eventDispatcher,
            $translator,
            $formFactoryBuilder,
            $validationBuilder,
            $tokenStorage
        );
	}

    #[Route('/update/{contentId}/tabs', name: 'manage_tabs_content')]
	public function manageTabsContentAssociation(
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        Translator $translator,
        FormFactoryBuilderInterface $formFactoryBuilder,
        ValidatorBuilder $validationBuilder,
        TokenStorageInterface $tokenStorage,
        ParserContext $parserContext,
        $contentId
    ){

		if (null !== $response = $this->checkAuth(array(), array('Tabs'), AccessManager::UPDATE)) {
			return $response;
		}

		$tabId = $requestStack->getCurrentRequest()->get('tab_id', null);
		if (null === $tabId) {
			return $this->createNewTabContentAssociation(
                $requestStack,
                $eventDispatcher,
                $translator,
                $formFactoryBuilder,
                $validationBuilder,
                $tokenStorage,
                $parserContext,
                $contentId);
		}
        return $this->updateTabContentAssociation(
            $requestStack,
            $eventDispatcher,
            $translator,
            $formFactoryBuilder,
            $validationBuilder,
            $tokenStorage,
            $parserContext,
            $tabId
        );
	}

	public function createNewTabContentAssociation(
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        Translator $translator,
        FormFactoryBuilderInterface $formFactoryBuilder,
        ValidatorBuilder $validationBuilder,
        TokenStorageInterface $tokenStorage,
        ParserContext $parserContext,
        $contentId)
	{

		$tabsContentForm = new TabsContentForm(
            $requestStack->getCurrentRequest(),
            $eventDispatcher,
            $translator,
            $formFactoryBuilder,
            $validationBuilder,
            $tokenStorage,
            $contentId
        );

		$message = false;

		try {
			$content = ContentQuery::create()->findPk($contentId);

			if (null === $content) {
				throw new \InvalidArgumentException(sprintf("%d content id does not exist", $contentId));
			}

			$form = $this->validateForm($tabsContentForm);

			$event = $this->createEventInstance($form->getData());
			$event->setContentId($content->getId());

            $eventDispatcher->dispatch($event, TabsEvent::TABS_CONTENT_CREATE);

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

			$parserContext
				->addForm($tabsContentForm)
				->setGeneralError($message);
		}

		return $this->updateAction($parserContext);
	}

	public function updateTabContentAssociation(
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        Translator $translator,
        FormFactoryBuilderInterface $formFactoryBuilder,
        ValidatorBuilder $validationBuilder,
        TokenStorageInterface $tokenStorage,
        ParserContext $parserContext,
        $tabId)
	{

		$tabsContentForm = new TabsContentForm(
            $requestStack->getCurrentRequest(),
            $eventDispatcher,
            $translator,
            $formFactoryBuilder,
            $validationBuilder,
            $tokenStorage,
            $tabId
        );

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

            $eventDispatcher->dispatch($event, TabsEvent::TABS_CONTENT_UPDATE);

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

			$parserContext
				->addForm($tabsContentForm)
				->setGeneralError($message);
		}

		return $this->updateAction($parserContext);
	}
}
