<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Bridge\PersonalAccessGrant;
use League\OAuth2\Server\AuthorizationServer;
use Doododo\OCR\OCR;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws \Exception
     */
    public function boot()
    {
        //自定义laravel passport过期时间
        $this->app->get(AuthorizationServer::class)
            ->enableGrantType(new PersonalAccessGrant(), new \DateInterval('P1W'));

        $this->app->singleton(OCR::class, function () {
            return new OCR(config('ocr'));
        });

        $this->app->alias(OCR::class, 'ocr');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
