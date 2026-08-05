/**
 * Menginisialisasi fungsi UI kustom untuk modal dan dropdown.
 */
function initializeCustomUI() {
    // --- FUNGSI-FUNGSI UTAMA ---

    /**
     * Membuka modal berdasarkan ID.
     * @param {string} modalId - ID dari elemen modal.
     */
    const openModal = (modalId) => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    };

    /**
     * Menutup elemen modal terdekat.
     * @param {HTMLElement} modalElement - Elemen modal yang akan ditutup.
     */
    const closeModal = (modalElement) => {
        if (modalElement) {
            modalElement.classList.add('hidden');
            modalElement.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    };

    // Expose ke global scope agar bisa dipanggil dari blade/inline script
    window.openModal = openModal;
    window.closeModal = closeModal;

    /**
     * Membuka/menutup dropdown dan secara dinamis memposisikannya (ke atas atau ke bawah).
     * @param {string} dropdownId - ID dari menu dropdown.
     * @param {HTMLElement} buttonElement - Elemen tombol yang di-klik.
     */
    const toggleDropdown = (dropdownId, buttonElement) => {
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) {
            // Cek apakah dropdown ini sudah terbuka
            const isCurrentlyHidden = dropdown.classList.contains('hidden');

            // Tutup semua dropdown lain terlebih dahulu
            document.querySelectorAll('.js-dropdown-menu').forEach(otherDropdown => {
                if (otherDropdown.id !== dropdownId) {
                    otherDropdown.classList.add('hidden');
                    // Reset style dropdown lain ke posisi default (bawah)
                    otherDropdown.classList.remove('bottom-full', 'mb-2');
                    otherDropdown.classList.add('mt-2');
                }
            });

            // Hanya proses kalkulasi posisi jika kita akan MEMBUKA dropdown
            if (isCurrentlyHidden) {
                // --- PERBAIKAN DI SINI ---
                // 1. Hapus 'hidden' untuk sementara agar bisa diukur
                dropdown.classList.remove('hidden');
                // 2. Ambil tinggi yang sebenarnya
                const dropdownHeight = dropdown.offsetHeight;
                // 3. Kembalikan 'hidden' dengan cepat sebelum browser me-render
                dropdown.classList.add('hidden');
                // --- AKHIR PERBAIKAN ---

                // Ambil posisi tombol
                const btnRect = buttonElement.getBoundingClientRect();
                // Ambil tinggi layar
                const viewportHeight = window.innerHeight;

                // Cek apakah dropdown akan terpotong di bawah (SEKARANG DENGAN TINGGI YANG BENAR)
                if (btnRect.bottom + dropdownHeight + 10 > viewportHeight) { // 10px = buffer
                    // Ya, akan terpotong. Pindahkan ke atas.
                    dropdown.classList.remove('mt-2');
                    dropdown.classList.add('bottom-full', 'mb-2');
                } else {
                    // Tidak terpotong. Posisi normal di bawah.
                    dropdown.classList.remove('bottom-full', 'mb-2');
                    dropdown.classList.add('mt-2');
                }
            }

            // Akhirnya, buka/tutup dropdown yang ditargetkan
            dropdown.classList.toggle('hidden');
        }
    };

    // --- EVENT LISTENER UTAMA (EVENT DELEGATION) ---
    document.addEventListener('click', function (event) {
        const target = event.target;

        // --- Logika untuk Modal ---
        const openModalBtn = target.closest('.js-open-modal-btn');
        if (openModalBtn) {
            event.preventDefault();
            const modalId = openModalBtn.getAttribute('data-target-modal');
            openModal(modalId);
            return;
        }

        const closeModalBtn = target.closest('.js-close-modal-btn');
        if (closeModalBtn) {
            event.preventDefault();
            const modal = closeModalBtn.closest('[role="dialog"]');
            closeModal(modal);
            return;
        }

        const modalOverlay = target.closest('[role="dialog"]');
        if (modalOverlay && target === modalOverlay) {
            closeModal(modalOverlay);
            return;
        }

        // --- Logika untuk Dropdown ---
        const dropdownToggleBtn = target.closest('.js-dropdown-toggle');
        if (dropdownToggleBtn) {
            event.preventDefault();
            const dropdownId = dropdownToggleBtn.getAttribute('data-target-dropdown');

            // **PERUBAHAN UTAMA DI SINI**
            // Kirim elemen tombol (dropdownToggleBtn) ke fungsi toggleDropdown
            toggleDropdown(dropdownId, dropdownToggleBtn);
            return;
        }

        // Tutup semua dropdown jika klik di luar area dropdown
        if (!target.closest('.js-dropdown-toggle') && !target.closest('.js-dropdown-menu')) {
            document.querySelectorAll('.js-dropdown-menu').forEach(dropdown => {
                dropdown.classList.add('hidden');
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', initializeCustomUI);
