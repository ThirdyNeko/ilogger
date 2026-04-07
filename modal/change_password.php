<?php
$firstLogin = $_SESSION['first_login'] ?? 0; // 1 if first login, 0 otherwise
echo ($_SESSION['first_login']); // should output 1
?>
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <form id="changePasswordForm">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div id="passwordAlert"></div> <!-- For success/error messages -->

                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                        <span class="input-group-text toggle-password" data-target="current_password" style="cursor:pointer;">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <span class="input-group-text toggle-password" data-target="new_password" style="cursor:pointer;">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        <span class="input-group-text toggle-password" data-target="confirm_password" style="cursor:pointer;">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Change Password</button>
            </div>
        </form>
    </div>
  </div>
</div>

<script src="<?= BASE_URL ?>sweetalert/dist/sweetalert2.all.min.js"></script>
<script>
const BASE_URL = '<?= BASE_URL ?>';
let firstLogin = <?= (int)$firstLogin ?>;

document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('changePasswordModal');
    const form = document.getElementById('changePasswordForm');

    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(span => {
        span.addEventListener('click', () => {
            const target = document.getElementById(span.dataset.target);
            const icon = span.querySelector('i');
            if (target.type === 'password') {
                target.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                target.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    });

    if (firstLogin === 1) {
        // Initialize modal with static backdrop and no ESC
        const modalInstance = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });
        modalInstance.show();

        // Function to prevent closing and show alert
        function preventClose(e) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Action Required!',
                text: 'You must change your password before continuing.',
                confirmButtonText: 'OK'
            });
        }

        // Prevent closing the modal until password is changed
        modalEl.addEventListener('hide.bs.modal', preventClose);
        modalEl.querySelectorAll('.btn-close').forEach(btn => btn.addEventListener('click', preventClose));

        // Handle password change
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;

            fetch(`${BASE_URL}functions/change_password.php`, { method: 'POST', body: new FormData(form) })
                .then(res => res.json())
                .then(data => {
                    Swal.fire({
                        icon: data.status === 'success' ? 'success' : 'error',
                        title: data.status === 'success' ? 'Password Changed!' : 'Oops...',
                        text: data.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        if (data.status === 'success') {
                            form.reset();
                            firstLogin = 0;

                            // Remove preventClose listeners
                            modalEl.removeEventListener('hide.bs.modal', preventClose);
                            modalEl.querySelectorAll('.btn-close').forEach(btn => btn.removeEventListener('click', preventClose));

                            // Close modal
                            modalInstance.hide();
                        }
                    });
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({ icon: 'error', title: 'Error!', text: 'Something went wrong. Please try again.', confirmButtonText: 'OK' });
                })
                .finally(() => submitBtn.disabled = false);
        });
    }
});
</script>