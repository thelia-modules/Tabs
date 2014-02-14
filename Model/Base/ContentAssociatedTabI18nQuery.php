<?php

namespace Tabs\Model\Base;

use \Exception;
use \PDO;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Tabs\Model\ContentAssociatedTabI18n as ChildContentAssociatedTabI18n;
use Tabs\Model\ContentAssociatedTabI18nQuery as ChildContentAssociatedTabI18nQuery;
use Tabs\Model\Map\ContentAssociatedTabI18nTableMap;

/**
 * Base class that represents a query for the 'content_associated_tab_i18n' table.
 *
 *
 *
 * @method     ChildContentAssociatedTabI18nQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildContentAssociatedTabI18nQuery orderByLocale($order = Criteria::ASC) Order by the locale column
 * @method     ChildContentAssociatedTabI18nQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method     ChildContentAssociatedTabI18nQuery orderByDescription($order = Criteria::ASC) Order by the description column
 *
 * @method     ChildContentAssociatedTabI18nQuery groupById() Group by the id column
 * @method     ChildContentAssociatedTabI18nQuery groupByLocale() Group by the locale column
 * @method     ChildContentAssociatedTabI18nQuery groupByTitle() Group by the title column
 * @method     ChildContentAssociatedTabI18nQuery groupByDescription() Group by the description column
 *
 * @method     ChildContentAssociatedTabI18nQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildContentAssociatedTabI18nQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildContentAssociatedTabI18nQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildContentAssociatedTabI18nQuery leftJoinContentAssociatedTab($relationAlias = null) Adds a LEFT JOIN clause to the query using the ContentAssociatedTab relation
 * @method     ChildContentAssociatedTabI18nQuery rightJoinContentAssociatedTab($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ContentAssociatedTab relation
 * @method     ChildContentAssociatedTabI18nQuery innerJoinContentAssociatedTab($relationAlias = null) Adds a INNER JOIN clause to the query using the ContentAssociatedTab relation
 *
 * @method     ChildContentAssociatedTabI18n findOne(ConnectionInterface $con = null) Return the first ChildContentAssociatedTabI18n matching the query
 * @method     ChildContentAssociatedTabI18n findOneOrCreate(ConnectionInterface $con = null) Return the first ChildContentAssociatedTabI18n matching the query, or a new ChildContentAssociatedTabI18n object populated from the query conditions when no match is found
 *
 * @method     ChildContentAssociatedTabI18n findOneById(int $id) Return the first ChildContentAssociatedTabI18n filtered by the id column
 * @method     ChildContentAssociatedTabI18n findOneByLocale(string $locale) Return the first ChildContentAssociatedTabI18n filtered by the locale column
 * @method     ChildContentAssociatedTabI18n findOneByTitle(string $title) Return the first ChildContentAssociatedTabI18n filtered by the title column
 * @method     ChildContentAssociatedTabI18n findOneByDescription(string $description) Return the first ChildContentAssociatedTabI18n filtered by the description column
 *
 * @method     array findById(int $id) Return ChildContentAssociatedTabI18n objects filtered by the id column
 * @method     array findByLocale(string $locale) Return ChildContentAssociatedTabI18n objects filtered by the locale column
 * @method     array findByTitle(string $title) Return ChildContentAssociatedTabI18n objects filtered by the title column
 * @method     array findByDescription(string $description) Return ChildContentAssociatedTabI18n objects filtered by the description column
 *
 */
