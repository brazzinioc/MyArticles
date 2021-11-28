<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Sluggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'slug', 'body', 'image', 'iframe', 'user_id',
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
                'onUpdate' => true,
            ]
        ];
    }


    //Create Extract
    public function getGetExtractAttribute(){
        return substr($this->body, 0, 144) . "...";
    }


    //made URL to show image in client
    public function getGetImageAttribute(){
        if($this->image){
            return url("storage/$this->image");
        }
    }

    //Relationship
    public function user()
    {
        return $this->belongsTo(User::class); //un post pertenece a un usuario
    }
}
