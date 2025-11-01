@if(is_array($eval->components ?? null))
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 my-4">
        @foreach (['pemahaman'=>'Pemahaman','metode'=>'Metodologi','ketepatan'=>'Ketepatan','analisa'=>'Analisa & Laporan','waktu'=>'Waktu'] as $k=>$label)
            <div class="rounded-lg p-3 bg-indigo-50 border border-indigo-100">
                <div class="text-xs text-indigo-700">{{ $label }}</div>
                <div class="text-xl font-bold text-indigo-900">{{ $eval->components[$k] ?? '—' }}</div>
            </div>
        @endforeach
    </div>
@endif
