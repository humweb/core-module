<?php

namespace Humweb\Tests\Core\Support\Traits;

use Humweb\Core\Data\Relatable;
use Humweb\Tests\Core\Fake\Page;

use Humweb\Tests\Core\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Collection;

class HasRelatedContentTest extends TestCase
{
    protected $runMigrations = true;


    /** @test */
    function it_can_add_a_related_model_via_a_model_instance()
    {
        $page  = Page::find(1);
        $page2 = Page::find(2);

        $page->relate($page2);
        $this->assertModelIsRelatedToSource($page2, $page);
    }


    /** @test */
    function it_can_add_a_related_model_via_an_id_and_a_type()
    {
        $page  = Page::find(1);
        $page2 = Page::find(2);

        $page->relate(2, Page::class);
        $this->assertModelIsRelatedToSource($page2, $page);
    }


    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     */
    function it_cant_add_a_related_model_via_id_if_no_type_is_provided()
    {
        Page::find(1)->relate(2);
    }


    /** @test */
    function it_can_remove_a_related_model_via_a_model_instance()
    {
        $page  = Page::find(1);
        $page2 = Page::find(2);

        $page->relate($page2);
        $this->assertModelIsRelatedToSource($page2, $page);

        $page->unrelate($page2);
        $this->assertModelIsntRelatedToSource($page2, $page);
    }


    /** @test */
    function it_can_remove_a_related_model_via_an_id_and_a_type()
    {
        $page  = Page::find(1);
        $page2 = Page::find(2);

        $page->relate(2, Page::class);
        $this->assertModelIsRelatedToSource($page2, $page);

        $page->unrelate(2, Page::class);
        $this->assertModelIsntRelatedToSource($page2, $page);
    }


    /** @test */
    function it_can_retrieve_a_collection_of_its_related_content()
    {
        $page  = Page::find(1);
        $page2 = Page::find(2);

        $page->relate($page2);

        $related = $page->related;

        $this->assertCount(1, $related);
        $this->assertRelatedCollectionContains($related, $page2);
    }


    /** @test */
    function it_can_determine_if_it_has_related_content()
    {
        $page = Page::find(1);

        $this->assertFalse($page->hasRelated());
    }


    /** @test */
    function it_can_determine_if_it_doenst_have_related_content()
    {
        $page = Page::find(1);
        $page->relate(Page::find(2));

        $this->assertTrue($page->hasRelated());
    }


    /** @test */
    function it_can_sync_related_content_from_a_collection_of_models()
    {
        $page  = Page::find(1);
        $page2 = Page::find(2);
        $page3 = Page::find(3);

        $page->relate($page2);

        $page->syncRelated(collect([$page3]));

        $related = $page->related;

        $this->assertCount(1, $related);
        $this->assertModelIsRelatedToSource($page3, $page);
        $this->assertModelIsntRelatedToSource($page2, $page);
    }


    /** @test */
    function it_can_sync_related_content_from_an_array_of_types_and_ids()
    {
        $page  = Page::find(1);
        $page2 = Page::find(2);
        $page3 = Page::find(3);

        $page->relate($page2);

        $page->syncRelated([['id' => 3, 'type' => Page::class]]);

        $related = $page->related;

        $this->assertCount(1, $related);
        $this->assertModelIsRelatedToSource($page3, $page);
        $this->assertModelIsntRelatedToSource($page2, $page);
    }


    /** @test */
    function it_can_sync_related_content_without_detaching()
    {
        $page  = Page::find(1);
        $page2 = Page::find(2);
        $page3 = Page::find(3);

        $page->relate($page2);

        $page->syncRelated(collect([$page3]), false);

        $related = $page->loadRelated();

        $this->assertCount(2, $related);
        $this->assertModelIsRelatedToSource($page3, $page);
        $this->assertModelIsRelatedToSource($page2, $page);
    }


    protected function modelIsRelatedToSource(Model $related, Model $source)
    {
        return (bool)Relatable::where([
            'source_id'    => $source->getKey(),
            'source_type'  => $source->getMorphClass(),
            'related_id'   => $related->getKey(),
            'related_type' => $related->getMorphClass(),
        ])->first();
    }


    /**
     * Custom Assertions
     */
    protected function assertModelIsRelatedToSource(Model $related, Model $source)
    {
        $this->assertTrue($this->modelIsRelatedToSource($related, $source));
    }


    protected function assertModelIsntRelatedToSource(Model $related, Model $source)
    {
        $this->assertFalse($this->modelIsRelatedToSource($related, $source));
    }


    protected function assertRelatedCollectionContains(Collection $collection, Model $related)
    {
        $this->assertTrue($collection->contains(function ($item, $key) use ($related) {
            return $item->id === $related->id && get_class($item) === $related->getMorphClass();
        }));
    }
}