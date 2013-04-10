<?php

namespace Deft\DataTables\Table;

use Deft\DataTables\DataSource\DataSourceInterface;
use Deft\DataTables\DataTransformer\DataTransformerInterface;

interface TableInterface
{
    /**
     * @return DataSourceInterface
     */
    public function getDataSource();

    /**
     * @return DataTransformerInterface[]
     */
    public function getDataTransformers();
}
