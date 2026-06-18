<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK Dashboard - Bacolod BIS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/style.css">
</head>

<body class="db-body">
    <?php
    $role      = 'sk';
    $active    = 'dashboard';
    $pageTitle = 'SK Dashboard';
    include(APPPATH . 'Views/dashboard/sidebar.php');
    ?>
    <div class="db-main">
        <?php include(APPPATH . 'Views/dashboard/topbar.php'); ?>
        <div class="db-content">

            <!-- Welcome Banner -->
            <div class="db-welcome">
                <div>
                    <h2>Welcome back, <?= esc(session()->get('username') ?? 'SK Official') ?> 👋</h2>
                    <p>Sangguniang Kabataan — Barangay Bacolod, Bato, Camarines Sur</p>
                </div>
                <div class="db-welcome-icon"><i class="fas fa-star"></i></div>
            </div>

            <!-- Stats -->
            <div class="db-stats">
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(91,111,214,0.15);color:#5b6fd6;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">548</span>
                        <span class="db-stat-label">Registered Youth (15–30)</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(22,199,154,0.15);color:#16c79a;">
                        <i class="fas fa-male"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">274</span>
                        <span class="db-stat-label">Male Youth</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(220,53,69,0.15);color:#dc3545;">
                        <i class="fas fa-female"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">274</span>
                        <span class="db-stat-label">Female Youth</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(255,193,7,0.15);color:#ffc107;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">28</span>
                        <span class="db-stat-label">Out-of-School Youth</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(91,111,214,0.15);color:#5b6fd6;">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">112</span>
                        <span class="db-stat-label">Employed Youth</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(22,199,154,0.15);color:#16c79a;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">4</span>
                        <span class="db-stat-label">Active Programs</span>
                    </div>
                </div>
            </div>

            <!-- Modules -->
            <h3 class="db-section-title">Modules</h3>
            <div class="db-modules">
                <a href="/sk/profiling" class="db-module-card">
                    <div class="db-module-icon"><i class="fas fa-id-card"></i></div>
                    <h4>SK Profiling</h4>
                    <p>Manage and view the list of youth residents aged 15–30 years old.</p>
                </a>
                <a href="/sk/programs" class="db-module-card">
                    <div class="db-module-icon"><i class="fas fa-calendar-alt"></i></div>
                    <h4>Programs & Events</h4>
                    <p>Create and track SK programs, activities, and community events.</p>
                </a>
                <a href="/sk/reports" class="db-module-card">
                    <div class="db-module-icon"><i class="fas fa-chart-bar"></i></div>
                    <h4>Reports</h4>
                    <p>Generate youth demographic reports and program participation data.</p>
                </a>
                <a href="/sk/settings" class="db-module-card">
                    <div class="db-module-icon"><i class="fas fa-cog"></i></div>
                    <h4>Settings</h4>
                    <p>Manage SK account settings and system preferences.</p>
                </a>
            </div>

            <!-- Recent Youth Registrations -->
            <h3 class="db-section-title">Recently Added Youth Profiles</h3>
            <div class="db-table-wrap">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Status</th>
                            <th>Date Added</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>001</td>
                            <td>Carlo Dela Cruz</td>
                            <td>20</td>
                            <td>Male</td>
                            <td><span class="db-badge db-badge--approved">Student</span></td>
                            <td>Apr 20, 2026</td>
                            <td><a href="/sk/profiling" class="db-action-btn">View</a></td>
                        </tr>
                        <tr>
                            <td>002</td>
                            <td>Ana Santos</td>
                            <td>17</td>
                            <td>Female</td>
                            <td><span class="db-badge db-badge--approved">Student</span></td>
                            <td>Apr 19, 2026</td>
                            <td><a href="/sk/profiling" class="db-action-btn">View</a></td>
                        </tr>
                        <tr>
                            <td>003</td>
                            <td>Mark Reyes</td>
                            <td>25</td>
                            <td>Male</td>
                            <td><span class="db-badge db-badge--pending">Out-of-School</span></td>
                            <td>Apr 18, 2026</td>
                            <td><a href="/sk/profiling" class="db-action-btn">View</a></td>
                        </tr>
                        <tr>
                            <td>004</td>
                            <td>Liza Bautista</td>
                            <td>22</td>
                            <td>Female</td>
                            <td><span class="db-badge db-badge--approved">Employed</span></td>
                            <td>Apr 17, 2026</td>
                            <td><a href="/sk/profiling" class="db-action-btn">View</a></td>
                        </tr>
                        <tr>
                            <td>005</td>
                            <td>Jose Manalo</td>
                            <td>15</td>
                            <td>Male</td>
                            <td><span class="db-badge db-badge--approved">Student</span></td>
                            <td>Apr 16, 2026</td>
                            <td><a href="/sk/profiling" class="db-action-btn">View</a></td>
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