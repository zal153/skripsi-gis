<x-app title="Desa">
    <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Desa</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Halaman Desa</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
            <div class="container-fluid">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Data Desa</h3>
                            <div class="ms-auto d-flex gap-2">
                                <button type="button" id="bulkDeleteBtn" class="btn btn-sm btn-danger d-none">
                                    <i class="bi bi-trash"></i> Hapus Terpilih
                                </button>
                                <a href="{{ route('desa.create') }}" class="btn btn-sm btn-primary">Tambah Data</a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <x-table id="desaTable" :headers="[
                                    '<input class=\'form-check-input\' type=\'checkbox\' id=\'selectAll\'>',
                                    'No',
                                    'Nama Desa',
                                    'Aksi'
                                ]">
                                    @foreach ($desa as $item)
                                        <tr>
                                            <td>
                                                <input class="form-check-input row-checkbox" type="checkbox" value="{{ $item->id }}">
                                            </td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->nama_desa }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('desa.edit', $item) }}"
                                                    class="btn btn-sm btn-primary"><i
                                                        class="bi bi-pencil-square"></i></a>
                                                <form action="{{ route('desa.destroy', $item) }}" method="POST"
                                                    style="display:inline;" id="delete-form-{{ $item->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                        data-form-id="{{ $item->id }}"><i
                                                            class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </x-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::App Content-->
    </main>

    <form id="bulkDeleteForm" action="{{ route('desa.bulk-destroy') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="ids" id="bulkDeleteIds">
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let selectedIds = [];
                const table = document.getElementById('desaTable');
                const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

                if (!table || !bulkDeleteBtn) return;

                // Listen to changes at the table level (event delegation)
                table.addEventListener('change', function(e) {
                    // Row Checkbox change
                    if (e.target.classList.contains('row-checkbox')) {
                        const id = e.target.value;
                        if (e.target.checked) {
                            if (!selectedIds.includes(id)) {
                                selectedIds.push(id);
                            }
                        } else {
                            const index = selectedIds.indexOf(id);
                            if (index > -1) {
                                selectedIds.splice(index, 1);
                            }
                        }
                        updateBulkDeleteButton();
                    }

                    // Select All Checkbox change
                    if (e.target.id === 'selectAll') {
                        const checkboxes = table.querySelectorAll('.row-checkbox');
                        checkboxes.forEach(cb => {
                            cb.checked = e.target.checked;
                            const id = cb.value;
                            if (e.target.checked) {
                                if (!selectedIds.includes(id)) {
                                    selectedIds.push(id);
                                }
                            } else {
                                const index = selectedIds.indexOf(id);
                                if (index > -1) {
                                    selectedIds.splice(index, 1);
                                }
                            }
                        });
                        updateBulkDeleteButton();
                    }
                });

                // Function to update the visibility of bulk delete button
                function updateBulkDeleteButton() {
                    if (selectedIds.length > 0) {
                        bulkDeleteBtn.classList.remove('d-none');
                    } else {
                        bulkDeleteBtn.classList.add('d-none');
                    }
                    syncSelectAllCheckbox();
                }

                // Sync the "Select All" checkbox state based on selected IDs
                function syncSelectAllCheckbox() {
                    const checkboxes = table.querySelectorAll('.row-checkbox');
                    const selectAll = document.getElementById('selectAll');
                    if (checkboxes.length === 0) {
                        if (selectAll) selectAll.checked = false;
                        return;
                    }
                    let allChecked = true;
                    checkboxes.forEach(cb => {
                        if (!selectedIds.includes(cb.value)) {
                            allChecked = false;
                        }
                    });
                    if (selectAll) selectAll.checked = allChecked;
                }

                // Function to sync checkboxes on table updates (sorting, search, page navigation)
                function syncCheckboxes() {
                    const checkboxes = table.querySelectorAll('.row-checkbox');
                    checkboxes.forEach(cb => {
                        cb.checked = selectedIds.includes(cb.value);
                    });
                    syncSelectAllCheckbox();
                }

                // Observe any child list additions/removals inside the entire table tree
                const observer = new MutationObserver(syncCheckboxes);
                observer.observe(table, { childList: true, subtree: true });

                // Handle bulk delete submission
                bulkDeleteBtn.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Semua data desa terpilih (" + selectedIds.length + " data) akan dihapus permanen beserta data posyandu di dalamnya!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('bulkDeleteIds').value = selectedIds.join(',');
                            document.getElementById('bulkDeleteForm').submit();
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app>
