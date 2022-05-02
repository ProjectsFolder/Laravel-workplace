<?php

namespace App\Model\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string title
 * @property string body
 * @property string attachment
 * @property int user_id
 */
class Post extends Model
{
    protected $table = 'posts';
}
