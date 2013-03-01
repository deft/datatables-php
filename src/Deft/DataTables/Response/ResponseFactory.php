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
        $response->echo = $request->echo;
        $response->totalNumberOfRecords = $dataSource->getTotalNumberOfRecords();
        $response->numberOfFilteredRecords = $dataSource->getNumberOfFilteredRecords($request);
        $response->data = $dataSource->getData($request);

        return $response;
    }
}
