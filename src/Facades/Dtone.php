<?php

namespace Ghanem\Dtone\Facades;

use Illuminate\Support\Facades\Facade;

class Dtone extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ghanem-dtone';
    }
}