<!-- MODAL Pelaporan Posyandu -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal-box w-[450px] max-w-[90vw]">
        <!-- Header -->
        <div class="flex items-center justify-between mb-3 pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                    <i class="bi bi-exclamation-triangle-fill text-amber-500 text-sm"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-base">Laporan & Balasan</h3>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M18 6L6 18M6 6l12 12" stroke-width="2" stroke-linecap="round" />
                </svg>
            </button>
        </div>

        <!-- Tab Navigation -->
        <div class="flex border-b border-gray-100 mb-4">
            <button type="button" id="tabFormBtn" onclick="switchModalTab('form')"
                class="flex-1 pb-2 text-center text-xs font-bold text-purple-600 border-b-2 border-purple-500 focus:outline-none transition-all">
                <i class="bi bi-pencil-square mr-1"></i> Laporkan Posyandu
            </button>
            <button type="button" id="tabListBtn" onclick="switchModalTab('list')"
                class="flex-1 pb-2 text-center text-xs font-bold text-gray-400 border-b-2 border-transparent hover:text-gray-600 focus:outline-none transition-all">
                <i class="bi bi-chat-left-text mr-1"></i> Balasan Admin <span id="reportCountBadge" class="bg-gray-100 text-gray-500 text-2xs px-1.5 py-0.5 rounded-full ml-1">0</span>
            </button>
        </div>

        <!-- TAB 1: FORM -->
        <div id="tabFormContainer">
            <p class="text-xs text-gray-500 mb-4 leading-relaxed">
                Apakah Posyandu di daerah Anda belum terdaftar di peta? Silakan isi formulir di bawah ini untuk melaporkannya kepada Admin.
            </p>

            <!-- Form content / success state container -->
            <div id="reportFormContent">
                <form id="reportForm" onsubmit="submitReport(event)" class="flex flex-col gap-3.5">
                    @csrf
                    <!-- Nama Posyandu -->
                    <div>
                        <label for="report_nama" class="block text-xs font-bold text-gray-700 mb-1">Nama Posyandu <span class="text-red-500">*</span></label>
                        <input type="text" id="report_nama" required
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
                            placeholder="Contoh: Posyandu Dahlia 1">
                    </div>

                    <!-- Alamat Posyandu -->
                    <div>
                        <label for="report_alamat" class="block text-xs font-bold text-gray-700 mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
                        <textarea id="report_alamat" required rows="2"
                            class="w-full border border-gray-200 rounded-xl p-3 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all resize-none"
                            placeholder="Contoh: Jl. Mawar No. 12, RT 02 RW 05, Desa Arjasa"></textarea>
                    </div>

                    <!-- Keterangan / Detail Tambahan -->
                    <div>
                        <label for="report_keterangan" class="block text-xs font-bold text-gray-700 mb-1">Keterangan Tambahan (Opsional)</label>
                        <textarea id="report_keterangan" rows="2"
                            class="w-full border border-gray-200 rounded-xl p-3 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all resize-none"
                            placeholder="Tulis informasi tambahan seperti patokan lokasi, jadwal buka, dll..."></textarea>
                    </div>

                    <!-- Legend -->
                    <div class="text-left text-2xs text-gray-500 mt-1">
                        <span class="text-red-500">*</span> Wajib diisi
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 mt-2">
                        <button type="button" onclick="closeModal()" 
                            class="px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 border border-gray-200 rounded-xl transition-colors text-center">
                            Batal
                        </button>
                        <button type="reset" 
                            class="px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 border border-gray-200 rounded-xl transition-colors text-center">
                            Reset
                        </button>
                        <button type="submit" 
                            class="flex-grow btn-primary !w-auto !py-2 !m-0 !shadow-none !text-xs">
                            Kirim Laporan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Success Message (Hidden by default) -->
            <div id="reportSuccessContainer" class="hidden text-center py-6 flex flex-col items-center gap-3">
                <div class="w-16 h-16 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-500">
                    <i class="bi bi-check-circle-fill text-3xl"></i>
                </div>
                <h4 class="font-bold text-gray-900 text-base">Laporan Berhasil Terkirim</h4>
                <p class="text-xs text-gray-500 max-w-[280px] leading-relaxed mx-auto">
                    Terima kasih atas laporan Anda. Admin akan segera memverifikasi data Posyandu tersebut untuk ditambahkan ke dalam sistem.
                </p>
                <button type="button" onclick="resetReportModal()" 
                    class="mt-3 px-6 py-2 rounded-xl bg-gray-900 text-white text-xs font-semibold hover:bg-gray-800 transition-all shadow-sm">
                    Selesai
                </button>
            </div>
        </div>

        <!-- TAB 2: LIST & REPLIES -->
        <div id="tabListContainer" class="hidden flex flex-col">
            <!-- Scrollable Comments Area -->
            <div id="reportsScrollArea" class="max-h-[320px] overflow-y-auto pr-1 flex flex-col gap-4">
                <!-- Loading State -->
                <div id="listLoadingState" class="flex flex-col items-center justify-center py-10 gap-2">
                    <div class="animate-spin rounded-full h-6 w-6 border-2 border-purple-500 border-t-transparent"></div>
                    <span class="text-xs text-gray-400 font-medium">Memuat tanggapan admin...</span>
                </div>
                <!-- Empty State -->
                <div id="listEmptyState" class="hidden flex flex-col items-center justify-center py-10 text-center gap-2">
                    <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center border border-gray-100 text-gray-300">
                        <i class="bi bi-chat-left-text-fill text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-700">Belum Ada Laporan</p>
                    <p class="text-2xs text-gray-400 max-w-[200px] leading-relaxed">Laporan posyandu yang dikirim oleh masyarakat akan tampil di sini beserta balasan admin.</p>
                </div>
                <!-- Comments list placeholder -->
                <div id="reportsListContent" class="flex flex-col gap-4"></div>
            </div>
            <div id="reportDeleteUndoToast" class="hidden mt-3 rounded-xl bg-gray-900 px-3 py-2 text-xs text-white shadow-lg flex items-center justify-between gap-3">
                <span>Laporan dihapus</span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="undoReportDeletion()" class="font-bold text-purple-300 hover:text-purple-200 transition-colors">Undo</button>
                    <button type="button" onclick="dismissReportDeletionUndo()" class="text-gray-300 hover:text-white transition-colors" aria-label="Tutup notifikasi">
                        <i class="bi bi-x-lg text-[10px]"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
