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

namespace Tabs\Loop;

use Propel\Runtime\ActiveQuery\Criteria;
use Tabs\Model\CategoryAssociatedTabQuery;
use Tabs\Model\ContentAssociatedTabQuery;
use Tabs\Model\FolderAssociatedTabQuery;
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
 *
 * Tabs loop
 *
 *
 * Class Tabs
 * @package Tabs\Loop
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class Tabs extends BaseI18nLoop implements PropelSearchLoopInterface
{
	protected $timestampable = true;

	/**
	 * @return ArgumentCollection
	 */
	protected function getArgDefinitions()
	{
		return new ArgumentCollection(
			Argument::createIntListTypeArgument('id'),
			Argument::createAnyTypeArgument('source'),
			Argument::createIntTypeArgument('source_id'),
			Argument::createIntTypeArgument('position'),
			Argument::createBooleanOrBothTypeArgument('visible', 1),
			new Argument(
				'order',
				new TypeCollection(
					new EnumListType(array('alpha', 'alpha-reverse', 'manual', 'manual_reverse', 'random', 'given_id'))
				),
				'alpha'
			)
		);
	}

	/**
	 * @return \Tabs\Model\ContentAssociatedTabQuery|ProductAssociatedTabQuery|CategoryAssociatedTabQuery|FolderAssociatedTabQuery
	 */
	protected function getSearchQuery()
	{


		if (null !== $source = $this->getSource()) {
			$id = $this->getSourceId();

			switch ($source) {
				case 'product':
					$query = ProductAssociatedTabQuery::create();
					if (null !== $id) {
						$query->filterByProductId($id);
					}
					return $query;

				case 'content':
					$query = ContentAssociatedTabQuery::create();
					if (null !== $id) {
						$query->filterByContentId($id);
					}
					return $query;

				case 'category':
					$query = CategoryAssociatedTabQuery::create();
					if (null !== $id) {
						$query->filterByCategoryId($id);
					}
					return $query;

				case 'folder':
					$query = FolderAssociatedTabQuery::create();
					if (null !== $id) {
						$query->filterByFolderId($id);
					}
					return $query;
			}
		}

		throw new \InvalidArgumentException('Please provide a product or content ID, or a valid source');
	}

	public function buildModelCriteria()
	{
		$search = $this->getSearchQuery();

		/* manage translations */
		$this->configureI18nProcessing($search, array('TITLE', 'DESCRIPTION'));

		$id = $this->getId();

		if (!is_null($id)) {
			$search->filterById($id, Criteria::IN);
		}

		$visible = $this->getVisible();

		if ($visible !== BooleanOrBothType::ANY) {
			$search->filterByVisible($visible ? 1 : 0);
		}

		$orders = $this->getOrder();

		foreach ($orders as $order) {
			switch ($order) {
				case "alpha":
					$search->addAscendingOrderByColumn('i18n_TITLE');
					break;
				case "alpha-reverse":
					$search->addDescendingOrderByColumn('i18n_TITLE');
					break;
				case "manual":
					$search->orderByPosition(Criteria::ASC);
					break;
				case "manual_reverse":
					$search->orderByPosition(Criteria::DESC);
					break;
				case "given_id":
					if (null === $id) {
						throw new \InvalidArgumentException('Given_id order cannot be set without `id` argument');
					}

					foreach ($id as $singleId) {
						$givenIdMatched = 'given_id_matched_' . $singleId;
						$search->withColumn(ContentTableMap::ID . "='$singleId'", $givenIdMatched);
						$search->orderBy($givenIdMatched, Criteria::DESC);
					}
					break;
				case "random":
					$search->clearOrderByColumns();
					$search->addAscendingOrderByColumn('RAND()');
					break(2);
			}
		}

		return $search;
	}

	public function parseResults(LoopResult $loopResult)
	{
		foreach ($loopResult->getResultDataCollection() as $tabs) {
			$loopResultRow = new LoopResultRow($tabs);

			$loopResultRow->set("ID", $tabs->getId())
				->set("LOCALE", $this->locale)
				->set("TITLE", $tabs->getVirtualColumn('i18n_TITLE'))
				->set("DESCRIPTION", $tabs->getVirtualColumn('i18n_DESCRIPTION'))
				->set("POSITION", $tabs->getPosition())
				->set("VISIBLE", $tabs->getVisible());

			$loopResult->addRow($loopResultRow);
		}

		return $loopResult;
	}
}