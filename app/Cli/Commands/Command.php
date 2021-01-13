<?php

namespace Cli\Commands;

abstract class Command
{
    /** @var array */
    private $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    abstract public function execute();

    protected function getParam(string $paramName): ?string
    {
        return $this->params[$paramName] ?? null;
    }
}