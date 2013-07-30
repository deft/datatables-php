<?php

namespace Deft\DataTables\DataSource\ORM;

use Deft\DataTables\DataSource\DataSet;
use Deft\DataTables\DataSource\DataSourceInterface;
use Deft\DataTables\Request\Request;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class QueryBuilderDataSource implements DataSourceInterface
{
    /**
     * @var QueryBuilder
     */
    protected $qb;

    /**
     * Maps datatables columns to fields in the DQL query
     *
     * @var array
     */
    protected $columnMapping;

    /**
     * Maps datatables columns to property paths in the object graph, relative to the selected root.
     *
     * @var array
     */
    protected $propertyPathMapping;

    public function __construct(QueryBuilder $qb, array $columnMapping, array $propertyPathMapping = [])
    {
        $this->qb = $qb;
        $this->columnMapping = $columnMapping;
        $this->propertyPathMapping = $propertyPathMapping;
    }

    /**
     * @param  Request $request
     * @return DataSet
     */
    public function createDataSet(Request $request)
    {
        $dataSet = new DataSet();

        $paginatorTotal = $this->createPaginator();
        $dataSet->numberOfTotalRecords = $paginatorTotal->count();

        // Add filters
        $where = [];
        foreach ($request->columnFilters as $column => $filter) {
            $fieldName = $this->columnMapping[$column];

            /**
             * @todo refactor handling of composite/aggregate fields
             */
            if (count(explode(".", $fieldName)) == 1) {
                $fieldName = $this->retrieveExpressionByFieldName($fieldName);
            }

            // Treat '|' as OR
            if (false !== strpos($filter, '|')) {
                $possibleValues = explode('|', $filter);
                $orX = [];
                foreach ($possibleValues as $value)
                {
                    $paramName = ':datatables_' . $this->qb->getParameters()->count();
                    $this->qb->setParameter($paramName, $value);
                    $orX[] = $this->qb->expr()->eq($fieldName, $paramName);
                }

                $where[] = call_user_func_array([$this->qb->expr(), 'orX'], $orX);
            } else {
                $paramName = ':datatables_' . $this->qb->getParameters()->count();
                $this->qb->setParameter($paramName, "%$filter%");
                $where[] = $this->qb->expr()->like($fieldName, $paramName);
            }
        }

        if (count($where) > 0) {
            $this->qb->andWhere(call_user_func_array([$this->qb->expr(), 'andX'], $where));
        }

        // Add sorting
        foreach ($request->columnSorts as $column => $direction) {
            $this->qb->addOrderBy($this->columnMapping[$column], $direction);
        }

        $this->qb->setFirstResult($request->displayStart);
        $this->qb->setMaxResults($request->displayLength);

        $paginatorFiltered = $this->createPaginator();
        $dataSet->numberOfFilteredRecords = $paginatorFiltered->count();
        $dataSet->data = [];

        $propertyPathMappingFactory = new PropertyPathMappingFactory();
        $propertyPathMapping = $propertyPathMappingFactory->createPropertyPathMapping($this->qb, $this->columnMapping, $this->propertyPathMapping);

        foreach ($paginatorFiltered->getIterator() as $item) {
            $dataSet->data[] = $this->buildRow($item, $propertyPathMapping);
        }

        return $dataSet;
    }

    protected function buildRow($item, $propertyPathMapping)
    {
        $row = [];
        foreach ($this->columnMapping as $column => $field) {
            try {
                $row[$column] = PropertyAccess::getPropertyAccessor()->getValue($item, $propertyPathMapping[$column]);
            } catch (UnexpectedTypeException $e) {
                $row[$column] = null;
            }
        }

        return $row;
    }

    public function createPaginator()
    {
        return new Paginator($this->qb);
    }

    private function retrieveExpressionByFieldName($fieldName)
    {
        /** @var $selectPart \Doctrine\ORM\Query\Expr\Select */
        foreach ($this->qb->getDqlPart('select') as $selectPart)
        {
            foreach ($selectPart->getParts() as $part)
            {
                $pattern = "/^(.+) AS (?:|HIDDEN ){$fieldName}$/i";
                if (preg_match($pattern, $part, $matches)) {
                    return $matches[1];
                }
            }
        }

        throw new \InvalidArgumentException("Expression for field '$fieldName' could not be determined.");
    }
}
