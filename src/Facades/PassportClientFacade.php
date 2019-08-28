<?php


namespace EdwinFadilah\PassportClient\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \EdwinFadilah\PassportClient\PassportClient
 */

class PassportClientFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'passport.client';
    }
}
