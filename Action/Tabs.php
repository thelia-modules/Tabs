<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
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
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace Tabs\Action;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tabs\Event\TabsDeleteEvent;
use Tabs\Event\TabsEvent;
use Tabs\Model\Base\ContentAssociatedTabQuery;
use Tabs\Model\ContentAssociatedTab;
use Tabs\Model\ProductAssociatedTab;
use Tabs\Model\ProductAssociatedTabQuery;

/**
 *
 * Tabs class where all actions are managed
 *
 * Class Tabs
 * @package Tabs\Action
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class Tabs implements EventSubscriberInterface
{

    public function tabsContentCreate(TabsEvent $event)
    {
        $association = new ContentAssociatedTab();

        $association
            ->setContentId($event->getContentId())
            ->setVisible($event->getVisible())
            ->setLocale($event->getLocale())
            ->setTitle($event->getTitle())
            ->setDescription($event->getDescription())
            ->save();
    }

    public function tabsContentUpdate(TabsEvent $event)
    {
        if (null !== $tab = ContentAssociatedTabQuery::create()->findPk($event->getTabId())) {
            $tab
                ->setContentId($event->getContentId())
                ->setVisible($event->getVisible())
                ->setLocale($event->getLocale())
                ->setTitle($event->getTitle())
                ->setDescription($event->getDescription())
                ->save();

        }

    }

    public function tabsDelete(TabsDeleteEvent $event)
    {

        if (null !== $tab = ContentAssociatedTabQuery::create()->findPk($event->getTabId())) {
            $tab->delete();

            $event->setContentId($tab->getContentId());
        }

        if (null !== $tab = ProductAssociatedTabQuery::create()->findPk($event->getTabId())) {
            $tab->delete();

            $event->setProductId($tab->getProductId());
        }

    }

    public function tabsProductCreate(TabsEvent $event)
    {

        $association = new ProductAssociatedTab();

        $association
            ->setProductId($event->getProductId())
            ->setVisible($event->getVisible())
            ->setLocale($event->getLocale())
            ->setTitle($event->getTitle())
            ->setDescription($event->getDescription())
            ->save();
    }

    public function tabsProductUpdate(TabsEvent $event)
    {

        if (null !== $tab = ProductAssociatedTabQuery::create()->findPk($event->getTabId())) {
            $tab
                ->setProductId($event->getProductId())
                ->setVisible($event->getVisible())
                ->setLocale($event->getLocale())
                ->setTitle($event->getTitle())
                ->setDescription($event->getDescription())
                ->save();

        }

    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            TabsEvent::TABS_CONTENT_CREATE => array('tabsContentCreate', 128),
            TabsEvent::TABS_CONTENT_UPDATE => array('tabsContentUpdate', 128),

            TabsEvent::TABS_PRODUCT_CREATE => array('tabsProductCreate', 128),
            TabsEvent::TABS_PRODUCT_UPDATE => array('tabsProductUpdate', 128),

            TabsEvent::TABS_DELETE => array('tabsDelete', 128)
        );
    }
}
