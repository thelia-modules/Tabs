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
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>. */

namespace Tabs;

use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Symfony\Component\Finder\Finder;
use Thelia\Core\Install\Database;
use Thelia\Module\BaseModule;

class Tabs extends BaseModule
{
    public const string DOMAIN_NAME = 'tabs';

    public function postActivation(?ConnectionInterface $con = null): void
    {
        if ('1' === $this->getConfigValue('is_initialized')) {
            return;
        }

        (new Database($con))->insertSql(null, [__DIR__.'/Config/TheliaMain.sql']);

        $this->setConfigValue('is_initialized', '1');
    }

    /**
     * Run every Config/update/<version>.sql newer than the installed version.
     */
    public function update($currentVersion, $newVersion, ?ConnectionInterface $con = null): void
    {
        $updateDirectory = __DIR__.'/Config/update';

        if (!is_dir($updateDirectory)) {
            return;
        }

        $files = Finder::create()
            ->files()
            ->name('*.sql')
            ->depth(0)
            ->sortByName()
            ->in($updateDirectory);

        $database = new Database($con);

        foreach ($files as $file) {
            if (version_compare($currentVersion, $file->getBasename('.sql'), '<')) {
                $database->insertSql(null, [$file->getPathname()]);
            }
        }
    }

    public static function configureServices(ServicesConfigurator $servicesConfigurator): void
    {
        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude([__DIR__.'/I18n/*', __DIR__.'/Model/*', __DIR__.'/Api/Resource/*'])
            ->autowire(true)
            ->autoconfigure(true);
    }
}
