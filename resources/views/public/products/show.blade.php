 @extends('layouts.public')

@section('title', $machine->name.' — '.$company->name)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($machine->short_description ?: $machine->function ?: $machine->name), 150))

@section('content')
    <x-page-header
        :title="$machine->name"
        :subtitle="$machine->short_description ?: $machine->function"
        :crumbs="array_values(array_filter([
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Produk & Mesin', 'url' => route('products.index')],
            $machine->category ? ['label' => $machine->category->name, 'url' => route('products.index', ['kategori' => $machine->category->slug])] : null,
            ['label' => $machine->name],
        ]))"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="grid gap-10 lg:grid-cols-3">
            {{-- Foto utama + galeri --}}
            <div class="lg:col-span-2">
                <div class="overflow-hidden rounded-3xl border border-stone-200 dark:border-gray-700 bg-stone-100 dark:bg-gray-700 shadow-sm">
                    <img src="{{ $machine->photo }}" alt="{{ $machine->name }}" class="aspect-[4/3] w-full object-cover">
                </div>

                @if ($machine->images->isNotEmpty())
                    <div class="mt-4 grid grid-cols-3 gap-3 sm:grid-cols-4">
                        @foreach ($machine->images as $image)
                            <a href="{{ $image->url() }}" target="_blank" rel="noopener"
                               class="group overflow-hidden rounded-xl border border-stone-200 dark:border-gray-700 bg-stone-100 dark:bg-gray-700">
                                <img src="{{ $image->url() }}" alt="{{ $image->caption ?: $machine->name }}" loading="lazy"
                                     class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Informasi ringkas --}}
            <aside class="space-y-5">
                <div class="rounded-2xl border border-stone-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm">
                    @if ($machine->category)
                        <a href="{{ route('products.index', ['kategori' => $machine->category->slug]) }}"
                           class="inline-block rounded-full bg-red-50 dark:bg-red-500/10 px-3 py-1 text-xs font-bold uppercase tracking-wide text-red-700 dark:text-red-400">
                            {{ $machine->category->name }}
                        </a>
                    @endif

                    <h2 class="mt-3 text-xl font-extrabold text-stone-900 dark:text-white">{{ $machine->name }}</h2>

                    @if ($machine->model_code)
                        <p class="mt-1 text-sm font-semibold text-stone-500 dark:text-gray-400">
                            Kode model: <span class="text-red-700 dark:text-red-400">{{ $machine->model_code }}</span>
                        </p>
                    @endif

                    @if ($machine->function)
                        <div class="mt-5 rounded-xl bg-stone-50 dark:bg-gray-800/40 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-stone-400 dark:text-gray-500">Fungsi Mesin</p>
                            <p class="mt-2 text-sm leading-relaxed text-stone-600 dark:text-gray-300">{{ $machine->function }}</p>
                        </div>
                    @endif

                    <div class="mt-5 space-y-2.5 text-sm">
                        @foreach ([
                            'Kapasitas' => $machine->capacity,
                            'Daya' => $machine->power,
                            'Dimensi' => $machine->dimension,
                            'Berat' => $machine->weight,
                            'Material' => $machine->material,
                        ] as $label => $value)
                            @if (filled($value))
                                <div class="flex justify-between gap-4 border-b border-dashed border-stone-200 dark:border-gray-700 pb-2 last:border-0">
                                    <span class="text-stone-500 dark:text-gray-400">{{ $label }}</span>
                                    <span class="text-right font-semibold text-stone-800 dark:text-white">{{ $value }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="mt-6 space-y-2.5">
                        <a href="{{ route('contact', ['mesin' => $machine->slug]) }}"
                           class="block rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-5 py-3 text-center text-sm font-bold text-white transition hover:from-red-700 hover:to-red-800">
                            Minta Penawaran
                        </a>

                        @php $wa = preg_replace('/[^0-9]/', '', (string) $company->whatsapp); @endphp
                        @if ($wa)
                            <a href="https://wa.me/{{ $wa }}?text={{ urlencode('Halo, saya ingin bertanya tentang mesin '.$machine->name) }}"
                               target="_blank" rel="noopener"
                               class="block rounded-xl border border-red-200 bg-red-50 px-5 py-3 text-center text-sm font-bold text-red-700 transition hover:bg-red-200/60 dark:border-red-700/50 dark:bg-red-700/15 dark:text-red-300 dark:hover:bg-red-700/25">
                                Tanya via WhatsApp
                            </a>
                        @endif
                    </div>
                </div>

                <div class="rounded-2xl border border-stone-200 dark:border-gray-700 bg-stone-50 dark:bg-gray-800/40 p-6">
                    <p class="text-xs font-bold uppercase tracking-wide text-stone-400 dark:text-gray-500">Butuh model lain?</p>
                    <p class="mt-2 text-sm leading-relaxed text-stone-600 dark:text-gray-300">
                        Kami dapat menyesuaikan dimensi, kapasitas, dan material mesin sesuai kebutuhan lini produksi Anda.
                    </p>
                    <a href="{{ route('products.index') }}" class="mt-3 inline-block text-sm font-bold text-red-700 dark:text-red-400">
                        Lihat semua model &rarr;
                    </a>
                </div>
            </aside>
        </div>

        {{-- Deskripsi & spesifikasi --}}
        <div class="mt-14 grid gap-10 lg:grid-cols-3">
            @if ($machine->description)
                <div class="lg:col-span-2">
                    <h3 class="text-xl font-extrabold text-stone-900 dark:text-white">Deskripsi &amp; keunggulan</h3>

                    <div class="mt-4 space-y-4 text-sm leading-relaxed text-stone-600 dark:text-gray-300">
                        @foreach (preg_split('/\r\n|\r|\n/', $machine->description) as $paragraph)
                            @if (trim($paragraph) !== '')
                                <p>{{ trim($paragraph) }}</p>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            @if (! empty($machine->specificationLines()))
                <div class="{{ $machine->description ? 'rounded-2xl border border-stone-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm' : 'max-w-2xl lg:col-span-2' }}">
                    <h3 class="text-base font-extrabold text-stone-900 dark:text-white">Spesifikasi teknis</h3>

                    <dl class="mt-4 space-y-3 text-sm">
                        @foreach ($machine->specificationLines() as $label => $value)
                            <div class="flex justify-between gap-4 border-b border-dashed border-stone-200 dark:border-gray-700 pb-2 last:border-0">
                                <dt class="text-stone-500 dark:text-gray-400">{{ $label }}</dt>
                                <dd class="text-right font-semibold text-stone-800 dark:text-white">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endif
        </div>

        {{-- Model serupa --}}
        @if ($related->isNotEmpty())
            <div class="mt-16">
                <h3 class="text-xl font-extrabold text-stone-900 dark:text-white">Model serupa</h3>

                <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $item)
                        <x-machine-card :machine="$item" />
                    @endforeach
                </div>
            </div>
        @endif
    </section>
@endsection
