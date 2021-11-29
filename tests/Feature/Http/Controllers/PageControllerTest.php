<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Pagination\Paginator;
use Tests\TestCase;
use App\{ Post, User };

class PageControllerTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /**
     * Test home page without login.
     *
     * @return void
     */
    public function test_home_page_with_pagination()
    {
        $user = factory(User::class)->create();
        $posts = factory(Post::class, 50)->create( [ "user_id" => $user->id ] );

        $response = $this->get('/');

        $response->assertStatus(200)
                ->assertViewIs('posts')
                ->assertViewHas('posts')
                ->assertSee('Next');

        $this->assertInstanceOf(Paginator::class, $response->viewData('posts')); // Check if the view data is an instance of Paginator

        $this->assertEquals(10, $response->viewData('posts')->count() ); // Posts quantity in first page

        $this->assertEquals(50, $response->viewData('posts')->total() ); // Total posts quantity
    }

    /**
     * Test view a post with valid slug.
     *
     * @return void
     */
    public function test_view_a_post_with_valid_slug()
    {
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create( [ "user_id" => $user->id ] );

        $response = $this->get( route('post', [ 'slug' => $post->slug ] ) );

        $response->assertStatus(200)
                ->assertViewIs('post')
                ->assertViewHas('post')
                ->assertSee($post->title)
                ->assertSee($post->body)
                ->assertSee($post->user->name)
                ->assertSee($post->iframe);
    }


    /**
     * Test view a post with invalid slug.
     * @return void
     */
    public function test_view_a_post_with_invalid_slug()
    {
        $response = $this->get( route('post', [ 'slug' => $this->faker->slug(10) ] ) );

        $response->assertStatus(404);
    }
}
