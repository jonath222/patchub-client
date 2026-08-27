<?php

namespace Patchub\Client\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MarkAsReadController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $request->user()->markPatchNotesAsRead();

        return back();
    }
}
