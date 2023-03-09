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

namespace Tabs\Event;

/**
 * Class TabsDeleteEvent
 * @package Tabs\Event
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class TabsDeleteEvent extends TabsEvent
{
    /**
     * @var int tabId
     */
    protected $tabId;

    function __construct($description, $locale, $title, $visible, $position, $productId, $folderId, $categoryId, $contentId, $tabId)
    {
        parent::__construct($description, $locale, $title, $visible, $position, $productId, $folderId, $categoryId, $contentId);
        $this->tabId = $tabId;
    }

    /**
     * @param int $tabId
     */
    public function setTabId($tabId)
    {
        $this->tabId = $tabId;
    }

    /**
     * @return int
     */
    public function getTabId()
    {
        return $this->tabId;
    }

}
