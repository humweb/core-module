<?php

namespace Humweb\Tests\Core\Support\Traits;

use Humweb\Tests\Core\Fake\Page;
use Humweb\Tests\Core\TestCase;

/**
 * SluggableTest
 *
 * @package Traits\Sluggable
 */
class SluggableTraitTest extends TestCase
{

    protected $runMigrations = true;


    /**
     * @test
     */
    public function it_will_save_a_slug_when_saving_a_model()
    {
        $model = $this->createPageEntity('this is a test');

        $this->assertEquals('this-is-a-test', $model->slug);
    }


    /**
     * @test
     */
    public function it_will_not_change_the_slug_when_the_source_field_is_not_changed()
    {
        $model = $this->createPageEntity('this is a test');

        $model->content = 'otherValue';
        $model->save();

        $this->assertEquals('this-is-a-test', $model->slug);
    }


    /**
     * @test
     */
    public function it_will_update_the_slug_when_the_source_field_is_changed()
    {
        $model = $this->createPageEntity('this is a test');

        $model->fill($this->createEntityArray('this is another test'));
        $model->save();

        $this->assertEquals('this-is-another-test', $model->slug);
    }


    /**
     * @test
     */
    public function it_will_save_a_unique_slug_by_default()
    {
        $this->createPageEntity('this is a test');

        foreach (range(1, 10) as $i) {
            $model = $this->createPageEntity('this is a test');
            $this->assertEquals("this-is-a-test-".$i, $model->slug);
        }
    }


    /**
     * @test
     */
    public function it_can_handle_empty_source_fields()
    {
        foreach (range(1, 10) as $i) {
            $model = $this->createPageEntity('');
            $this->assertEquals("-{$i}", $model->slug);
        }
    }


    /**
     * @test
     */
    public function it_can_generate_duplicate_slugs()
    {
        foreach (range(1, 10) as $i) {
            $model = new Page;
            $model->setSlugOptions('unique', false);

            $model->fill($this->createEntityArray('this is a test'));
            $model->save();

            $this->assertEquals('this-is-a-test', $model->slug);
        }
    }


    /**
     * @test
     */
    public function it_can_generate_slugs_with_a_maximum_length()
    {
        $model = new Page;
        $model->setSlugOptions('maxlen', 5);
        $model->fill($this->createEntityArray('123456789'));
        $model->save();
        $this->assertEquals('12345', $model->slug);
    }


    /**
     * @test
     * @dataProvider weirdCharacterProvider
     */
    public function it_can_handle_weird_characters_when_generating_the_slug($weirdCharacter, $normalCharacter)
    {
        $model = $this->createPageEntity($weirdCharacter);

        $this->assertEquals($normalCharacter, $model->slug);
    }


    public function weirdCharacterProvider()
    {
        return [
            ['é', 'e'],
            ['è', 'e'],
            ['à', 'a'],
            ['a€', 'a'],
            ['ß', 'ss'],
            ['a/ ', 'a'],
        ];
    }


    /**
     * @test
     */
    public function it_can_handle_overwrites_when_updating_a_model()
    {
        $model = $this->createPageEntity('this is a test');

        $model->slug = 'this-is-an-url';
        $model->save();

        $this->assertEquals('this-is-an-url', $model->slug);
    }


    /**
     * @test
     */
    public function it_can_handle_duplicates_when_overwriting_a_slug()
    {
        $model = $this->createPageEntity('this is a test');
        $this->createPageEntity('this is an other');

        $model->slug = 'this-is-an-other';
        $model->save();

        $this->assertEquals('this-is-an-other-1', $model->slug);
    }


    protected function createPageEntity($name)
    {
        return Page::create([
            'uri'     => $name,
            'title'   => $name,
            'content' => '',
            'created_by' => 1,
            'published'  => true,
            'order'   => 0,
        ]);
    }


    protected function createEntityArray($name)
    {
        return [
            'title'   => $name,
            'uri'     => $name,
            'content' => '',
            'created_by' => 1,
            'published'  => true,
            'order'   => 0,
        ];
    }
}
