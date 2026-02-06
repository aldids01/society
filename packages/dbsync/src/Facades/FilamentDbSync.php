<?php

namespace Aldids\FilamentDbSync\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Aldids\FilamentDbSync\FilamentDbSync
 */
class FilamentDbSync extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Aldids\FilamentDbSync\FilamentDbSync::class;
    }
}
