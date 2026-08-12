<?php

declare(strict_types=1);

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*      Copyright (c) OpenStudio */
/*      email : info@thelia.net */
/*      web : http://www.thelia.net */

/*      This program is free software; you can redistribute it and/or modify */
/*      it under the terms of the GNU General Public License as published by */
/*      the Free Software Foundation; either version 3 of the License */

/*      This program is distributed in the hope that it will be useful, */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the */
/*      GNU General Public License for more details. */

/*      You should have received a copy of the GNU General Public License */
/*      along with this program. If not, see <http://www.gnu.org/licenses/>. */

namespace Tabs\Controller\Admin;

use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Event\ActiveRecordEvent;
use Propel\Runtime\Exception\PropelException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tabs\Event\TabsDeleteEvent;
use Tabs\Event\TabsEvent;
use Tabs\Form\TabsContentForm;
use Tabs\Form\TabsProductForm;
use Tabs\Model\ContentAssociatedTabQuery;
use Tabs\Model\ProductAssociatedTabQuery;
use Thelia\Controller\Admin\AbstractSeoCrudController;
use Thelia\Core\Event\ActionEvent;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Template\ParserContext;
use Thelia\Form\BaseForm;
use Thelia\Form\ContentModificationForm;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Form\ProductModificationForm;
use Thelia\Log\Tlog;
use Thelia\Model\Base\ProductQuery;
use Thelia\Model\ContentQuery;
use Thelia\Tools\TokenProvider;
use Thelia\Tools\URL;

