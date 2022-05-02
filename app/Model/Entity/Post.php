<?php

namespace App\Model\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

/**
 * @property int id
 * @property string title
 * @property string body
 * @property string attachment
 * @property int user_id
 * @method static create(array $validated)
 */
class Post extends Model
{
    protected $table = 'posts';

    protected $guarded = [];

    protected $appends = ['file_url'];

    public function getFileUrlAttribute(): ?string
    {
        if (!empty($this->attachment)) {
            return URL::route('file_download', ['file' => $this->attachment, 'area' => "post$this->id"]);
        }

        return null;
    }
}
