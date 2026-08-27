<?php

namespace Patchub\Client\Concerns;

use Patchub\Client\Models\PatchNote;

trait HasPatchNotes
{
    /**
     * Nombre de patch notes publiées depuis la dernière lecture de l'utilisateur.
     */
    public function unreadPatchNotesCount(): int
    {
        return PatchNote::query()
            ->where('published_at', '>', $this->patchub_last_read_at ?? '1970-01-01')
            ->count();
    }

    /**
     * Marque toutes les patch notes actuelles comme lues par l'utilisateur.
     */
    public function markPatchNotesAsRead(): void
    {
        $this->forceFill(['patchub_last_read_at' => now()])->save();
    }
}
