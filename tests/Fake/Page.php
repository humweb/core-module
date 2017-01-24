<?php

namespace Humweb\Tests\Core\Fake;

use Humweb\Core\Data\Traits\HasRelatedContent;
use Humweb\Core\Data\Traits\SluggableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Page
 *
 * @package Humweb\Tests\Core\Fake
 */
class Page extends Model
{
    use SluggableTrait, HasRelatedContent;

    protected $table = 'pages';

    protected $guarded = [];

    protected $versionsEnabled = true;


    public function __construct(array $attributes = [])
    {

        parent::__construct($attributes);

        $this->slugOptions = [
            'maxlen'     => 200,
            'unique'     => true,
            'slug_field' => 'slug',
            'from_field' => 'title',
        ];
    }
}