<?php

namespace Deft\DataTables\Response;

use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Response
{
    /**
     * Used by DataTables to identify the request/response pair
     *
     * @var string
     */
    public $sEcho;

    /**
     * The total of number of records in the data set
     *
     * @var int
     */
    public $iTotalRecords;

    /**
     * The total number of records in the data set, after filtering
     *
     * @var int
     */
    public $iTotalDisplayRecords;

    /**
     * The actual data
     *
     * @var array
     */
    public $aaData;

    public function createHttpResponse()
    {
        $httpResponse = new HttpResponse();
        $httpResponse->setContent(json_encode($this));
        $httpResponse->headers->set('Content-Type', 'application/json');

        return $httpResponse;
    }
}
