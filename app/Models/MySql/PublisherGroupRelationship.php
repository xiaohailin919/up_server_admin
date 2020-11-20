<?php

namespace App\Models\MySql;

class PublisherGroupRelationship extends Base
{
    protected $table = 'publisher_group_relationship';

    protected $fillable = ['publisher_id', 'publisher_group_id'];
}
