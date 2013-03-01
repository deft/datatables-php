<?php

namespace Deft\DataTables\Response;

class Response
{
    /**
     * Used by DataTables to identify the request/response pair
     *
     * @var string
     */
    public $echo;

    /**
     * The total of number of records in the data set
     *
     * @var int
     */
    public $totalNumberOfRecords;

    /**
     * The total number of records in the data set, after filtering
     *
     * @var int
     */
    public $numberOfFilteredRecords;

    /**
     * The actual data
     *
     * @var array
     */
    public $data;
}
