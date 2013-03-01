<?php

namespace Deft\DataTables\Response;

use Deft\DataTables\DataSource\DataSourceInterface;
use Deft\DataTables\Request\Request;

class ResponseFactory
{
    /**
     * @param  \Deft\DataTables\Request\Request                $request
     * @param  \Deft\DataTables\DataSource\DataSourceInterface $dataSource
     * @return Response
     */
    public function createResponse(Request $request, DataSourceInterface $dataSource)
    {
        $response = new Response;
        $response->sEcho = $request->echo;
        $response->iTotalRecords = $dataSource->getTotalNumberOfRecords();
        $response->iTotalDisplayRecords = $dataSource->getNumberOfFilteredRecords($request);
        $response->aaData = $dataSource->getDisplayData($request);

        return $response;
    }
}
