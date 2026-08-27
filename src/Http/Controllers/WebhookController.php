<?php

namespace Patchub\Client\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Patchub\Client\Models\PatchNote;

class WebhookController extends Controller
{
    /**
     * Réceptionne une patch note envoyée par le dashboard Patchub et la stocke localement.
     * La signature a déjà été vérifiée par le middleware VerifySignature avant d'arriver ici.
     */
    public function store(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
            'version' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],
        ]);

        $patchNote = PatchNote::updateOrCreate(
            ['title' => $validated['title'], 'version' => $validated['version'] ?? null],
            [
                'content' => $validated['content'],
                'published_at' => $validated['published_at'] ?? now(),
            ],
        );

        return ['received' => true, 'id' => $patchNote->id];
    }
}
