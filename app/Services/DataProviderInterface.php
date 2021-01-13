<?php

namespace Services;

interface DataProviderInterface
{
    public function queryData(?int $forceUpdate);
}