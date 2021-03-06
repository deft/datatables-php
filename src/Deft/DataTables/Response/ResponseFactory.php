<?php

namespace Deft\DataTables\Response;

use Deft\DataTables\DataSource\DataSet;
use Deft\DataTables\Request\Request;

class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * @param  Request  $request
     * @param  DataSet  $dataSet
     * @return Response
     */
    public function createResponse(Request $request, DataSet $dataSet)
    {
        $response = new Response;
        $response->sEcho = $request->echo;
        $response->iTotalRecords = $dataSet->numberOfTotalRecords;
        $response->iTotalDisplayRecords = $dataSet->numberOfFilteredRecords;
        $response->aaData = $dataSet->data;

        return $response;
    }
}
