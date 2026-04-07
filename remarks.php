<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);

include 'config/db.php';
include 'auth/require_login.php';
include 'partials/header.php';
include 'partials/sidebar.php';

$pdo = qa_db();
?>

<div class="content">
    <style>
    #remarksTable th,
    #remarksTable td {
        text-align: center;
        vertical-align: middle;
    }
    .clickable-row {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .clickable-row:hover {
        background-color: #f1f1f1;
    }
    </style>

    <div class="container-fluid">

        <!-- HEADER -->
        <div class="row mb-3">
            <div class="col d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0">Remarks</h4>
                <button class="btn btn-outline-primary" id="refreshRemarks">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
        </div>

        <!-- TABLE -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row g-2">

                    <div class="col-md-2">
                        <label class="form-label">Username</label>
                        <input type="text" id="filterUsername" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Program</label>
                        <input type="text" id="filterProgram" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Remark Name</label>
                        <input type="text" id="filterRemarkName" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Resolved</label>
                        <select id="filterResolved" class="form-select">
                            <option value="">All</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" id="filterFrom" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" id="filterTo" class="form-control">
                    </div>

                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="remarksTable" class="table table-striped table-hover align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Program</th>
                                <th>Session</th>
                                <th>Iteration</th>
                                <th>Remark Name</th>
                                <th>Resolved</th>
                                <th>Username</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- JS -->
<script src="assets/js/jquery-4.0.0.min.js"></script>
<script src="assets/js/datatables.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    const table = $('#remarksTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: 'functions/remarks_server.php',
            type: 'POST',
            data: function(d) {
                d.username    = $('#filterUsername').val();
                d.program     = $('#filterProgram').val();
                d.remark_name = $('#filterRemarkName').val();
                d.resolved    = $('#filterResolved').val();
                d.from_date   = $('#filterFrom').val();
                d.to_date     = $('#filterTo').val();
            }
        },
        pageLength: 25,
        searching: false,
        ordering: false
    });

    // Refresh button
    $('#refreshRemarks').on('click', function() {
        table.ajax.reload();
    });

    // Reload on filter change
    $('#filterUsername, #filterProgram, #filterRemarkName, #filterResolved, #filterFrom, #filterTo')
        .on('change keyup', function() {
            table.ajax.reload();
        });

    // 🖱 Row click to iteration view (role-based)
    $('#remarksTable tbody').on('click', 'tr', function() {
        const data = table.row(this).data();
        if (!data) return;

        const session   = data[1];   // Session
        const iteration = data[2];   // Iteration

        const roleViewerMap = {
            admin: 'viewers/admin_viewer.php',
            qa: 'viewers/qa_viewer.php',
            developer: 'viewers/dev_viewer.php'
        };

        const viewerPage = roleViewerMap[userRole];

        window.location.href =
            `${viewerPage}?session=${encodeURIComponent(session)}&iteration=${encodeURIComponent(iteration)}`;
    });
});
</script>

</body>
</html>