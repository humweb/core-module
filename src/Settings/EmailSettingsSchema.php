<?php

namespace Humweb\Core\Settings;

use Humweb\Settings\SettingsSchema;

class EmailSettingsSchema extends SettingsSchema
{
    public function __construct($values = [], $decorator = null)
    {
        parent::__construct($values, $decorator);

        $this->settings = [
            'email.from_address'         => [
                'type'        => 'text',
                'label'       => 'From email',
                'description' => 'Specify address that is used globally for all e-mails.',
            ],
            'email.from_name'         => [
                'type'        => 'text',
                'label'       => 'From Name',
                'description' => 'Specify name that is used globally for all e-mails.',
            ]
        ];
    }
}
