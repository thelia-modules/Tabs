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

namespace Tabs\Loop;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Tabs\Model\Base\ContentAssociatedTabQuery;
use Tabs\Model\ProductAssociatedTabQuery;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Model\Map\ContentTableMap;
use Thelia\Type\BooleanOrBothType;
use Thelia\Type\EnumListType;
use Thelia\Type\TypeCollection;

/**
 * Tabs loop.
 *
 * Class Tabs
 *
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class Tabs extends BaseI18nLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;

    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions(): ArgumentCollection
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createIntListTypeArgument('content'),
            Argument::createIntListTypeArgument('product'),
            Argument::createBooleanOrBothTypeArgument('visible', 1),
            new Argument(
                'order',
                new TypeCollection(
                    new EnumListType(['alpha', 'alpha-reverse', 'manual', 'manual_reverse', 'random', 'given_id'])
                ),
                'alpha'
            )
        );
    }

    public function buildModelCriteria(): ModelCriteria
    {
        $search = null;

        if ($this->getContent()) {
            $search = ContentAssociatedTabQuery::create();

            $search->filterByContentId($this->getContent());
        } elseif ($this->getProduct()) {
            $search = ProductAssociatedTabQuery::create();

            $search->filterByProductId($this->getProduct());
        } else {
            throw new \InvalidArgumentException('Please provide a product or content ID');
        }

        /* manage translations */
        $this->configureI18nProcessing($search, ['TITLE', 'DESCRIPTION']);

        $id = $this->getId();

        if (null !== $id) {
            $search->filterById($id, Criteria::IN);
        }

        $visible = $this->getVisible();

        if ($visible !== BooleanOrBothType::ANY) {
            $search->filterByVisible($visible ? 1 : 0);
        }

        $orders = $this->getOrder();

        foreach ($orders as $order) {
            switch ($order) {
                case 'alpha':
                    $search->addAscendingOrderByColumn('i18n_TITLE');
                    break;
                case 'alpha-reverse':
                    $search->addDescendingOrderByColumn('i18n_TITLE');
                    break;
                case 'manual':
                    $search->orderByPosition(Criteria::ASC);
                    break;
                case 'manual_reverse':
                    $search->orderByPosition(Criteria::DESC);
                    break;
                case 'given_id':
                    if (null === $id) {
                        throw new \InvalidArgumentException('Given_id order cannot be set without `id` argument');
                    }

                    foreach ($id as $singleId) {
                        $givenIdMatched = 'given_id_matched_'.$singleId;
                        $search->withColumn(ContentTableMap::ID."='$singleId'", $givenIdMatched);
                        $search->orderBy($givenIdMatched, Criteria::DESC);
                    }
                    break;
                case 'random':
                    $search->clearOrderByColumns();
                    $search->addAscendingOrderByColumn('RAND()');
                    break 2;
            }
        }

        return $search;
    }

    public function parseResults(LoopResult $loopResult): LoopResult
    {
        foreach ($loopResult->getResultDataCollection() as $tabs) {
            $loopResultRow = new LoopResultRow($tabs);

            $loopResultRow->set('ID', $tabs->getId())
                ->set('LOCALE', $this->locale)
                ->set('TITLE', $tabs->getVirtualColumn('i18n_TITLE'))
                ->set('DESCRIPTION', $tabs->getVirtualColumn('i18n_DESCRIPTION'))
                ->set('POSITION', $tabs->getPosition())
                ->set('VISIBLE', $tabs->getVisible());

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}
