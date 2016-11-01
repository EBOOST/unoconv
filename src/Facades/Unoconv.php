<?php
/**
 * Created by Eboost Interactive BV.
 * User: Bert van Hoekelen
 * Date: 15/03/16
 */

namespace Eboost\Unoconv\Facades;

class Unoconv extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'unoconv';
    }
}
