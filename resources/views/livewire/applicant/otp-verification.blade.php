<div>
    @if($verified)
        <x-ui.alert variant="success">{{ $channel === 'email' ? 'Email' : 'WhatsApp' }} Anda telah diverifikasi.</x-ui.alert>
    @else
        <x-ui.card class="max-w-md mx-auto">
            <div class="text-center mb-8">
                <div class="flex size-16 mx-auto items-center justify-center rounded-full bg-primary/10 mb-4">
                    <x-lucide-shield-check class="size-8 text-primary" />
                </div>
                <h2 class="text-display-xs text-ink">Verifikasi {{ $channel === 'email' ? 'Email' : 'WhatsApp' }}</h2>
                <p class="text-body-sm text-mute mt-2">@if(!$codeSent) Klik kirim untuk menerima kode. @else Masukkan kode 6 digit. @endif</p>
            </div>
            @if($error) <x-ui.alert variant="destructive" class="mb-6">{{ $error }}</x-ui.alert> @endif
            @if(!$codeSent)
                <x-ui.button variant="primary" wire:click="sendOtp" class="w-full">Kirim Kode OTP</x-ui.button>
            @else
                <form wire:submit="verify" class="space-y-6">
                    <x-ui.input label="Kode OTP" wire:model="code" maxlength="6" placeholder="000000" class="text-center text-lg tracking-widest font-mono font-medium" />
                    <x-ui.button variant="primary" type="submit" class="w-full">Verifikasi</x-ui.button>
                </form>
                <p class="text-center text-body-sm text-mute mt-6">
                    @if($canResend)
                        <button wire:click="sendOtp" class="text-primary hover:underline font-medium transition-colors">Kirim ulang</button>
                    @else
                        Kirim ulang dalam <span class="font-medium text-ink" wire:poll.1s="decrementCountdown">{{ $countdown }}</span> detik
                    @endif
                </p>
            @endif
        </x-ui.card>
    @endif
</div>
