<?php

namespace App\Model\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed|string message
 * @property mixed id
 */
class Log extends Model
{
    protected $table = 'log';
}
