<?php

namespace Deft\DataTables;

use Deft\DataTables\DataSource\DataSourceInterface;
use Deft\DataTables\Request\RequestParser;
use Deft\DataTables\Request\RequestParserInterface;
use Deft\DataTables\Response\ResponseFactory;
use Deft\DataTables\Response\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Server implements ServerInterface
{
    /**
     * @var Request\RequestParserInterface
     */
    protected $requestParser;

    /**
     * @var Response\ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * @var DataSource\DataSourceInterface
     */
    protected $dataSource;

    public static function create(DataSourceInterface $dataSource)
    {
        return new self(
            new RequestParser(),
            new ResponseFactory(),
            $dataSource
        );
    }

    public function __construct(
        RequestParserInterface $requestParser,
        ResponseFactoryInterface $responseFactory,
        DataSourceInterface $dataSource
    ) {
        $this->requestParser = $requestParser;
        $this->responseFactory = $responseFactory;
        $this->dataSource = $dataSource;
    }

    /**
     * Handles a DataTables server-side processing request.
     *
     * @param  HttpRequest  $httpRequest
     * @return HttpResponse
     */
    public function handleRequest(HttpRequest $httpRequest)
    {
        $dtRequest = $this->requestParser->parseRequest($httpRequest);
        $dataSet = $this->dataSource->createDataSet($dtRequest);

        return $this->responseFactory
            ->createResponse($dtRequest, $dataSet)
            ->createHttpResponse()
        ;
    }
}
