@if (session('success') || session('error') || session('status'))
    <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
        @if (session('success'))
            <x-alert type="success">{{ session('success') }}</x-alert>
        @endif

        @if (session('status'))
            <x-alert type="info">{{ session('status') }}</x-alert>
        @endif

        @if (session('error'))
            <x-alert type="error" class="mt-3">{{ session('error') }}</x-alert>
        @endif
    </div>
@endif

@if ($errors->any() && ! request()->routeIs('admin.*'))
    <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
        <x-alert type="error">
            <p class="font-bold">Mohon periksa kembali data yang Anda kirim:</p>
            <ul class="mt-2 list-inside list-disc space-y-1 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    </div>
@endif
