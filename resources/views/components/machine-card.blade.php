@props(['machine'])

<a href="{{ route('products.show', $machine) }}" class="machine-card-new group">
    <div class="card-img">
        <img src="{{ $machine->photo }}" alt="{{ $machine->name }}" loading="lazy"
             onerror="this.src='https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=800&auto=format&fit=crop'">

        @if ($machine->category)
            <span class="card-badge">
                {{ $machine->category->name }}
            </span>
        @endif
    </div>

    <div class="card-body">
        @if ($machine->model_code)
            <div class="card-model">{{ $machine->model_code }}</div>
        @endif

        <h3 class="card-title">
            {{ $machine->name }}
        </h3>

        @php $preview = $machine->function; @endphp
        @if ($preview)
            <p class="card-desc">
                {{ \Illuminate\Support\Str::limit(strip_tags($preview), 115) }}
            </p>
        @endif

        <div class="mt-auto pt-3 border-t border-stone-100 dark:border-slate-800 flex items-center justify-between">
            @if ($machine->capacity)
                <span class="text-xs text-stone-400 flex items-center gap-1">
                    <i class="fas fa-weight-hanging text-zinc-600 dark:text-zinc-400 text-[10px]"></i> {{ \Illuminate\Support\Str::limit($machine->capacity, 20) }}
                </span>
            @else
                <span></span>
            @endif

            <span class="card-link">
                Lihat Detail <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
            </span>
        </div>
    </div>
</a>
