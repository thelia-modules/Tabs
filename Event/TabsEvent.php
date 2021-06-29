<?php

namespace Tabs\Event;


use Thelia\Core\Event\ActionEvent;

class TabsEvent extends ActionEvent
{

	const TABS_CATEGORY_CREATE            = 'tabs.category.action.create';
	const TABS_CATEGORY_UPDATE            = 'tabs.category.action.update';

	const TABS_FOLDER_CREATE            = 'tabs.folder.action.create';
	const TABS_FOLDER_UPDATE            = 'tabs.folder.action.update';
	const TABS_CONTENT_CREATE            = 'tabs.content.action.create';
	const TABS_CONTENT_UPDATE            = 'tabs.content.action.update';

	const TABS_PRODUCT_CREATE            = 'tabs.product.action.create';
	const TABS_PRODUCT_UPDATE            = 'tabs.product.action.update';

	const TABS_DELETE                    = 'tabs.action.delete';
	const TABS_CONTENT_POSITION_UPDATE           = 'tabs.content.position.update';
	const TABS_CATEGORY_POSITION_UPDATE           = 'tabs.category.position.update';
	const TABS_FOLDER_POSITION_UPDATE           = 'tabs.folder.position.update';
	const TABS_PRODUCT_POSITION_UPDATE           = 'tabs.product.position.update';

	protected $locale;
	protected $title;
	protected $description;
	protected $visible;
	protected $contentId;
	protected $productId;
	protected $categoryId;
	protected $folderId;
	protected $position;
	protected $tabId;

	function __construct($description, $locale, $title, $visible, $position, $productId, $folderId, $categoryId, $contentId)
	{
		$this->description = $description;
		$this->locale = $locale;
		$this->title = $title;
		$this->visible = $visible;
		$this->position = $position;
		$this->productId = $productId;
		$this->contentId = $contentId;
		$this->categoryId = $categoryId;
		$this->folderId = $folderId;
	}

	/**
	 * @return mixed
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * @param mixed $position
	 * @return TabsEvent
	 */
	public function setPosition($position)
	{
		$this->position = $position;
		return $this;
	}

	/**
	 * @param mixed $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param mixed $locale
	 */
	public function setLocale($locale)
	{
		$this->locale = $locale;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getLocale()
	{
		return $this->locale;
	}

	/**
	 * @param mixed $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param mixed $visible
	 */
	public function setVisible($visible)
	{
		$this->visible = $visible;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getVisible()
	{
		return $this->visible;
	}

	/**
	 * @param mixed $tabId
	 */
	public function setTabId($tabId)
	{
		$this->tabId = $tabId;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTabId()
	{
		return $this->tabId;
	}

	/**
	 * @param mixed $productId
	 */
	public function setProductId($productId)
	{
		$this->productId = $productId;
	}

	/**
	 * @return mixed
	 */
	public function getProductId()
	{
		return $this->productId;
	}
	/**
	 * @param mixed $categoryId
	 */
	public function setCategoryId($categoryId)
	{
		$this->categoryId = $categoryId;
	}

	/**
	 * @return mixed
	 */
	public function getCategoryId()
	{
		return $this->categoryId;
	}

	/**
	 * @param mixed $folderId
	 */
	public function setFolderId($folderId)
	{
		$this->folderId = $folderId;
	}

	/**
	 * @return mixed
	 */
	public function getFolderId()
	{
		return $this->folderId;
	}

	/**
	 * @param mixed $contentId
	 */
	public function setContentId($contentId)
	{
		$this->contentId = $contentId;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getContentId()
	{
		return $this->contentId;
	}

} 