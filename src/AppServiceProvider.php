<?php

namespace Humweb\Core;

use Humweb\Core\Settings\EmailSettingsSchema;
use Humweb\Core\Settings\SiteSettingsSchema;
use Humweb\Modules\ModuleServiceProvider;

class AppServiceProvider extends ModuleServiceProvider
{

    protected $moduleMeta = [
        'name'    => 'Core module',
        'slug'    => 'core',
        'version' => '',
        'author'  => '',
        'email'   => '',
        'website' => '',
    ];


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrations();
        // Load resources
        $this->loadLang();

        // Register module
        $this->app['modules']->put('core', $this);
        $this->app['settings.schema.manager']->register('site', SiteSettingsSchema::class)->register('email', EmailSettingsSchema::class);

        try {
            $settings = $this->app['settings']->getSection('email');
            $from     = $this->app['config']['mail.from'];

            $this->app['mailer']->alwaysFrom($settings->get('email.from_address', $from['address']), $settings->get('email.from_name', $from['name']));
        } catch (\Exception $e) {
            ! $this->app->runningInConsole() && die("Could not connect to the database.  Please check your configuration.");
        }
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
