<?php

namespace AlexBuckham\CloudflareImagesLaravel\Facades;

use Illuminate\Support\Facades\Facade;

class CloudflareImagesFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \AlexBuckham\CloudflareImagesLaravel\CloudflareImages::class;
    }
}
