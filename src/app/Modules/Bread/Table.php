<?php

namespace Cms\Modules\Bread;

use Spark\Database\Model;
use Spark\Http\Response;

class Table
{
    protected array $columns = [];
    protected array $filters = [];
    protected array $actions = [];

    public function __construct(
        protected Model $model
    ) {
    }

    public static function make(Model $model): static
    {
        return new static($model);
    }

    public function render(): Response
    {
        return view('cms::bread.table');
    }
}