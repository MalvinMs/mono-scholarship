<div class="relative w-full overflow-auto">
    <table {{ $attributes->merge(['class' => 'w-full caption-bottom text-body-sm']) }}>
        @if(isset($header))
            <thead class="[&_tr]:border-b border-hairline sticky top-0 bg-canvas z-10">
                {{ $header }}
            </thead>
        @endif
        <tbody class="[&_tr:last-child]:border-0 bg-canvas">
            {{ $slot }}
        </tbody>
    </table>
</div>
