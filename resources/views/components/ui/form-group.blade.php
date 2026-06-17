@props(['label' => null, 'error' => null, 'required' => false, 'description' => null])

<div class="space-y-1.5">
    @if($label)
        <label class="block text-body-sm-strong peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
            {{ $label }}
            @if($required)
                <span class="text-error">*</span>
            @endif
        </label>
    @endif
    {{ $slot }}
    @if($description)
        <p class="text-caption text-mute mt-1">{{ $description }}</p>
    @endif
    @if($error)
        <p class="text-caption text-error mt-1">{{ $error }}</p>
    @endif
</div>
