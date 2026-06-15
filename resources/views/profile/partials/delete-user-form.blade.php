<section>
    <header class="mb-3">
        <p class="text-sm text-muted">
            {{ __('Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Sebelum menghapus akun Anda, silakan unduh data atau informasi apa pun yang ingin Anda simpan.') }}
        </p>
    </header>

    <!-- Trigger Button -->
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        {{ __('Hapus Akun') }}
    </button>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="{{ route('profile.destroy') }}" class="modal-content">
                @csrf
                @method('delete')

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-semibold text-dark" id="confirmUserDeletionModalLabel">
                        {{ __('Apakah Anda yakin ingin menghapus akun Anda?') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p class="text-sm text-muted mb-3">
                        {{ __('Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Silakan masukkan password Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun Anda secara permanen.') }}
                    </p>

                    <div class="mb-3">
                        <label for="delete_password" class="form-label sr-only">{{ __('Password') }}</label>
                        <input type="password" name="password" id="delete_password"
                            class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                            placeholder="{{ __('Password') }}" required>
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Hapus Akun') }}</button>
                </div>
            </form>
        </div>
    </div>

    @if ($errors->userDeletion->isNotEmpty())
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const modalEl = document.getElementById('confirmUserDeletionModal');
                    if (modalEl) {
                        const modal = new bootstrap.Modal(modalEl);
                        modal.show();
                    }
                });
            </script>
        @endpush
    @endif
</section>
