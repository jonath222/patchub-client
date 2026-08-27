<?php

namespace Patchub\Client\View\Components;

use Illuminate\View\Component;
use Patchub\Client\Models\PatchNote;

class Bell extends Component
{
    public function render()
    {
        $user = auth()->user();

        return view('patchub-client::components.bell', [
            'patchNotes' => PatchNote::query()->latest('published_at')->limit(10)->get(),
            'unreadCount' => $user && method_exists($user, 'unreadPatchNotesCount')
                ? $user->unreadPatchNotesCount()
                : 0,
        ]);
    }
}
