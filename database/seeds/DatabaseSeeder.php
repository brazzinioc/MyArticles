<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        //Create a user
        App\User::create([
            'name' => 'Jhon Doe Does',
            'email' => 'jhondoe@admin.com',
            'password' => bcrypt('12345678')
        ]);

        //Create 24 Posts, using Faker
        factory(App\Post::class, 24)->create();

    }
}
