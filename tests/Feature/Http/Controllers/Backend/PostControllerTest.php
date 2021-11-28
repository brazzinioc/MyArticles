<?php

namespace Tests\Feature\Http\Controllers\Backend;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\{User, Post};

class PostControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * Test index route is working. When database has posts.
     *
     * @return void
     */
    public function test_posts_index_with_data()
    {
        $user = factory(User::class)->create();
        $posts = factory(Post::class, 20)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
                        ->get( route('posts.index') );

        $response->assertStatus(200)
                ->assertViewIs('posts.index')
                ->assertViewHas('posts');

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $response->viewData('posts'));
    }

    /**
     * test index route is working. When database has no posts.
     *
     * @return void
     */
    public function test_posts_index_without_data()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->get( route('posts.index') );

        $response->assertStatus(200)
                ->assertViewIs('posts.index')
                ->assertViewHas('posts')
                ->assertSee('Aún no se publicó ningún artículo. Sé el primero en pubicar.');
    }



}
