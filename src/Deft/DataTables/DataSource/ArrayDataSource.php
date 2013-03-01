<?php

namespace Deft\DataTables\DataSource;

use Deft\DataTables\Request\Request;

class ArrayDataSource implements DataSourceInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var array
     */
    protected $data;

    /**
     * Filtered and sorted data
     *
     * @var array
     */
    private $displayData;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param \Deft\DataTables\Request\Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return int
     */
    public function getTotalNumberOfRecords()
    {
        return count($this->data);
    }

    /**
     * @return int
     */
    public function getNumberOfFilteredRecords()
    {
        return count($this->getDisplayData());
    }

    /**
     * @return array
     */
    public function getDisplayData()
    {
        if (!$this->displayData) {
            $this->displayData = $this->createDisplayData();
        }

        return array_values($this->displayData);
    }

    protected function createDisplayData()
    {
        $displayData = [];

        foreach ($this->data as $row) {
            $filterResults = [];
            foreach ($this->request->columnFilters as $column => $filter) {
                $filterResults[] = false !== strpos($row[$column], $filter);
            }
            if (in_array(true, $filterResults) || count($this->request->columnFilters) == 0) $displayData[] = $row;
        }

        $sorts = $this->request->columnSorts;
        if (count($sorts) == 0) return array_values($displayData);

        uasort($displayData, function ($a, $b) use ($sorts) {
            foreach ($sorts as $column => $direction) {
                if ($a[$column] == $b[$column]) continue;
                $result = $a[$column] < $b[$column] ? -1 : 1;

                return $direction == 'asc' ? $result : $result * -1;
            }

            return 0;
        });

        return array_values($displayData);
    }
}
