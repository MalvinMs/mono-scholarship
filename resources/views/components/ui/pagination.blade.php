@props(['paginator'])

@if($paginator->hasPages())
<nav class="flex items-center justify-between border-t border-hairline px-4 py-3 sm:px-6" aria-label="Pagination">
    <div class="hidden sm:block">
        <p class="text-caption text-mute">
            Menampilkan
            <span class="font-medium text-ink">{{ $paginator->firstItem() }}</span>
            sampai
            <span class="font-medium text-ink">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-medium text-ink">{{ $paginator->total() }}</span>
            data
        </p>
    </div>
    <div class="flex flex-1 justify-between sm:justify-end gap-2">
        @if($paginator->onFirstPage())
            <span class="inline-flex items-center justify-center rounded-sm border border-hairline bg-canvas px-3 py-2 text-body-sm font-medium text-mute opacity-50 cursor-not-allowed">Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center justify-center rounded-sm border border-hairline bg-canvas px-3 py-2 text-body-sm font-medium transition-colors hover:bg-canvas-soft hover:text-ink">Sebelumnya</a>
        @endif
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center justify-center rounded-sm border border-hairline bg-canvas px-3 py-2 text-body-sm font-medium transition-colors hover:bg-canvas-soft hover:text-ink">Berikutnya</a>
        @else
            <span class="inline-flex items-center justify-center rounded-sm border border-hairline bg-canvas px-3 py-2 text-body-sm font-medium text-mute opacity-50 cursor-not-allowed">Berikutnya</span>
        @endif
    </div>
</nav>
@endif
