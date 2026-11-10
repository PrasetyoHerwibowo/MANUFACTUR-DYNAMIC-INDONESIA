<nav role="navigation" aria-label="Navigasi Halaman" class="product-pagination">
    {{-- Tombol Sebelumnya --}}
    @if ($paginator->onFirstPage())
        <span class="page-link disabled" aria-disabled="true" aria-label="Sebelumnya">
            <i class="fas fa-chevron-left"></i>
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="page-link" aria-label="Sebelumnya">
            <i class="fas fa-chevron-left"></i>
        </a>
    @endif

    {{-- Daftar Angka Halaman --}}
    <div class="page-numbers">
        @if ($paginator->hasPages())
            @foreach ($elements as $element)
                {{-- Separator Titik Tiga --}}
                @if (is_string($element))
                    <span class="page-link dots" aria-disabled="true">{{ $element }}</span>
                @endif

                {{-- Link Halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="page-link active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        @else
            {{-- Selalu tampilkan halaman 1 aktif saat data belum mencapai 2 halaman --}}
            <span class="page-link active" aria-current="page">1</span>
        @endif
    </div>

    {{-- Tombol Berikutnya --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="page-link" aria-label="Berikutnya">
            <i class="fas fa-chevron-right"></i>
        </a>
    @else
        <span class="page-link disabled" aria-disabled="true" aria-label="Berikutnya">
            <i class="fas fa-chevron-right"></i>
        </span>
    @endif
</nav>
