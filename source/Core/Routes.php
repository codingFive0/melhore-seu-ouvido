<?php

namespace Source\Core;

use CoffeeCode\Router\Router;

class Routes extends Router
{
    public function __construct(string $projectUrl, ?string $separator = ":")
    {
        parent::__construct($projectUrl, $separator);
    }

    public function getRouteName()
    {
        return $this->route["name"];
    }
}