@props([
    'id' => 'myTable',
    'headers' => [],
])

<table class="table table-bordered" id="{{ $id }}">
    <thead>
        <tr>
            @foreach ($headers as $header)
                <th class="text-start">{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        {{ $slot }}
    </tbody>
</table>

@push('styles')
    <style>
        #{{ $id }} thead th,
        #{{ $id }} tbody td {
            text-align: left !important;
        }

        #{{ $id }} tbody td.datatable-empty,
        #{{ $id }} tbody td[colspan] {
            text-align: center !important;
        }
    </style>
@endpush

@push('scripts')
    <script type="module">
        document.addEventListener('DOMContentLoaded', function() {
            const tableElement = document.querySelector('#{{ $id }}');

            if (!tableElement || !window.SimpleDataTable) {
                return;
            }

            new window.SimpleDataTable(tableElement, {
                labels: {
                    placeholder: 'Cari...',
                    perPage: 'baris per halaman',
                    noRows: 'Tidak ada data tersedia pada tabel ini',
                    info: 'Menampilkan {start} sampai {end} dari {rows} baris',
                },
            });
        });
    </script>
@endpush
