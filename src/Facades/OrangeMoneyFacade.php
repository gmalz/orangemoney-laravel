<?php

namespace OrangeMoney\Facades;

use Illuminate\Support\Facades\Facade;

class OrangeMoneyFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'OrangeMoney';
    }
}