@php
    use Patchub\Client\Markdown\MarkdownConverter;

    $hasUnread = $unreadCount > 0;
    $hasPatchNotesRoute = app('router')->has('patchub.patch-notes');
@endphp

<div class="dropdown dropdown-start dropdown-top fixed left-6 bottom-6 z-50">
    {{-- Trigger --}}
    <div tabindex="0" role="button" class="btn btn-circle shadow-lg z-50 text-white"
        style="background-color: var(--color-info); border-color: var(--color-info);" aria-label="Afficher les patch notes">

        <div class="indicator">
            <span class="text-lg">🔔</span>
            @if ($hasUnread)
                <span class="badge badge-xs badge-primary indicator-item"></span>
            @endif
        </div>
    </div>

    {{-- Dropdown Content --}}
    <div tabindex="0"
        class="dropdown-content card card-compact w-80 sm:w-96 p-2 shadow-xl bg-base-100 text-base-content border border-base-200 mb-2">
        <div class="card-body p-0">
            {{-- Header --}}
            <div class="flex items-center justify-between pb-2 border-b border-base-200">
                <h3 class="font-bold text-lg">Patch notes</h3>

                @if ($hasUnread)
                    <form method="POST" action="{{ route('patchub.mark-as-read') }}" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-xs text-primary">
                            Tout marquer comme lu
                        </button>
                    </form>
                @endif
            </div>

            {{-- Items Container --}}
            <div class="flex flex-col gap-2 max-h-96 overflow-y-auto my-2 pr-1">
                @forelse ($patchNotes as $patchNote)
                    <a href="#" class="p-1 pb-3 mt-1 hover:bg-base-200 transition-colors text-left group block border-b border-base-300"
                        data-modal-id="{{ $patchNote->id }}" data-modal-title="{{ e($patchNote->title) }}"
                        data-modal-version="{{ e($patchNote->version) }}"
                        data-modal-content="{{ base64_encode(MarkdownConverter::convert($patchNote->content)) }}"
                        onclick="patchubOpenModal(event, this)">
                        <div class="font-semibold flex items-center justify-between text-base-content">
                            <span>{{ $patchNote->title }}</span>
                            @if ($patchNote->version)
                                <span class="badge badge-sm badge-outline">v{{ $patchNote->version }}</span>
                            @endif
                        </div>
                        <div class="text-sm opacity-70 line-clamp-2 mt-1 patchub-markdown text-base-content">
                            {!! MarkdownConverter::convert($patchNote->content) !!}
                        </div>
                        <span class="text-xs text-primary font-medium mt-2 inline-block group-hover:underline">Lire plus →</span>
                    </a>
                @empty
                    <p class="text-center text-sm opacity-60 py-4">Aucune patch note pour le moment.</p>
                @endforelse
            </div>

            {{-- Footer --}}
            @if ($hasPatchNotesRoute && $patchNotes->count() > 0)
                <div class="pt-2 border-t border-base-200 text-center">
                    <a href="{{ route('patchub.patch-notes') }}" class="link link-primary link-hover text-sm font-medium">
                        Voir toutes les patch notes →
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal DaisyUI --}}
<dialog id="patchub-modal" class="modal">
    <div class="modal-box w-11/12 max-w-3xl bg-base-100 text-base-content shadow-2xl">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <div id="patchub-modal-body"></div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>


<script>
    function patchubOpenModal(event, element) {
        event.preventDefault();
        event.stopPropagation();

        const modal = document.getElementById('patchub-modal');
        const body = document.getElementById('patchub-modal-body');

        const binaryString = atob(element.dataset.modalContent);
        const bytes = new Uint8Array(binaryString.length);
        for (let i = 0; i < binaryString.length; i++) {
            bytes[i] = binaryString.charCodeAt(i);
        }
        const decodedContent = new TextDecoder().decode(bytes);

        const data = {
            id: element.dataset.modalId,
            title: element.dataset.modalTitle,
            version: element.dataset.modalVersion,
            content: decodedContent
        };

        body.innerHTML = `
        <h3 class="font-bold text-xl flex items-center gap-2 text-base-content">
            ${data.title}
            ${data.version ? `<span class="badge badge-primary badge-sm">v${data.version}</span>` : ''}
        </h3>
        <div class="patchub-markdown mt-4 text-base-content">
            ${data.content}
        </div>
    `;

        modal.showModal();
    }
</script>
