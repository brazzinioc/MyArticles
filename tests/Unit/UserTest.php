<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\ { User, Post } ;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test relationship between User and Post.
     *
     * @return void
     */
    public function test_user_has_many_post()
    {
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create(['user_id' => $user->id]);

        $this->assertEquals(1, $user->posts->count());
        $this->assertInstanceOf(EloquentCollection::class, $user->posts);
    }
}
