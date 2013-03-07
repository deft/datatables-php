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

    /**
     * @var DataTransformer\DataTransformerInterface[]
     */
    protected $dataTransformers;

    public static function create(DataSourceInterface $dataSource, array $dataTransformers = [])
    {
        return new self(
            new RequestParser(),
            new ResponseFactory(),
            $dataSource,
            $dataTransformers
        );
    }

    public function __construct(
        RequestParserInterface $requestParser,
        ResponseFactoryInterface $responseFactory,
        DataSourceInterface $dataSource,
        array $dataTransformers = []
    ) {
        $this->requestParser = $requestParser;
        $this->responseFactory = $responseFactory;
        $this->dataSource = $dataSource;
        $this->dataTransformers = $dataTransformers;
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

        foreach ($dataSet->data as &$row) {
            $this->applyDataTransformers($row);
        }

        return $this->responseFactory
            ->createResponse($dtRequest, $dataSet)
            ->createHttpResponse()
        ;
    }

    /**
     * @param  array $row
     * @return array
     */
    protected function applyDataTransformers(array &$row)
    {
        foreach ($this->dataTransformers as $column => $dataTransformer) {
            $row[$column] = $dataTransformer->transform($row[$column]);
        }

        return $row;
    }
}
