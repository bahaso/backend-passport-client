<?php
/**
 * Created by PhpStorm.
 * User: bahasolaptop2
 * Date: 31/07/19
 * Time: 15:42
 */

namespace EdwinFadilah\PassportClient\Requests\Contracts;


interface PassportRequest
{
    public function getGrantType();
}