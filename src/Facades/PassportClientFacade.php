<?php


namespace Bahaso\PassportClient\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Bahaso\PassportClient\PassportClient
 */

class PassportClientFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'passport.client';
    }
}
