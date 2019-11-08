<?php
namespace App\Modules\Users\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class UserServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["admin","site"];
    
    /**
     * {@inheritDoc}
     */    
    protected $namespace = 'App/Modules/Users/';
}
