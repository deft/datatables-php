<?php

namespace Deft\DataTables\DataTransformer;

class CallbackDataTransformer implements DataTransformerInterface
{
    /**
     * The callback used to transform the source value
     *
     * @var callable
     */
    protected $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Transforms a value from the data source representation to the table representation
     *
     * @param  mixed
     * @return mixed
     */
    public function transform($value)
    {
        $callback = $this->callback;
        return $callback($value);
    }
}
