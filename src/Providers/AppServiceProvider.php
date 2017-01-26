<?php

namespace Humweb\Core\Providers;

use Humweb\Modules\ModuleServiceProvider;
use Humweb\Core\Settings\EmailSettingsSchema;
use Humweb\Core\Settings\SiteSettingsSchema;

class AppServiceProvider extends ModuleServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register module
        $this->app['modules']->put('core', $this);
        $this->app['settings.schema.manager']->register('site', SiteSettingsSchema::class)->register('email', EmailSettingsSchema::class);

        $settings = $this->app['settings']->getSection('email');
        $from     = $this->app['config']['mail.from'];

        $this->app['mailer']->alwaysFrom(
            $settings->get('email.from_address', $from['address']),
            $settings->get('email.from_name', $from['name'])
        );
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


    public function getAdminMenu()
    {
        return [
            'Settings' => [
                [
                    'label' => 'Site',
                    'url'   => '/admin/settings/site',
                    'icon'  => '<i class="fa fa-home" ></i>',
                ],
                [
                    'label' => 'Mail',
                    'url'   => '/admin/settings/email',
                    'icon'  => '<i class="fa fa-envelope" ></i>',
                ],
            ],
        ];
    }
}
