@extends('layouts.public')

@section('title', $machine->name.' — '.$company->name)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($machine->function ?: $machine->name), 150))

@section('content')
    <x-page-header
        :title="$machine->name"
        :crumbs="array_values(array_filter([
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Produk & Mesin', 'url' => route('products.index')],
            $machine->category ? ['label' => $machine->category->name, 'url' => route('products.index', ['kategori' => $machine->category->slug])] : null,
            ['label' => $machine->name],
        ]))"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="grid gap-10 lg:grid-cols-3">
            {{-- Foto Utama + Galeri --}}
            <div class="lg:col-span-2 min-w-0">
                <div class="overflow-hidden rounded-2xl border border-stone-200 dark:border-slate-800 bg-stone-50 dark:bg-slate-800/60 shadow-md">
                    <img src="{{ $machine->photo }}" alt="{{ $machine->name }}" class="aspect-[4/3] w-full object-cover"
                         onerror="this.src='https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=800&auto=format&fit=crop'">
                </div>

                @if ($machine->images->isNotEmpty())
                    <div class="mt-4 grid grid-cols-3 gap-3 sm:grid-cols-4">
                        @foreach ($machine->images as $image)
                            <a href="{{ $image->url() }}" target="_blank" rel="noopener"
                               class="group overflow-hidden rounded-xl border border-stone-200 dark:border-slate-800 bg-stone-50 dark:bg-slate-800 shadow-sm transition hover:border-zinc-900 dark:hover:border-white">
                                <img src="{{ $image->url() }}" alt="{{ $image->caption ?: $machine->name }}" loading="lazy"
                                     class="aspect-[4/3] w-full object-cover transition duration-300 group-hover:scale-105">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Ringkasan & Spesifikasi Utama --}}
            <aside class="space-y-5 min-w-0">
                <div class="rounded-2xl border border-stone-200 dark:border-slate-800 bg-white dark:bg-slate-800/80 p-6 shadow-sm">
                    @if ($machine->category)
                        <a href="{{ route('products.index', ['kategori' => $machine->category->slug]) }}"
                           class="inline-block rounded-full bg-zinc-100 px-3 py-1 text-xs font-bold uppercase tracking-wider text-zinc-900 dark:bg-zinc-800 dark:text-white">
                            {{ $machine->category->name }}
                        </a>
                    @endif

                    <h2 class="mt-3 text-2xl font-extrabold text-navy dark:text-white">{{ $machine->name }}</h2>

                    @if ($machine->model_code)
                        <p class="mt-1 text-xs font-bold uppercase tracking-wider text-zinc-600 dark:text-zinc-400">
                            Kode Model: {{ $machine->model_code }}
                        </p>
                    @endif

                    @if ($machine->function)
                        <div class="mt-5 rounded-xl bg-stone-50 dark:bg-gray-800/40 p-4 min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wide text-stone-400 dark:text-gray-500">Fungsi Mesin</p>
                            <p class="mt-2 text-sm leading-relaxed text-stone-600 dark:text-gray-300 break-all">{{ $machine->function }}</p>
                        </div>
                    @endif
                    <div class="mt-5 space-y-2.5 text-sm">
                        @foreach ([
                            'Kapasitas' => $machine->capacity,
                            'Daya' => $machine->power,
                            'Dimensi' => $machine->dimension,
                            'Berat' => $machine->weight,
                        ] as $label => $value)
                            @if (filled($value))
                                <div class="grid grid-cols-3 gap-4 border-b border-dashed border-stone-200 dark:border-gray-700 pb-2 last:border-0 min-w-0">
                                    <span class="text-stone-500 dark:text-gray-400">{{ $label }}</span>
                                    <span class="col-span-2 text-right font-semibold text-stone-800 dark:text-white break-all min-w-0">{{ $value }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    @php $wa = preg_replace('/[^0-9]/', '', (string) $company->whatsapp); @endphp
                    @if ($wa)
                        <div class="mt-6">
                            <a href="https://wa.me/{{ $wa }}?text={{ urlencode('Halo Manufactur Dynamic, saya ingin konsultasi mengenai mesin: '.$machine->name.($machine->model_code ? ' ('.$machine->model_code.')' : '')) }}"
                               target="_blank" rel="noopener"
                               class="flex w-full items-center justify-center gap-2 rounded-lg bg-zinc-900 px-5 py-3 text-center text-sm font-bold text-white shadow-md transition hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">
                                <i class="fab fa-whatsapp text-lg"></i>
                                <span>Konsultasi via WhatsApp</span>
                            </a>
                        </div>
                    @endif
                </div>
            </aside>
        </div>

        {{-- Deskripsi & spesifikasi --}}
        @if ($machine->description || ! empty($machine->specificationLines()))
            <div class="mt-14 grid gap-10 {{ $machine->description && ! empty($machine->specificationLines()) ? 'lg:grid-cols-3' : 'grid-cols-1' }}">
                @if ($machine->description)
                    <div class="{{ ! empty($machine->specificationLines()) ? 'lg:col-span-2' : 'max-w-4xl' }} min-w-0">
                        <h3 class="text-xl font-extrabold text-stone-900 dark:text-white">Deskripsi &amp; keunggulan</h3>

                        <div class="mt-4 space-y-4 text-sm leading-relaxed text-stone-600 dark:text-gray-300">
                            @foreach (preg_split('/\r\n|\r|\n/', $machine->description) as $paragraph)
                                @if (trim($paragraph) !== '')
                                    <p class="break-all">{{ trim($paragraph) }}</p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (! empty($machine->specificationLines()))
                    <div class="{{ $machine->description ? 'rounded-2xl border border-stone-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm' : 'max-w-2xl' }} min-w-0">
                        <h3 class="text-base font-extrabold text-stone-900 dark:text-white">Spesifikasi tambahan</h3>

                        <dl class="mt-4 space-y-3 text-sm">
                            @foreach ($machine->specificationLines() as $label => $value)
                                <div class="grid grid-cols-3 gap-4 border-b border-dashed border-stone-200 dark:border-gray-700 pb-2 last:border-0 min-w-0">
                                    <dt class="text-stone-500 dark:text-gray-400">{{ $label }}</dt>
                                    <dd class="col-span-2 text-right font-semibold text-stone-800 dark:text-white break-all min-w-0">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endif
            </div>
        @endif

        {{-- Model Serupa --}}
        @if ($related->isNotEmpty())
            <div class="mt-16 border-t border-stone-200 dark:border-slate-800 pt-12">
                <h3 class="text-2xl font-extrabold text-navy dark:text-white">Model Mesin Serupa</h3>
                <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $item)
                        <x-machine-card :machine="$item" />
                    @endforeach
                </div>
            </div>
        @endif
    </section>
@endsection
