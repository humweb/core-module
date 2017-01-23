<?php

use Humweb\Tests\Core\Fake\Page;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

class CreatePagesTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pages', function($table)
		{
			$table->increments('id');
			$table->integer('parent_id')->default(0);
			$table->string('uri');
			$table->string('title');
			$table->string('slug');
			$table->string('layout')->nullable();
			$table->text('content');
			$table->boolean('published');
			$table->integer('created_by');
			$table->datetime('published_at')->nullable();
			$table->timestamps();
			$table->text('css')->nullable();
			$table->text('js')->nullable();
			$table->string('meta_title')->nullable();
			$table->string('meta_description')->nullable();
			$table->enum('meta_robots', array('noindex','nofollow','both','index','all'))->default('all');
			$table->boolean('is_index')->default(0);
			$table->boolean('comment_status')->default(false);
			$table->integer('order');

			$table->index('parent_id');
			$table->index('created_at');
			$table->index('uri');
		});


        DB::table('pages')->insert([
            'created_by'       => 1,
            'slug'             => Str::slug('Test title'),
            'title'            => 'Test title',
            'parent_id'        => 0,
            'uri'              => Str::slug('Test title'),
            'layout'           => '',
            'content'          => 'Default content',
            'published'        => 1,
            'css'              => '',
            'js'               => '',
            'meta_title'       => 'Test title',
            'meta_description' => '',
            'is_index'         => 1,
            'order'            => 1,
        ]);
        DB::table('pages')->insert([
            'created_by'       => 1,
            'slug'             => Str::slug('Test title 2'),
            'title'            => 'Test title 2',
            'parent_id'        => 0,
            'uri'              => Str::slug('Test title 2'),
            'layout'           => '',
            'content'          => 'Default content',
            'published'        => 1,
            'css'              => '',
            'js'               => '',
            'meta_title'       => 'Test title 2',
            'meta_description' => '',
            'is_index'         => 1,
            'order'            => 1,
        ]);
        DB::table('pages')->insert([
            'created_by'       => 1,
            'slug'             => Str::slug('Test title 3'),
            'title'            => 'Test title 3',
            'parent_id'        => 0,
            'uri'              => Str::slug('Test title 3'),
            'layout'           => '',
            'content'          => 'Default content',
            'published'        => 1,
            'css'              => '',
            'js'               => '',
            'meta_title'       => 'Test title 3',
            'meta_description' => '',
            'is_index'         => 1,
            'order'            => 1,
        ]);

        $homePage = Page::create([
            'created_by'       => 1,
            'slug'             => 'home',
            'parent_id'        => 0,
            'uri'              => 'home',
            'title'            => 'Home',
            'content'          => 'Welcome to your new site!',
            'published'        => 1,
            'is_index'         => 1,
            'order'            => 0
        ]);

    }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pages');
	}

}
