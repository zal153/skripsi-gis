@if ($errors->any())
    <script type="module">
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menyimpan Data!',
                html: `
                    <ul style="text-align: left; margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                `,
                confirmButtonText: 'Kembali',
                confirmButtonColor: '#d33',
            });
        });
    </script>
@endif

@if (session('error'))
    <script type="module">
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#d33',
            });
        });
    </script>
@endif

<script type="module">
    document.addEventListener('DOMContentLoaded', function() {
        // Event listener untuk tombol Hapus (.delete-btn)
        document.body.addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('.delete-btn');

            if (deleteBtn) {
                e.preventDefault();

                const formId = deleteBtn.getAttribute('data-form-id');
                const form = document.getElementById('delete-form-' + formId);

                if (form) {
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            }
        });
    });
</script>
