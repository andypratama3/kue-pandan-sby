/**
 * Initializes a live search component on a page.
 *
 * @param {object} options - The configuration options.
 * @param {string} options.searchInputId - The ID of the search input element.
 * @param {string} options.desktopContainerId - The ID of the container where desktop results will be rendered.
 * @param {string} [options.mobileContainerId] - The ID of the container where mobile results will be rendered.
 * @param {number} [options.debounceTime=300] - Time in ms to wait after user stops typing.
 */
window.initializeLiveSearch = function (options) {
    const searchInput = document.getElementById(options.searchInputId);
    const desktopContainer = document.getElementById(options.desktopContainerId);
    const mobileContainer = document.getElementById(options.mobileContainerId);

    if (!searchInput || (!desktopContainer && !mobileContainer)) {
        console.error("LiveSearch Error: Search input or at least one result container not found.");
        return;
    }

    const loadingHtml = `
        <tr>
            <td colspan="100%" class="text-center p-4">
                <div class="flex justify-center items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Mencari...</span>
                </div>
            </td>
        </tr>`;

    let debounceTimer;
    const debounceTime = options.debounceTime || 300;

    searchInput.addEventListener("input", (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            performSearch(e.target.value);
        }, debounceTime);
    });

    async function performSearch(query) {
        if (desktopContainer) desktopContainer.innerHTML = loadingHtml;
        if (mobileContainer) mobileContainer.innerHTML = `<div class="text-center p-4">${loadingHtml}</div>`;

        const url = new URL(window.location.href);
        
        url.searchParams.set("search", query);
        url.searchParams.set("page", 1); // Selalu reset ke halaman 1 saat pencarian baru

        try {
            const response = await fetch(url.toString(), {
                method: "GET",
                headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "application/json" },
            });

            if (!response.ok) throw new Error("Network response was not ok");

            const data = await response.json();


            if (desktopContainer && data.desktop_html) {
                desktopContainer.innerHTML = data.desktop_html;
            }
            if (mobileContainer && data.mobile_html) {
                mobileContainer.innerHTML = data.mobile_html;
            }
            // Update modal customer/kurir jika ada
            // Untuk halaman kurir, gunakan id 'courier-modals-container'
            let modalsContainer = document.getElementById('customer-modals-container');
            if (!modalsContainer) {
                modalsContainer = document.getElementById('courier-modals-container');
            }
            // if (modalsContainer && data.modals_html) {
            //     modalsContainer.innerHTML = data.modals_html;
            // }

            if (modalsContainer && data.modals_html) {
                // 1. Suntikkan HTML modal baru
                modalsContainer.innerHTML = data.modals_html;

                // 2. TEMUKAN SEMUA input daterange DI DALAM MODAL BARU
                const newDateRangePickers = modalsContainer.querySelectorAll('input[name="daterange"]');

                // 3. INISIALISASI SETIAP input daterange SATU PER SATU
                // (Ini adalah logika yang disalin dari rekap.blade.php)
                newDateRangePickers.forEach(pickerElement => {
                    const $picker = $(pickerElement); // Ubah ke objek jQuery

                    $picker.daterangepicker({
                        locale: {
                            format: 'YYYY-MM-DD',
                            separator: ' - ',
                            applyLabel: 'Pilih',
                            cancelLabel: 'Batal',
                            daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                            firstDay: 1
                        },
                        opens: 'center',
                        autoUpdateInput: false
                    });

                    $picker.on('apply.daterangepicker', function(ev, picker) {
                        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                    });

                    $picker.on('cancel.daterangepicker', function(ev, picker) {
                        $(this).val('');
                    });
                });
            }

            // Perbarui URL di browser tanpa me-reload halaman
            history.pushState({}, "", url.toString());

        } catch (error) {
            console.error("LiveSearch Fetch Error:", error);
            const errorHtml = `<tr><td colspan="100%" class="text-center text-red-500 p-4">Gagal memuat data.</td></tr>`;
            if (desktopContainer) desktopContainer.innerHTML = errorHtml;
            if (mobileContainer) mobileContainer.innerHTML = `<div class="text-center p-4 text-red-500">Gagal memuat data.</div>`;
        }
    }
};
