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

namespace Tabs;

use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Install\Database;
use Thelia\Module\BaseModule;
use Symfony\Component\Finder\Finder;

class Tabs extends BaseModule
{
	const MESSAGE_DOMAIN_BO = "tabs";
	const UPDATE_PATH = __DIR__ . DS . 'Config' . DS . 'update';

	public function postActivation(ConnectionInterface $con = null)
	{
        try {
            ContentAssociatedTabQuery::create()->findOne();
            ProductAssociatedTabQuery::create()->findOne();
            FolderAssociatedTabQuery::create()->findOne();
            CategoryAssociatedTabQuery::create()->findOne();
        } catch (\Exception $ex) {
            $database = new Database($con->getWrappedConnection());
            $database->insertSql(null, array(THELIA_ROOT . '/local/modules/Tabs/Config/thelia.sql'));
        }
	}

	public function update($currentVersion, $newVersion, ConnectionInterface $con = null)
	{
		$finder = (new Finder())->files()->name('#.*?\.sql#')->sortByName()->in(self::UPDATE_PATH);

		if ($finder->count() === 0) {
			return;
		}

		$database = new Database($con);

		/** @var \Symfony\Component\Finder\SplFileInfo $updateSQLFile */
		foreach ($finder as $updateSQLFile) {
			if (version_compare($currentVersion, str_replace('.sql', '', $updateSQLFile->getFilename()), '<')) {
				$database->insertSql(null, [$updateSQLFile->getPathname()]);
			}
		}
	}
}
