<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar d-flex flex-column p-3 bg-dark">

    <!-- Logo -->
    <h5 class="text-white text-center mb-4 sidebar-text">BRANCH LOGGER</h5>

    <!-- Menu -->
    <ul class="nav nav-pills flex-column mb-3">

        <!-- Logs -->
        <li class="nav-item">
            <a href="index.php"
               class="nav-link d-flex align-items-center gap-2 text-light <?= $current_page == 'index.php' ? 'active' : '' ?>">
                <i class="bi bi-list-check"></i>
                <span class="sidebar-text">Logs</span>
            </a>
        </li>

        <!-- Remarks -->
        <li>
            <a href="remarks.php"
               class="nav-link d-flex align-items-center gap-2 text-light <?= $current_page == 'remarks.php' ? 'active' : '' ?>">
                <i class="bi bi-chat-left-text"></i>
                <span class="sidebar-text">Remarks</span>
            </a>
        </li>

        <!-- Change Password -->
        <li>
            <a href="change_password.php"
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
        <a href="auth/logout.php"
           class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-box-arrow-right"></i>
            <span class="sidebar-text">Logout</span>
        </a>

    </div>

</div>