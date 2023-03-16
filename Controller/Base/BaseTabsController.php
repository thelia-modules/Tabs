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

namespace Tabs\Controller\Base;

use Symfony\Component\Form\FormFactoryBuilderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tabs\Event\TabsDeleteEvent;
use Tabs\Event\TabsEvent;
use Tabs\Model\ProductAssociatedTab;
use Tabs\Model\ProductAssociatedTabQuery;
use Thelia\Controller\Admin\AbstractCrudController;
use Thelia\Core\Event\UpdatePositionEvent;
use Thelia\Core\Template\ParserContext;
use Thelia\Core\Translation\Translator;
use Thelia\Form\CategoryModificationForm;
use Thelia\Form\ContentModificationForm;
use Thelia\Form\FolderModificationForm;
use Thelia\Form\ProductModificationForm;
use Thelia\Model\CategoryQuery;
use Thelia\Model\ContentQuery;
use Thelia\Model\FolderQuery;
use Thelia\Model\ProductQuery;
use Thelia\Tools\URL;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BaseTabsController
 * @package Tabs\Controller\Base
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
#[Route('/admin/module/Tabs', name: 'base_tabs_')]
class BaseTabsController extends AbstractCrudController
{
	static $possibleVars = [ 'product_id', 'category_id', 'content_id', 'folder_id'];

    private Request $request;
    private EventDispatcherInterface $eventDispatcher;
    private FormFactoryBuilderInterface $formFactoryBuilder;
    private ValidatorBuilder $validationBuilder;
    private TokenStorageInterface $tokenStorage;

	public function __construct(
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        Translator $translator,
        FormFactoryBuilderInterface $formFactoryBuilder,
        ValidatorBuilder $validationBuilder,
        TokenStorageInterface $tokenStorage
    ){
		parent::__construct(
			'tabs',
			'manual',
			'tabs_order',
			'admin.tabs',
			null,
			null,
			TabsEvent::TABS_DELETE,
			null,
			null,
			'Tabs'
		);

        $this->request = $requestStack->getCurrentRequest();
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactoryBuilder = $formFactoryBuilder;
        $this->validationBuilder = $validationBuilder;
        $this->translator = $translator;
        $this->tokenStorage = $tokenStorage;
	}

    #[Route('', name: 'config')]
	public function config()
	{
		return $this->render('tabs-config');
	}

    #[Route('/init', name: 'init_position')]
	public function initPosition()
	{
		$products = ProductQuery::create()
			->find();

		foreach ($products as $product) {
			$productTabs = ProductAssociatedTabQuery::create()
				->filterByProductId($product->getId())
				->find();

			$position = 1;

			/** @var ProductAssociatedTab $productTab */
			foreach ($productTabs as $productTab) {
				$productTab
					->setPosition($position)
					->save();

				$position++;
			}
		}

		return $this->render('tabs-config');
	}

    #[Route('/delete', name: 'delete_event_instance')]
    protected function createEventInstance($data)
	{
		$tabsAssociationEvent = new TabsEvent(
			empty($data["description"]) ? null : $data["description"],
			empty($data["locale"]) ? null : $data["locale"],
			empty($data["title"]) ? null : $data["title"],
			empty($data["visible"]) ? null : $data["visible"],
			empty($data["position"]) ? null : $data["position"],
			empty($data["product_id"]) ? null : $data["product_id"],
			empty($data["folder_id"]) ? null : $data["folder_id"],
			empty($data["category_id"]) ? null : $data["category_id"],
			empty($data["contenu_id"]) ? null : $data["contenu_id"]
        );

		return $tabsAssociationEvent;
	}

	/**
	 * Return the creation form for this object
	 */
	protected function getCreationForm()
	{
		// TODO: Implement getCreationForm() method.
	}

	/**
	 * Return the update form for this object
	 */
	protected function getUpdateForm()
	{
		// TODO: Implement getUpdateForm() method.
	}

