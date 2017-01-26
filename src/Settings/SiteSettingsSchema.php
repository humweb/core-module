<?php

namespace Humweb\Settings;

class SiteSettingsSchema extends SettingsSchema
{
    public function __construct($values = [], $decorator = null)
    {
        parent::__construct($values, $decorator);

        $this->settings = [
            'site.status'       => [
                'type'        => 'select',
                'label'       => trans('core::site.status_label'),
                'description' => trans('core::site.status_description'),
                'options'     => [
                    0 => 'Offline',
                    1 => 'Online',
                ],
            ],
            'site.name'         => [
                'type'        => 'text',
                'label'       => trans('core::site.name_label'),
                'description' => trans('core::site.name_description'),
            ],
            'site.slogan'       => [
                'type'        => 'text',
                'label'       => trans('core::site.slogan_label'),
                'description' => trans('core::site.slogan_description'),
            ],
            'site.meta_topic'   => [
                'type'        => 'text',
                'label'       => trans('core::site.meta_topic_label'),
                'description' => trans('core::site.meta_topic_description'),
            ],
            'site.lang'         => [
                'type'        => 'select',
                'label'       => trans('core::site.lang_label'),
                'description' => trans('core::site.lang_description'),
                'options'     => ['en' => 'English'],
            ],
            'site.date_format'  => [
                'type'        => 'text',
                'label'       => trans('core::site.date_format_label'),
                'description' => trans('core::site.date_format_description'),
                'options'     => ['en' => 'English'],
            ],
            'site.record_limit' => [
                'type'        => 'select',
                'label'       => trans('core::site.record_limit_label'),
                'description' => trans('core::site.record_limit_description'),
                'options'     => ['5', '10', '15', '25', '50', '75', '100'],
            ],
        ];
    }
}
