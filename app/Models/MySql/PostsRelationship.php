<?php


namespace App\Models\MySql;


class PostsRelationship extends Base
{
    protected $table = 'posts_relationship';

    protected $fillable = ['posts_id', 'term_id', 'create_time'];

}