	/**
	 * Hydrate the update form for this object, before passing it to the update template
	 */
	protected function hydrateObjectForm(
        ParserContext $parserContext,
        $object)
	{
		// Hydrate the "SEO" tab form
		$this->hydrateSeoForm($parserContext, $object);

		// Prepare the data that will hydrate the form
		$data = array(
			'id' => $object->getId(),
			'locale' => $object->getLocale(),
			'title' => $object->getTitle(),
			'chapo' => $object->getChapo(),
			'description' => $object->getDescription(),
			'postscriptum' => $object->getPostscriptum(),
			'visible' => $object->getVisible()
		);

		// Get type of association to hydrate the correct modification form
		if ($object->type === 'content') {
			// Setup the object form
			return new ContentModificationForm(
                $this->request,
                $this->eventDispatcher,
                $this->translator,
                $this->formFactoryBuilder,
                $this->validationBuilder,
                $this->tokenStorage
            );
		}

		if ($object->type === 'product') {
			// Setup the object form
			return new ProductModificationForm(
                $this->request,
                $this->eventDispatcher,
                $this->translator,
                $this->formFactoryBuilder,
                $this->validationBuilder,
                $this->tokenStorage
            );
		}

		if ($object->type === 'category') {
			// Setup the object form
			return new CategoryModificationForm(
                $this->request,
                $this->eventDispatcher,
                $this->translator,
                $this->formFactoryBuilder,
                $this->validationBuilder,
                $this->tokenStorage
            );
		}

		if ($object->type === 'folder') {
			// Setup the object form
			return new FolderModificationForm(
                $this->request,
                $this->eventDispatcher,
                $this->translator,
                $this->formFactoryBuilder,
                $this->validationBuilder,
                $this->tokenStorage
            );
		}
	}

	/**
	 * Creates the creation event with the provided form data
	 */
	protected function getCreationEvent($formData)
	{
		// TODO: Implement getCreationEvent() method.
	}

	/**
	 * Creates the update event with the provided form data
	 */
	protected function getUpdateEvent($formData)
	{
		// TODO: Implement getUpdateEvent() method.
	}

	/**
	 * Creates the delete event with the provided form data
	 */
	protected function getDeleteEvent()
	{
		return new TabsDeleteEvent(
            $this->request->get('description'),
            $this->request->get('locale'),
            $this->request->get('title'),
            $this->request->get('visible'),
            $this->request->get('position'),
            $this->request->get('productId'),
            $this->request->get('folderId'),
            $this->request->get('categoryId'),
            $this->request->get('contentId'),
            $this->request->get('tab_id')
        );
	}

	/**
	 * Return true if the event contains the object, e.g. the action has updated the object in the event.
	 */
	protected function eventContainsObject($event)
	{
		// TODO: Implement eventContainsObject() method.
	}

	/**
	 * Get the created object from an event.
	 */
	protected function getObjectFromEvent($event)
	{
		// TODO: Implement getObjectFromEvent() method.
	}

	/**
	 * Load an existing object from the database
	 */
	protected function getExistingObject()
	{
		$contentId = $this->request->get('content_id', null);

		// Create ContentQuery id contentId
		if (null !== $contentId) {
			$query = ContentQuery::create()
				->joinWithI18n($this->getCurrentEditionLocale())
				->findOneById($contentId);

			// Set type of association
			$query->type = 'content';

			return $query;
		}

		$productId = $this->request->get('product_id', null);

		// Create ContentQuery id contentId
		if (null !== $productId) {
			$query = ProductQuery::create()
				->joinWithI18n($this->getCurrentEditionLocale())
				->findOneById($productId);

			// Set type of association
			$query->type = 'product';

			return $query;
		}

		$folderId = $this->request->get('folder_id', null);

		// Create ContentQuery id contentId
		if (null !== $folderId) {
			$query = FolderQuery::create()
				->joinWithI18n($this->getCurrentEditionLocale())
				->findOneById($folderId);

			// Set type of association
			$query->type = 'folder';

			return $query;
		}

		$categoryId = $this->request->get('category_id', null);

		// Create ContentQuery id contentId
		if (null !== $categoryId) {
			$query = CategoryQuery::create()
				->joinWithI18n($this->getCurrentEditionLocale())
				->findOneById($categoryId);

			// Set type of association
			$query->type = 'category';

			return $query;
		}

		return null;
	}

	/**
	 * Returns the object label form the object event (name, title, etc.)
	 */
	protected function getObjectLabel($object)
	{
		// TODO: Implement getObjectLabel() method.
	}

	/**
	 * Returns the object ID from the object
	 */
	protected function getObjectId($object)
	{
		// TODO: Implement getObjectId() method.
	}

	/**
	 * Render the main list template
	 */
	protected function renderListTemplate($currentTabs)
	{
		// TODO: Implement renderListTemplate() method.
	}

