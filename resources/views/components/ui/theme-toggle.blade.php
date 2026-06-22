<button 
    type="button" 
    x-data="{ 
        theme: localStorage.theme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
        toggleTheme() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            localStorage.theme = this.theme;
            if (this.theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    }" 
    @click="toggleTheme()"
    title="Toggle dark mode"
    {{ $attributes->merge(['class' => 'flex size-8 shrink-0 items-center justify-center rounded-sm text-mute transition-colors hover:bg-canvas-soft hover:text-ink']) }}
>
    <x-lucide-sun x-show="theme === 'dark'" x-cloak class="size-4" />
    <x-lucide-moon x-show="theme === 'light'" x-cloak class="size-4" />
    <span class="sr-only">Toggle dark mode</span>
</button>
