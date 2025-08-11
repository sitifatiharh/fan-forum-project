document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk membuat fitur show/hide
    function setupPasswordToggle(toggleId, inputId) {
        const toggle = document.getElementById(toggleId);
        const input = document.getElementById(inputId);

        if (toggle && input) {
            toggle.addEventListener('click', function() {
                // Ganti tipe input
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                
                // Ganti ikon mata
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }
    }

    // Terapkan fungsi untuk input 'password'
    setupPasswordToggle('togglePassword', 'password');

    // Terapkan fungsi untuk input 'konfirmasi password'
    setupPasswordToggle('toggleConfirmPassword', 'confirm_password');
});