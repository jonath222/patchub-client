<?php

namespace Patchub\Client\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'content', 'version', 'published_at'])]
class PatchNote extends Model
{
    protected $table = 'patchub_patch_notes';

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
}
