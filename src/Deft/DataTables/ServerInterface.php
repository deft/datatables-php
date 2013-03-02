<?php

namespace Deft\DataTables;

use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
interface ServerInterface
{
    /**
     * Handles a DataTables server-side processing request.
     *
     * @param  HttpRequest  $httpRequest
     * @return HttpResponse
     */
    public function handleRequest(HttpRequest $httpRequest);
}