abstract class ContentAssociatedTabI18nQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Tabs\Model\Base\ContentAssociatedTabI18nQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\Tabs\\Model\\ContentAssociatedTabI18n', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildContentAssociatedTabI18nQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildContentAssociatedTabI18nQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Tabs\Model\ContentAssociatedTabI18nQuery) {
            return $criteria;
        }
        $query = new \Tabs\Model\ContentAssociatedTabI18nQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array[$id, $locale] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildContentAssociatedTabI18n|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ContentAssociatedTabI18nTableMap::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ContentAssociatedTabI18nTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return   ChildContentAssociatedTabI18n A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, LOCALE, TITLE, DESCRIPTION FROM content_associated_tab_i18n WHERE ID = :p0 AND LOCALE = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildContentAssociatedTabI18n();
            $obj->hydrate($row);
            ContentAssociatedTabI18nTableMap::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildContentAssociatedTabI18n|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ChildContentAssociatedTabI18nQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(ContentAssociatedTabI18nTableMap::ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(ContentAssociatedTabI18nTableMap::LOCALE, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildContentAssociatedTabI18nQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(ContentAssociatedTabI18nTableMap::ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(ContentAssociatedTabI18nTableMap::LOCALE, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @see       filterByContentAssociatedTab()
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildContentAssociatedTabI18nQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ContentAssociatedTabI18nTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ContentAssociatedTabI18nTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ContentAssociatedTabI18nTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the locale column
     *
     * Example usage:
     * <code>
     * $query->filterByLocale('fooValue');   // WHERE locale = 'fooValue'
     * $query->filterByLocale('%fooValue%'); // WHERE locale LIKE '%fooValue%'
     * </code>
     *
     * @param     string $locale The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildContentAssociatedTabI18nQuery The current query, for fluid interface
     */
    public function filterByLocale($locale = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($locale)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $locale)) {
                $locale = str_replace('*', '%', $locale);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ContentAssociatedTabI18nTableMap::LOCALE, $locale, $comparison);
    }

    /**
     * Filter the query on the title column
     *
     * Example usage:
     * <code>
     * $query->filterByTitle('fooValue');   // WHERE title = 'fooValue'
     * $query->filterByTitle('%fooValue%'); // WHERE title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $title The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildContentAssociatedTabI18nQuery The current query, for fluid interface
     */
    public function filterByTitle($title = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($title)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $title)) {
                $title = str_replace('*', '%', $title);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ContentAssociatedTabI18nTableMap::TITLE, $title, $comparison);
    }

    /**
     * Filter the query on the description column
     *
     * Example usage:
     * <code>
     * $query->filterByDescription('fooValue');   // WHERE description = 'fooValue'
     * $query->filterByDescription('%fooValue%'); // WHERE description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $description The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildContentAssociatedTabI18nQuery The current query, for fluid interface
     */
    public function filterByDescription($description = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($description)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $description)) {
                $description = str_replace('*', '%', $description);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ContentAssociatedTabI18nTableMap::DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query by a related \Tabs\Model\ContentAssociatedTab object
     *
     * @param \Tabs\Model\ContentAssociatedTab|ObjectCollection $contentAssociatedTab The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildContentAssociatedTabI18nQuery The current query, for fluid interface
     */
    public function filterByContentAssociatedTab($contentAssociatedTab, $comparison = null)
    {
        if ($contentAssociatedTab instanceof \Tabs\Model\ContentAssociatedTab) {
            return $this
                ->addUsingAlias(ContentAssociatedTabI18nTableMap::ID, $contentAssociatedTab->getId(), $comparison);
        } elseif ($contentAssociatedTab instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ContentAssociatedTabI18nTableMap::ID, $contentAssociatedTab->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByContentAssociatedTab() only accepts arguments of type \Tabs\Model\ContentAssociatedTab or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ContentAssociatedTab relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildContentAssociatedTabI18nQuery The current query, for fluid interface
     */
    public function joinContentAssociatedTab($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ContentAssociatedTab');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'ContentAssociatedTab');
        }

        return $this;
    }

    /**
     * Use the ContentAssociatedTab relation ContentAssociatedTab object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Tabs\Model\ContentAssociatedTabQuery A secondary query class using the current class as primary query
     */
    public function useContentAssociatedTabQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinContentAssociatedTab($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ContentAssociatedTab', '\Tabs\Model\ContentAssociatedTabQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildContentAssociatedTabI18n $contentAssociatedTabI18n Object to remove from the list of results
     *
     * @return ChildContentAssociatedTabI18nQuery The current query, for fluid interface
     */
    public function prune($contentAssociatedTabI18n = null)
    {
        if ($contentAssociatedTabI18n) {
            $this->addCond('pruneCond0', $this->getAliasedColName(ContentAssociatedTabI18nTableMap::ID), $contentAssociatedTabI18n->getId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(ContentAssociatedTabI18nTableMap::LOCALE), $contentAssociatedTabI18n->getLocale(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the content_associated_tab_i18n table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ContentAssociatedTabI18nTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            ContentAssociatedTabI18nTableMap::clearInstancePool();
            ContentAssociatedTabI18nTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildContentAssociatedTabI18n or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildContentAssociatedTabI18n object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ContentAssociatedTabI18nTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ContentAssociatedTabI18nTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        ContentAssociatedTabI18nTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            ContentAssociatedTabI18nTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // ContentAssociatedTabI18nQuery
