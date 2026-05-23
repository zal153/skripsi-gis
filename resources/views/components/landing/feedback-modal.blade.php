<!-- MODAL Kritik & Saran -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal-box">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-800">Kritik & Saran</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M18 6L6 18M6 6l12 12" stroke-width="2" stroke-linecap="round" />
                </svg>
            </button>
        </div>
        <textarea
            class="w-full border border-gray-200 rounded-xl p-3 text-sm text-gray-700 resize-none focus:outline-none focus:ring-2 focus:ring-blue-400 h-28"
            placeholder="Tulis kritik atau saran Anda di sini..."></textarea>
        <button class="btn-primary w-full mt-3 justify-center" onclick="closeModal()">Kirim</button>
    </div>
</div>
