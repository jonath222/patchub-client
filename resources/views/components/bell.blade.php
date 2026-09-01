@php
    $hasUnread = $unreadCount > 0;
@endphp

<div class="patchub-bell">
    <details style="list-style: none;">
        <summary class="patchub-bell__trigger" style="cursor: pointer;">
            🔔
            @if ($hasUnread)
                <span class="patchub-bell__badge">{{ $unreadCount }}</span>
            @endif
        </summary>

        <div class="patchub-bell__dropdown">
            {{-- Header --}}
            <div class="patchub-bell__header">
                <strong class="patchub-bell__title">Patch notes</strong>

                @if ($hasUnread)
                    <form method="POST" action="{{ route('patchub.mark-as-read') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="patchub-bell__mark-read">
                            Tout marquer comme lu
                        </button>
                    </form>
                @endif
            </div>

            {{-- Items --}}
            @forelse ($patchNotes as $patchNote)
                <div class="patchub-bell__item">
                    <div class="patchub-bell__item-title">
                        {{ $patchNote->title }}
                        @if ($patchNote->version)
                            <span class="patchub-bell__item-version">v{{ $patchNote->version }}</span>
                        @endif
                    </div>
                    <div class="patchub-bell__item-content">
                        {{ \Illuminate\Support\Str::limit(strip_tags($patchNote->content), 120) }}
                    </div>
                        @php
                            $hasPatchNotesRoute = app('router')->has('patchub.patch-notes');
                        @endphp
                        @if ($hasPatchNotesRoute)
                            <a href="{{ route('patchub.patch-notes', $patchNote->id) }}" class="patchub-bell__item-link">
                                Lire la suite →
                            </a>
                        @endif
                </div>
            @empty
                <p class="patchub-bell__empty">Aucune patch note pour le moment.</p>
            @endforelse

            {{-- Footer with view all link --}}
            @php
                $hasPatchNotesRoute = app('router')->has('patchub.patch-notes');
            @endphp
            @if ($hasPatchNotesRoute && $patchNotes->count() > 0)
                <div class="patchub-bell__footer">
                    <a href="{{ route('patchub.patch-notes') }}" class="patchub-bell__view-all">
                        Voir toutes les patch notes →
                    </a>
                </div>
            @endif
        </div>
    </details>
</div>
