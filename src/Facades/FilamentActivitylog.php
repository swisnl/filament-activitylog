<?php

namespace Swis\FilamentActivitylog\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Swis\FilamentActivitylog\FilamentActivitylog
 */
class FilamentActivitylog extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Swis\FilamentActivitylog\FilamentActivitylog::class;
    }
}
