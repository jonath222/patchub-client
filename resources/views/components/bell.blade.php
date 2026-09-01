@php
    use Patchub\Client\Markdown\MarkdownConverter;
    
    $hasUnread = $unreadCount > 0;
    $hasPatchNotesRoute = app('router')->has('patchub.patch-notes');
@endphp

<div class="patchub-bell">
    <details class="patchub-bell__details">
        <summary class="patchub-bell__trigger" aria-label="Afficher les patch notes">
            <span aria-hidden="true">🔔</span>
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
                <a href="#" class="patchub-bell__item" data-modal-id="{{ $patchNote->id }}" data-modal-title="{{ e($patchNote->title) }}" data-modal-version="{{ e($patchNote->version) }}" data-modal-content="{{ base64_encode(MarkdownConverter::convert($patchNote->content)) }}" onclick="patchubOpenModal(event, this)">
                    <div class="patchub-bell__item-title">
                        {{ $patchNote->title }}
                        @if ($patchNote->version)
                            <span class="patchub-bell__item-version">v{{ $patchNote->version }}</span>
                        @endif
                    </div>
                    <div class="patchub-bell__item-content">
                        {{ \Illuminate\Support\Str::limit(strip_tags($patchNote->content), 120) }}
                    </div>
                </a>
            @empty
                <p class="patchub-bell__empty">Aucune patch note pour le moment.</p>
            @endforelse

            {{-- Footer with view all link --}}
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

{{-- Modal --}}
<div id="patchub-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div id="patchub-modal-content" style="position: relative; background: white; border-radius: 8px; padding: 2rem; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto; box-shadow: 0 20px 25px rgba(0, 0, 0, 0.15);">
        <button onclick="patchubCloseModal()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; padding: 0; line-height: 1;">×</button>
        <div id="patchub-modal-body"></div>
    </div>
</div>

<style>
    .patchub-bell__item {
        display: block;
        text-decoration: none;
        cursor: pointer;
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s;
    }
    .patchub-bell__item:hover {
        background-color: #f9fafb;
    }
</style>

<script>
function patchubOpenModal(event, element) {
    event.preventDefault();
    event.stopPropagation();
    
    const overlay = document.getElementById('patchub-modal-overlay');
    const body = document.getElementById('patchub-modal-body');
    
    // Decode base64 content properly for UTF-8
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
        <h2 style="margin: 0 0 0.5rem 0; font-size: 1.5rem; font-weight: bold; color: #1f2937;">
            ${data.title}
            ${data.version ? `<span style="background: #dbeafe; color: #1e40af; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; margin-left: 0.5rem;">v${data.version}</span>` : ''}
        </h2>
        <div class="patchub-markdown" style="margin-top: 1.5rem; color: #374151; line-height: 1.6;">
            ${data.content}
        </div>
    `;
    
    overlay.style.display = 'flex';
}

function patchubCloseModal() {
    const overlay = document.getElementById('patchub-modal-overlay');
    overlay.style.display = 'none';
}

document.getElementById('patchub-modal-overlay').addEventListener('click', function(event) {
    if (event.target === this) {
        patchubCloseModal();
    }
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        patchubCloseModal();
    }
});
</script>
