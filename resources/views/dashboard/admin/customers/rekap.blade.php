@php
    $modalId = 'rekap-customer-modal-' . $customer->id;
@endphp
<x-modal-custom :id="$modalId" title="Rekap Order Customer" size="2xl">
    <form method="GET" action="{{ route('admin.customers.rekap.download', $customer) }}" target="_blank" id="rekap-form-{{ $customer->id }}">
        <div class="mb-4">
            <label for="daterange-{{ $customer->id }}" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Pilih Rentang Tanggal</label>
            <input type="text" name="daterange" id="daterange-{{ $customer->id }}" class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200 dark:bg-gray-800 dark:text-white" autocomplete="off" required>
        </div>
        <div class="flex justify-end gap-2">
            <button type="submit" class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">Download</button>
        </div>
    </form>
    @push('page-scripts')
        <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                $('#daterange-{{ $customer->id }}').daterangepicker({
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
                $('#daterange-{{ $customer->id }}').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                });
                $('#daterange-{{ $customer->id }}').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                });
            });
        </script>
    @endpush
</x-modal-custom>
