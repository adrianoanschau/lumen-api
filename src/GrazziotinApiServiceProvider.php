<?php

namespace Grazziotin\GrazziotinApi;

use BenSampo\Enum\EnumServiceProvider;
use Fruitcake\Cors\CorsServiceProvider;
use geekcom\ValidatorDocs\ValidatorProvider;
use Illuminate\Support\ServiceProvider;
use Knuckles\Scribe\ScribeServiceProvider;
use Yajra\Oci8\Oci8ServiceProvider;

class GrazziotinApiServiceProvider extends ServiceProvider {

    public function boot()
    {
        $this->app->register(CorsServiceProvider::class);
        $this->app->register(Oci8ServiceProvider::class);
        $this->app->register(EnumServiceProvider::class);
        $this->app->register(ScribeServiceProvider::class);
        $this->app->register(ValidatorProvider::class);
    }

    public function register()
    {
        //
    }

}
