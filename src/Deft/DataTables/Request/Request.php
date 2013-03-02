<?php

namespace Deft\DataTables\Request;

class Request
{
    /**
     * Used by DataTables to identify the request/response pair
     *
     * @var string
     */
    public $echo;

    /**
     * Number of items displayed (max) per page
     *
     * @var int
     */
    public $displayLength;

    /**
     * Zero-indexed starting point in data set
     *
     * @var int
     */
    public $displayStart;

    /**
     * Term to search globally for
     *
     * @var string
     */
    public $globalSearch;

    /**
     * List of column-specific filters, key being the column name
     *
     * @var string
     */
    public $columnFilters = [];

    /**
     * Ordered list of key (column) / value (direction) pairs
     *
     * @var string
     */
    public $columnSorts = [];
}
