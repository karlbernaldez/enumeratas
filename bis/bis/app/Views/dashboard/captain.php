<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captain Dashboard - Bacolod BIS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/style.css">
</head>

<body class="db-body">
    <?php
    $role      = 'captain';
    $active    = 'dashboard';
    $pageTitle = 'Captain Dashboard';
    include(APPPATH . 'Views/dashboard/sidebar.php');
    ?>
    <div class="db-main">
        <?php include(APPPATH . 'Views/dashboard/topbar.php'); ?>
        <div class="db-content">

            <!-- Welcome Banner -->
            <div class="db-welcome">
                <div>
                    <h2>Welcome back, <?= esc(session()->get('username') ?? 'Captain') ?> 👋</h2>
                    <p>Barangay Bacolod, Bato, Camarines Sur — Barangay Information System</p>
                </div>
                <div class="db-welcome-icon"><i class="fas fa-user-tie"></i></div>
            </div>

            <!-- Stats -->
            <div class="db-stats">
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(91,111,214,0.15);color:#5b6fd6;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">1,248</span>
                        <span class="db-stat-label">Total Residents</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(22,199,154,0.15);color:#16c79a;">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">34</span>
                        <span class="db-stat-label">Pending Clearances</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(255,193,7,0.15);color:#ffc107;">
                        <i class="fas fa-home"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">312</span>
                        <span class="db-stat-label">Households</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(220,53,69,0.15);color:#dc3545;">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">7</span>
                        <span class="db-stat-label">Open Complaints</span>
                    </div>
                </div>
            </div>

            <!-- Modules -->
            <h3 class="db-section-title">Modules</h3>
            <div class="db-modules">
                <a href="/captain/census" class="db-module-card">
                    <div class="db-module-icon"><i class="fas fa-users"></i></div>
                    <h4>Census Records</h4>
                    <p>Manage resident profiles, household data, and population census.</p>
                </a>
                <a href="/captain/clearance" class="db-module-card">
                    <div class="db-module-icon"><i class="fas fa-file-alt"></i></div>
                    <h4>Clearance Management</h4>
                    <p>Review, approve, or reject barangay clearance requests.</p>
                </a>
                <a href="/captain/reports" class="db-module-card">
                    <div class="db-module-icon"><i class="fas fa-chart-bar"></i></div>
                    <h4>Reports & Analytics</h4>
                    <p>View population reports, clearance statistics, and trends.</p>
                </a>
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