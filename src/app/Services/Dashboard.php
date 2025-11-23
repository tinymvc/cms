<?php

namespace Cms\Services;

use Spark\Support\Collection;

class Dashboard
{
    public Collection $menu;
    public array $config;

    public function __construct(array $menu = [], array $config = [])
    {
        $this->config = $config;
        $this->menu = new Collection($menu);
    }
}