<x-app-layout>
    <div class="max-w-3xl mx-auto mt-10 bg-white shadow-xl rounded-2xl overflow-hidden" 
         x-data="scoringForm()"
         x-init="recalculate()">

        <div class="p-6 border-b bg-gradient-to-r from-indigo-600 to-violet-600 text-white">
            <h1 class="text-2xl font-semibold">Form Penilaian Peserta</h1>
            <p class="text-sm opacity-90">Nilai otomatis dihitung berdasarkan bobot tiap aspek</p>
        </div>

        <form method="POST" action="{{ route('admin.scores.store') }}" class="p-6 space-y-6">
            @csrf
            <template x-for="(item, key) in aspek" :key="key">
                <div>
                    <div class="flex justify-between mb-1">
                        <label class="font-semibold text-slate-700" x-text="item.label"></label>
                        <span class="text-sm text-indigo-600 font-medium" 
                              x-text="`${(item.weight*100).toFixed(0)}%`"></span>
                    </div>
                    <input type="range" min="0" max="100" x-model.number="item.score"
                           class="w-full accent-indigo-600">
                    <div class="flex justify-between text-xs text-slate-500">
                        <span>0</span>
                        <span x-text="item.score + ' / 100'"></span>
                    </div>
                    <input type="hidden" :name="key" :value="item.score">
                </div>
            </template>

            <div class="pt-4 border-t">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-slate-800">Total Nilai</h2>
                    <div class="text-3xl font-bold text-indigo-700" x-text="total.toFixed(2) + ' / 100'"></div>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow">
                    Simpan Penilaian
                </button>
            </div>
        </form>
    </div>

    <script>
        function scoringForm() {
            return {
                aspek: {
                    pemahaman_kasus: { label: 'Pemahaman Kasus', weight: 0.15, score: 0 },
                    langkah_teknis: { label: 'Langkah Teknis / Metodologi', weight: 0.20, score: 0 },
                    ketepatan_hasil: { label: 'Ketepatan Hasil', weight: 0.30, score: 0 },
                    analisa_laporan: { label: 'Analisa & Laporan', weight: 0.30, score: 0 },
                    manajemen_waktu: { label: 'Manajemen Waktu', weight: 0.05, score: 0 },
                },
                total: 0,
                recalculate() {
                    this.$watch('aspek', () => {
                        let t = 0;
                        for (const key in this.aspek) {
                            t += this.aspek[key].score * this.aspek[key].weight;
                        }
                        this.total = t;
                    }, { deep: true });
                }
            }
        }
    </script>
</x-app-layout>
