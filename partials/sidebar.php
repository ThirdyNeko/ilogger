<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../auth/require_login.php'; // Make sure BASE_URL is defined

$current_page = basename($_SERVER['SCRIPT_NAME']); // safer across folders
?>

<div class="sidebar d-flex flex-column p-3 bg-dark">

    <!-- Logo -->
    <h5 class="text-white text-center mb-4 sidebar-text">BRANCH LOGGER</h5>

    <!-- Menu -->
    <ul class="nav nav-pills flex-column mb-3">

        <!-- Logs -->
        <li class="nav-item">
            <a href="<?= BASE_URL ?>index.php"
               class="nav-link d-flex align-items-center gap-2 text-light <?= in_array($current_page, ['index.php','qa_viewer.php','admin_viewer.php','dev_viewer.php']) ? 'active' : '' ?>">
                <i class="bi bi-list-check"></i>
                <span class="sidebar-text">Logs</span>
            </a>
        </li>

        <!-- Remarks -->
        <li>
            <a href="<?= BASE_URL ?>remarks.php"
               class="nav-link d-flex align-items-center gap-2 text-light <?= $current_page == 'remarks.php' ? 'active' : '' ?>">
                <i class="bi bi-chat-left-text"></i>
                <span class="sidebar-text">Remarks</span>
            </a>
        </li>

        <!-- Change Password -->
        <li>
            <a href="<?= BASE_URL ?>change_password.php"
               class="nav-link d-flex align-items-center gap-2 text-light <?= $current_page == 'change_password.php' ? 'active' : '' ?>">
                <i class="bi bi-key"></i>
                <span class="sidebar-text">Change Password</span>
            </a>
        </li>

    </ul>

    <!-- Spacer -->
    <div class="flex-grow-1"></div>

    <!-- Bottom -->
    <div class="mt-auto pt-3 border-top border-secondary">

        <?php
        $roleLabels = [
            'admin' => 'ADMIN',
            'user'  => 'USER',
        ];
        $currentRole = $_SESSION['role'] ?? 'Guest';
        $roleDisplay = $roleLabels[$currentRole] ?? strtoupper($currentRole);
        ?>

        <div class="text-light small mb-2 d-flex align-items-center gap-2 justify-content-center justify-content-md-start">
            <i class="bi bi-person-circle"></i>
            <span class="sidebar-text"><?= htmlspecialchars($roleDisplay) ?></span>
        </div>

        <!-- Logout -->
        <a href="<?= BASE_URL ?>auth/logout.php"
           class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-box-arrow-right"></i>
            <span class="sidebar-text">Logout</span>
        </a>

    </div>

</div>