<?php

namespace Cms\Modules\Bread;

use Spark\Database\Model;

class Form
{
    protected array $fields = [];
    protected Model $model;

    public function __construct(Model $model, array $fields = [])
    {
        $this->model = $model;
        $this->fields = $fields;
    }

    public static function make(Model $model, array $fields = []): static
    {
        return new static($model, $fields);
    }

    public function render()
    {
        return view('cms::bread.form');
    }
}