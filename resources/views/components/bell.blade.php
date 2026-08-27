@php
    $hasUnread = $unreadCount > 0;
@endphp

<details class="patchub-bell">
    <summary style="list-style: none; cursor: pointer; position: relative; display: inline-block;">
        🔔
        @if ($hasUnread)
            <span style="position: absolute; top: -4px; right: -8px; background: red; color: white; border-radius: 999px; font-size: 10px; padding: 1px 5px;">
                {{ $unreadCount }}
            </span>
        @endif
    </summary>

    <div style="position: absolute; margin-top: 8px; min-width: 320px; max-height: 400px; overflow-y: auto; background: white; border: 1px solid #ddd; border-radius: 8px; padding: 12px; box-shadow: 0 4px 12px rgba(0,0,0,.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <strong>Patch notes</strong>

            @if ($hasUnread)
                <form method="POST" action="{{ route('patchub.mark-as-read') }}">
                    @csrf
                    <button type="submit" style="font-size: 12px;">Tout marquer comme lu</button>
                </form>
            @endif
        </div>

        @forelse ($patchNotes as $patchNote)
            <div style="padding: 8px 0; border-top: 1px solid #eee;">
                <div style="font-weight: 600;">
                    {{ $patchNote->title }}
                    @if ($patchNote->version)
                        <span style="color: #888; font-weight: 400;">({{ $patchNote->version }})</span>
                    @endif
                </div>
                <div style="font-size: 13px; color: #555;">
                    {{ \Illuminate\Support\Str::limit(strip_tags($patchNote->content), 120) }}
                </div>
            </div>
        @empty
            <p style="font-size: 13px; color: #888;">Aucune patch note pour le moment.</p>
        @endforelse
    </div>
</details>
