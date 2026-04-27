@php
    /** @var string $id */
    /** @var string $title */
    $id = $id ?? 'help-sheet';
    $title = $title ?? __('Help');
    $buttonLabel = $buttonLabel ?? __('Help');
@endphp

<button
    type="button"
    class="helpSheet__btn"
    data-help-sheet-open="{{ $id }}"
    aria-controls="{{ $id }}"
    aria-expanded="false"
    title="{{ $buttonLabel }}"
>
    ?
</button>

<div id="{{ $id }}" class="helpSheet" aria-hidden="true">
    <div class="helpSheet__backdrop" data-help-sheet-close="{{ $id }}" aria-hidden="true"></div>
    <aside
        class="helpSheet__panel"
        role="dialog"
        aria-modal="true"
        aria-label="{{ $title }}"
        tabindex="-1"
    >
        <div class="helpSheet__header">
            <div class="helpSheet__title">{{ $title }}</div>
            <button type="button" class="helpSheet__close" data-help-sheet-close="{{ $id }}" aria-label="{{ __('Close') }}">
                ✕
            </button>
        </div>
        <div class="helpSheet__body">
            {{ $slot }}
        </div>
    </aside>
</div>

