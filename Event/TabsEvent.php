<?php

namespace Tabs\Event;


use Thelia\Core\Event\ActionEvent;

class TabsEvent extends ActionEvent{

    const TABS_CONTENT_CREATE            = 'tabs.content.action.create';
    const TABS_CONTENT_UPDATE            = 'tabs.content.action.update';

    const TABS_PRODUCT_CREATE            = 'tabs.product.action.create';
    const TABS_PRODUCT_UPDATE            = 'tabs.product.action.update';

    const TABS_DELETE                    = 'tabs.action.delete';

    protected $locale;
    protected $title;
    protected $description;
    protected $visible;
    protected $contentId;
    protected $productId;
    protected $tabId;

    function __construct($description, $locale, $title, $visible)
    {
        $this->description = $description;
        $this->locale = $locale;
        $this->title = $title;
        $this->visible = $visible;
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

} 