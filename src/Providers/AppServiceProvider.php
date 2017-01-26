<?php

namespace Humweb\Core\Providers;

use Humweb\Modules\ModuleServiceProvider;
use Humweb\Settings\EmailSettingsSchema;
use Humweb\Settings\SiteSettingsSchema;

class AppServiceProvider extends ModuleServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['settings.schema.manager']->register('site', SiteSettingsSchema::class)->register('mail', EmailSettingsSchema::class);

        $settings = $this->app['settings']->getSection('email');
        $from     = $this->app['config']['mail.from'];

        $this->app['mailer']->alwaysFrom(
            $settings->get(['from_address'], $from['address']),
            $settings->get(['from_name'], $from['name'])
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