/**
 * Class TabsController.
 *
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class TabsController extends AbstractSeoCrudController
{
    public function __construct(
        public ParserContext $parserContext,
        public RequestStack $requestStack,
        protected EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct(
            'tabs',
            'manual',
            'tabs_order',
            'admin.tabs',
            null,
            null,
            TabsEvent::TABS_DELETE,
            null,
            null
        );
    }

    #[Route('/admin/content/update/{contentId}/tabs', name: 'tab_manage_tab_content')]
    public function manageTabsContentAssociation(int $contentId)
    {
        if (null !== $response = $this->checkAuth([], ['Tabs'], AccessManager::UPDATE)) {
            return $response;
        }

        $tabId = $this->requestStack->getCurrentRequest()->get('tab_id', null);
        if (null === $tabId) {
            return $this->createNewTabContentAssociation($contentId);
        }

        return $this->updateTabContentAssociation($tabId);
    }

    #[Route('/admin/modules/tabs/delete', name: 'tab_delete_action')]
    public function deleteAction(
        Request $request,
        TokenProvider $tokenProvider,
        EventDispatcherInterface $eventDispatcher,
        ParserContext $parserContext,
    ): Response|RedirectResponse {
        return parent::deleteAction($request, $tokenProvider, $eventDispatcher, $parserContext);
    }

    #[Route('/admin/product/update/{productId}/tabs', name: 'tab_manage_tab_assoc')]
    public function manageTabsProductAssociation(int $productId)
    {
        if (null !== $response = $this->checkAuth([], ['Tabs'], AccessManager::UPDATE)) {
            return $response;
        }

        $tabId = $this->requestStack->getCurrentRequest()->get('tab_id', null);
        if (null === $tabId) {
            return $this->createNewTabProductAssociation($productId);
        }

        return $this->updateTabProductAssociation($tabId);
    }

    public function createNewTabContentAssociation(int $contentId)
    {
        $tabsContentForm = new TabsContentForm();

        $message = false;

        try {
            $content = ContentQuery::create()->findPk($contentId);

            if (null === $content) {
                throw new \InvalidArgumentException(\sprintf('%d content id does not exist', $contentId));
            }

            $form = $this->validateForm($tabsContentForm);

            $event = $this->createEventInstance($form->getData());
            $event->setContentId($content->getId());

            $this->eventDispatcher->dispatch($event, TabsEvent::TABS_CONTENT_CREATE);

            return $this->generateSuccessRedirect($tabsContentForm);
        } catch (FormValidationException $e) {
            $message = \sprintf('Please check your input: %s', $e->getMessage());
        } catch (PropelException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $message = \sprintf('Sorry, an error occured: %s', $e->getMessage().' '.$e->getFile());
        }

        if ($message !== false) {
            Tlog::getInstance()->error(\sprintf('Error during tabs content association process : %s.', $message));

            $tabsContentForm->setErrorMessage($message);

            $this->parserContext
                ->addForm($tabsContentForm)
                ->setGeneralError($message);
        }

        return $this->updateAction($this->parserContext);
    }

    public function updateTabContentAssociation(int $tabId)
    {
        $tabsContentForm = new TabsContentForm();

        $message = false;

        try {
            $tab = ContentAssociatedTabQuery::create()->findPk($tabId);

            if (null === $tab) {
                throw new \InvalidArgumentException(\sprintf('%d tab id does not exist', $tabId));
            }

            $form = $this->validateForm($tabsContentForm);

            $event = $this->createEventInstance($form->getData());
            $event->setTabId($tab->getId());
            $event->setContentId($tab->getContentId());

            $this->eventDispatcher->dispatch($event, TabsEvent::TABS_CONTENT_UPDATE);

            return $this->generateSuccessRedirect($tabsContentForm);
        } catch (FormValidationException $e) {
            $message = \sprintf('Please check your input: %s', $e->getMessage());
        } catch (PropelException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $message = \sprintf('Sorry, an error occured: %s', $e->getMessage().' '.$e->getFile());
        }

        if ($message !== false) {
            Tlog::getInstance()->error(\sprintf('Error during tabs content association process : %s.', $message));

            $tabsContentForm->setErrorMessage($message);

            $this->parserContext
                ->addForm($tabsContentForm)
                ->setGeneralError($message);
        }

        return $this->updateAction($this->parserContext);
    }

    public function createNewTabProductAssociation(int $productId)
    {
        $tabsProductForm = new TabsProductForm();

        $message = false;

        try {
            $product = ProductQuery::create()->findPk($productId);

            if (null === $product) {
                throw new \InvalidArgumentException(\sprintf('%d content id does not exist', $productId));
            }

            $form = $this->validateForm($tabsProductForm);

            $event = $this->createEventInstance($form->getData());
            $event->setProductId($product->getId());

            $this->eventDispatcher->dispatch($event, TabsEvent::TABS_PRODUCT_CREATE);

            return $this->generateSuccessRedirect($tabsProductForm);
        } catch (FormValidationException $e) {
            $message = \sprintf('Please check your input: %s', $e->getMessage());
        } catch (PropelException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $message = \sprintf('Sorry, an error occured: %s', $e->getMessage().' '.$e->getFile());
        }

        if ($message !== false) {
            Tlog::getInstance()->error(\sprintf('Error during tabs product association process : %s.', $message));

            $tabsProductForm->setErrorMessage($message);

            $this->parserContext
                ->addForm($tabsProductForm)
                ->setGeneralError($message);
        }

        return $this->updateAction($this->parserContext);
    }

    public function updateTabProductAssociation(int $tabId)
    {
        $tabsProductForm = new TabsProductForm();

        $message = false;

        try {
            $tab = ProductAssociatedTabQuery::create()->findPk($tabId);

            if (null === $tab) {
                throw new \InvalidArgumentException(\sprintf('%d tab id does not exist', $tabId));
            }

            $form = $this->validateForm($tabsProductForm);

            $event = $this->createEventInstance($form->getData());
            $event->setTabId($tab->getId());
            $event->setProductId($tab->getProductId());

            $this->eventDispatcher->dispatch($event, TabsEvent::TABS_PRODUCT_UPDATE);

            return $this->generateSuccessRedirect($tabsProductForm);
        } catch (FormValidationException $e) {
            $message = \sprintf('Please check your input: %s', $e->getMessage());
        } catch (PropelException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $message = \sprintf('Sorry, an error occured: %s', $e->getMessage().' '.$e->getFile());
        }

        if ($message !== false) {
            Tlog::getInstance()->error(\sprintf('Error during tabs product association process : %s.', $message));

            $tabsProductForm->setErrorMessage($message);

            $this->parserContext
                ->addForm($tabsProductForm)
                ->setGeneralError($message);
        }

        return $this->updateAction($this->parserContext);
    }

    /**
     * @return TabsEvent
     */
    private function createEventInstance(mixed $data)
    {
        $tabsAssociationEvent = new TabsEvent(
            empty($data['description']) ? null : $data['description'],
            empty($data['locale']) ? null : $data['locale'],
            empty($data['title']) ? null : $data['title'],
            empty($data['visible']) ? null : $data['visible']
        );

        return $tabsAssociationEvent;
    }

    /**
     * Return the creation form for this object.
     */
    protected function getCreationForm(): ?BaseForm
    {
        return parent::getCreationForm();
    }

    /**
     * Return the update form for this object.
     */
    protected function getUpdateForm(): ?BaseForm
    {
        return parent::getCreationForm();
    }

    /**
     * Hydrate the update form for this object, before passing it to the update template.
     */
    protected function hydrateObjectForm(ParserContext $parserContext, mixed $object): BaseForm
    {
        // Hydrate the "SEO" tab form
        $this->hydrateSeoForm($parserContext, $object);

        // Prepare the data that will hydrate the form
        $data = [
            'id' => $object->getId(),
            'locale' => $object->getLocale(),
            'title' => $object->getTitle(),
            'chapo' => $object->getChapo(),
            'description' => $object->getDescription(),
            'postscriptum' => $object->getPostscriptum(),
            'visible' => $object->getVisible(),
        ];

        // Get type of association to hydrate the correct modification form
        if ($object->type === 'content') {
            // Setup the object form
            return new ContentModificationForm($this->requestStack->getCurrentRequest(), 'form', $data);
        }

        if ($object->type === 'product') {
            // Setup the object form
            return new ProductModificationForm($this->requestStack->getCurrentRequest(), 'form', $data);
        }

        return parent::hydrateObjectForm($parserContext, $object);
    }

    /**
     * Creates the creation event with the provided form data.
     */
    protected function getCreationEvent(array $formData): ActionEvent|ActiveRecordEvent|null
    {
        return parent::getCreationEvent($formData);
    }

    /**
     * Creates the update event with the provided form data.
     */
    protected function getUpdateEvent(array $formData): ActionEvent|ActiveRecordEvent|null
    {
        return parent::getUpdateEvent($formData);
    }

    /**
     * Creates the delete event with the provided form data.
     */
    protected function getDeleteEvent(): ActiveRecordEvent|ActionEvent|null
    {
        return new TabsDeleteEvent($this->requestStack->getCurrentRequest()->get('tab_id'));
    }

    /**
     * Return true if the event contains the object, e.g. the action has updated the object in the event.
     */
    protected function eventContainsObject(Event $event): bool
    {
        return parent::eventContainsObject($event);
    }

    /**
     * Get the created object from an event.
     */
    protected function getObjectFromEvent(Event $event): mixed
    {
        return parent::getObjectFromEvent($event);
    }

    /**
     * Load an existing object from the database.
     */
    protected function getExistingObject(): ?ActiveRecordInterface
    {
        $contentId = $this->requestStack->getCurrentRequest()->get('content_id', null);

        // Create ContentQuery id contentId
        if (null !== $contentId) {
            $query = ContentQuery::create()
                ->joinWithI18n($this->getCurrentEditionLocale())
                ->findOneById($contentId);

            // Set type of association
            $query->type = 'content';

            return $query;
        }

        $productId = $this->requestStack->getCurrentRequest()->get('product_id', null);

        // Create ContentQuery id contentId
        if (null !== $productId) {
            $query = ProductQuery::create()
                ->joinWithI18n($this->getCurrentEditionLocale())
                ->findOneById($productId);

            // Set type of association
            $query->type = 'product';

            return $query;
        }

        return null;
    }

    /**
     * Returns the object label form the object event (name, title, etc.).
     */
    protected function getObjectLabel(ActiveRecordInterface $object): ?string
    {
        return parent::getObjectLabel($object);
    }

    /**
     * Returns the object ID from the object.
     */
    protected function getObjectId(ActiveRecordInterface $object): int
    {
        return parent::getObjectId($object);
    }

    /**
     * Render the main list template.
     */
    protected function renderListTemplate(string $currentOrder): Response
    {
        return parent::renderListTemplate($currentOrder);
    }

    protected function getFolderId()
    {
        $folderId = $this->requestStack->getCurrentRequest()->get('folder_id', null);

        if (null === $folderId) {
            $content = $this->getExistingObject();

            if ($content) {
                $folderId = $content->getDefaultFolderId();
            }
        }

        return $folderId ?: 0;
    }

    protected function getCategoryId()
    {
        $category_id = $this->requestStack->getCurrentRequest()->get('category_id', null);

        if ($category_id == null) {
            $product = $this->getExistingObject();

            if ($product !== null) {
                $category_id = $product->getDefaultCategoryId();
            }
        }

        return $category_id != null ? $category_id : 0;
    }

    protected function getEditionArguments()
    {
        $args = [];

        // Return args for content association
        $contentId = $this->requestStack->getCurrentRequest()->get('content_id', null);

        if (null !== $contentId) {
            $args = [
                'content_id' => $this->requestStack->getCurrentRequest()->get('content_id', 0),
                'current_tab' => $this->requestStack->getCurrentRequest()->get('current_tab', 'general'),
                'folder_id' => $this->getFolderId(),
            ];
        }

        // Return args for product association
        $productId = $this->requestStack->getCurrentRequest()->get('product_id', null);

        if (null !== $productId) {
            $args = [
                'product_id' => $this->requestStack->getCurrentRequest()->get('product_id', 0),
                'current_tab' => $this->requestStack->getCurrentRequest()->get('current_tab', 'general'),
                'category_id' => $this->getCategoryId(),
            ];
        }

        return $args;
    }

    /**
     * Render the edition template.
     */
    protected function renderEditionTemplate(): Response
    {
        $args = $this->getEditionArguments();

        // Render content-edit if content_id
        if (isset($args['content_id']) && null !== $args['content_id']) {
            return $this->render('content-edit', $args);
        }

        // Render product-edit if product_id
        if (isset($args['product_id']) && null !== $args['product_id']) {
            return $this->render('product-edit', $args);
        }

        return $this->render('home');
    }

    /**
     * Redirect to the edition template.
     */
    protected function redirectToEditionTemplate(): Response|RedirectResponse
    {
        return parent::redirectToEditionTemplate();
    }

    /**
     * Redirect to the list template.
     */
    protected function redirectToListTemplate(): Response|RedirectResponse
    {
        return parent::redirectToListTemplate();
    }

    /**
     * Put in this method post object delete processing if required.
     */
    protected function performAdditionalDeleteAction(ActionEvent|ActiveRecordEvent|null $deleteEvent): ?Response
    {
        if (null !== $deleteEvent->getContentId()) {
            $url = '/admin/content/update/'.$deleteEvent->getContentId();
        }

        if (null !== $deleteEvent->getProductId()) {
            $url = '/admin/products/update?product_id='.$deleteEvent->getProductId();
        }

        return $this->generateRedirect(
            URL::getInstance()->absoluteUrl($url, ['current_tab' => 'modules'])
        );
    }
}
