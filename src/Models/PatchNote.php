<?php

namespace Patchub\Client\Models;

use Illuminate\Database\Eloquent\Model;

class PatchNote extends Model
{
    protected $table = 'patchub_patch_notes';

    protected $fillable = ['title', 'content', 'version', 'published_at'];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
}