	protected function getFolderId()
	{
		$folderId = $this->request->get('folder_id', null);

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
		$category_id = $this->request->get('category_id', null);

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
		$args = array();

		// Return args for content association
		$contentId = $this->request->get('content_id', null);

		if (null !== $contentId) {
			$args = array(
				'content_id' => $this->request->get('content_id', 0),
				'current_tab' => $this->request->get('current_tab', 'general'),
				'folder_id' => $this->getFolderId()
			);
		}

		// Return args for product association
		$productId = $this->request->get('product_id', null);

		if (null !== $productId) {
			$args = array(
				'product_id' => $this->request->get('product_id', 0),
				'current_tab' => $this->request->get('current_tab', 'general'),
				'category_id' => $this->getCategoryId()
			);
		}

		// Return args for category association
		$categoryId = $this->request->get('category_id', null);

		if (null !== $categoryId) {
			$args = array(
				'category_id' => $this->request->get('category_id', 0),
				'current_tab' => $this->request->get('current_tab', 'general'),
			);
		}

		// Return args for folder association
		$folderId = $this->request->get('folder_id', null);

		if (null !== $folderId) {
			$args = array(
				'folder_id' => $this->request->get('folder_id', 0),
				'current_tab' => $this->request->get('current_tab', 'general'),
			);
		}

		return $args;
	}

	/**
	 * Render the edition template
	 */
	protected function renderEditionTemplate()
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

		// Render product-edit if category_id
		if (isset($args['category_id']) && null !== $args['category_id']) {
			return $this->render('category-edit', $args);
		}

		// Render product-edit if folder_id
		if (isset($args['folder_id']) && null !== $args['folder_id']) {
			return $this->render('folder-edit', $args);
		}

		return $this->render('home');
	}

	/**
	 * Redirect to the edition template
	 */
	protected function redirectToEditionTemplate()
	{
		$productId = $this->request->get('product_id');

		return new RedirectResponse(URL::getInstance()->absoluteUrl("/admin/products/update",
			["product_id" => $productId, "current_tab" => 'modules']));
	}

	/**
	 * Redirect to the list template
	 */
	protected function redirectToListTemplate()
	{
		foreach (self::$possibleVars as $var) {
			if (null !== $resourceId = $this->request->get($var)) {
				switch ($var) {
					case 'product_id' :
						$uri = '/admin/products/update';
						$param = [$var => $resourceId, "current_tab" => 'modules'];
						break 2;
					case 'category_id' :
						$uri = '/admin/categories/update';
						$param = [$var => $resourceId, "current_tab" => 'modules'];
						break 2;
					case 'folder_id' :
						$uri = '/admin/folders/update/' . $resourceId . '#modules';
						$param = ["current_tab" => 'modules'];
						break 2;
					case 'content_id' :
						$uri = '/admin/content/update/' . $resourceId . '#modules';
						$param = ["current_tab" => 'modules'];
						break 2;
					default :
						throw new NotFoundHttpException('Resource key not found');
				}
			}
		}

		return new RedirectResponse(URL::getInstance()->absoluteUrl($uri, $param));
	}

	/**
	 * Put in this method post object delete processing if required.
	 */
	protected function performAdditionalDeleteAction($deleteEvent)
	{
		if (null !== $deleteEvent->getContentId()) {
			$url = '/admin/content/update/' . $deleteEvent->getContentId();
		}

		if (null !== $deleteEvent->getProductId()) {
			$url = '/admin/products/update?product_id=' . $deleteEvent->getProductId();
		}

		if (null !== $deleteEvent->getCategoryId()) {
			$url = '/admin/categories/update?category_id=' . $deleteEvent->getCategoryId();
		}

		if (null !== $deleteEvent->getFolderId()) {
			$url = '/admin/folders/update/' . $deleteEvent->getFolderId();
		}

		return $this->generateRedirect(
			URL::getInstance()->absoluteUrl($url, ['current_tab' => 'modules'])
		);
	}

	protected function createUpdatePositionEvent($positionChangeMode, $positionValue)
	{
		foreach (self::$possibleVars as $var) {
			if (null !== $this->request->get($var)) {
				// Change our parent event name :)
				switch ($var) {
					case 'content_id':
						$this->changePositionEventIdentifier = TabsEvent::TABS_CONTENT_POSITION_UPDATE;
						break 2;
					case 'category_id':
						$this->changePositionEventIdentifier = TabsEvent::TABS_CATEGORY_POSITION_UPDATE;
						break 2;
					case 'product_id':
						$this->changePositionEventIdentifier = TabsEvent::TABS_PRODUCT_POSITION_UPDATE;
						break 2;
					case 'folder_id':
						$this->changePositionEventIdentifier = TabsEvent::TABS_FOLDER_POSITION_UPDATE;
						break 2;
				}
			}
		}

		return new UpdatePositionEvent(
            $this->request->get('tab_id', null),
			$positionChangeMode,
			$positionValue
		);
	}

    #[Route('/update-position', name: 'update_position')]
    protected function performAdditionalUpdatePositionAction($positionChangeEvent)
	{
		return $this->redirectToListTemplate();
	}
}
