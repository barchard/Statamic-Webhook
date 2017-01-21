<?php

namespace Statamic\Addons\Webhook;

use Statamic\API\Config;
use Statamic\Extend\ServiceProvider;

class WebhookServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    public $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $excludes = Config::get('system.csrf_exclude', []);

        /**
         * TODO: change this to `$this->actionUrl('facebook')` when
         * https://github.com/statamic/v2-hub/issues/997 is fixed
         * @link https://lodge.statamic.com/questions/2596-webhooks-for-external-app
         */
        Config::set('system.csrf_exclude',
            array_merge(['!/Webhook/*', '!/webhook/*'], $excludes));
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
