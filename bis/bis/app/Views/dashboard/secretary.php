<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secretary Dashboard - Bacolod BIS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/style.css">
</head>

<body class="db-body">
    <?php
    $role      = 'secretary';
    $active    = 'dashboard';
    $pageTitle = 'Secretary Dashboard';
    include(APPPATH . 'Views/dashboard/sidebar.php');
    ?>
    <div class="db-main">
        <?php include(APPPATH . 'Views/dashboard/topbar.php'); ?>
        <div class="db-content">

            <div class="db-welcome">
                <div>
                    <h2>Welcome back, <?= esc(session()->get('username') ?? 'Secretary') ?> 👋</h2>
                    <p>Barangay Bacolod, Bato, Camarines Sur — Barangay Information System</p>
                </div>
                <div class="db-welcome-icon"><i class="fas fa-user-edit"></i></div>
            </div>

            <div class="db-stats">
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(91,111,214,0.15);color:#5b6fd6;">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">18</span>
                        <span class="db-stat-label">New Requests</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(22,199,154,0.15);color:#16c79a;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">142</span>
                        <span class="db-stat-label">Processed Today</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(255,193,7,0.15);color:#ffc107;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">1,248</span>
                        <span class="db-stat-label">Registered Residents</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(220,53,69,0.15);color:#dc3545;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">5</span>
                        <span class="db-stat-label">Overdue Requests</span>
                    </div>
                </div>
            </div>

            <h3 class="db-section-title">Modules</h3>
            <div class="db-modules">
                <a href="/secretary/census" class="db-module-card">
                    <div class="db-module-icon"><i class="fas fa-users"></i></div>
                    <h4>Census Records</h4>
                    <p>Encode and update resident and household census data.</p>
                </a>
                <a href="/secretary/clearance" class="db-module-card">
                    <div class="db-module-icon"><i class="fas fa-file-alt"></i></div>
                    <h4>Clearance Processing</h4>
                    <p>Process and prepare barangay clearance documents.</p>
                </a>
                <a href="/secretary/requests" class="db-module-card">
                    <div class="db-module-icon"><i class="fas fa-inbox"></i></div>
                    <h4>Document Requests</h4>
                    <p>Handle incoming document requests from residents.</p>
                </a>
                <a href="/secretary/chatbot" class="db-module-card">
                    <div class="db-module-icon"><i class="fas fa-robot"></i></div>
                    <h4>Chatbot Logs</h4>
                    <p>Review chatbot conversations and unanswered queries.</p>
                </a>
            </div>

            <h3 class="db-section-title">Pending Document Requests</h3>
            <div class="db-table-wrap">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Resident</th>
                            <th>Document</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>001</td>
                            <td>Ana Gomez</td>
                            <td>Barangay Clearance</td>
                            <td>Mar 17, 2026</td>
                            <td><span class="db-badge db-badge--pending">Pending</span></td>
                            <td><a href="#" class="db-action-btn">Process</a></td>
                        </tr>
                        <tr>
                            <td>002</td>
                            <td>Carlos Lim</td>
                            <td>Certificate of Residency</td>
                            <td>Mar 17, 2026</td>
                            <td><span class="db-badge db-badge--pending">Pending</span></td>
                            <td><a href="#" class="db-action-btn">Process</a></td>
                        </tr>
                        <tr>
                            <td>003</td>
                            <td>Rosa Bautista</td>
                            <td>Certificate of Indigency</td>
                            <td>Mar 16, 2026</td>
                            <td><span class="db-badge db-badge--approved">Done</span></td>
                            <td><a href="#" class="db-action-btn">View</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <script>
        document.querySelectorAll('.db-nav-item').forEach(item => {
            item.addEventListener('click', () => document.getElementById('sidebar').classList.remove('open'));
        });
    </script>
</body>

</html>