<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK Reports - Bacolod BIS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/style.css">
</head>

<body class="db-body">
    <?php
    $role      = 'sk';
    $active    = 'reports';
    $pageTitle = 'SK Reports';
    include(APPPATH . 'Views/dashboard/sidebar.php');
    ?>
    <div class="db-main">
        <?php include(APPPATH . 'Views/dashboard/topbar.php'); ?>
        <div class="db-content">

            <!-- Top toolbar with export -->
            <div class="db-toolbar" style="margin-bottom:24px;">
                <div style="font-size:14px;color:#7a8aaa;">
                    <i class="fas fa-calendar" style="margin-right:6px;"></i>
                    Report Period: <strong style="color:#1d2448;">January – April 2026</strong>
                </div>
                <div class="db-toolbar-actions">
                    <select class="db-filter-select">
                        <option>2026</option>
                        <option>2025</option>
                        <option>2024</option>
                    </select>
                    <button class="db-btn db-btn--outline">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                    <button class="db-btn db-btn--primary">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="db-stats" style="margin-bottom:28px;">
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(91,111,214,0.15);color:#5b6fd6;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">548</span>
                        <span class="db-stat-label">Registered Youth</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(22,199,154,0.15);color:#16c79a;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">320</span>
                        <span class="db-stat-label">Students</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(255,193,7,0.15);color:#e6a800;">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">28</span>
                        <span class="db-stat-label">Out-of-School</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(91,111,214,0.15);color:#5b6fd6;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">8</span>
                        <span class="db-stat-label">Programs Conducted</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(22,199,154,0.15);color:#16c79a;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">312</span>
                        <span class="db-stat-label">Total Participants</span>
                    </div>
                </div>
            </div>

            <!-- Section 1: Youth Demographics -->
            <h3 class="db-section-title">
                <i class="fas fa-chart-pie" style="color:#5b6fd6;margin-right:8px;"></i>
                Youth Demographics
            </h3>

            <div class="db-table-wrap" style="margin-bottom:32px;">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th>Age Group</th>
                            <th>Total Count</th>
                            <th>Male</th>
                            <th>Female</th>
                            <th>Student</th>
                            <th>Employed</th>
                            <th>Unemployed</th>
                            <th>Out-of-School</th>
                            <th>% of Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $demographics = [
                            ['15–17 (Child Youth)',  148, 72,  76,  120, 8,  12, 8,  27.0],
                            ['18–24 (Core Youth)',   264, 132, 132, 168, 52, 30, 14, 48.2],
                            ['25–30 (Young Adult)',  136, 70,  66,  32,  68, 30, 6,  24.8],
                        ];
                        $totals = [548, 274, 274, 320, 128, 72, 28, 100];
                        foreach ($demographics as $d): ?>
                            <tr>
                                <td><strong><?= $d[0] ?></strong></td>
                                <td><?= $d[1] ?></td>
                                <td><?= $d[2] ?></td>
                                <td><?= $d[3] ?></td>
                                <td><?= $d[4] ?></td>
                                <td><?= $d[5] ?></td>
                                <td><?= $d[6] ?></td>
                                <td><?= $d[7] ?></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <div style="flex:1;background:#eaeef6;border-radius:4px;height:6px;min-width:60px;">
                                            <div style="width:<?= $d[8] ?>%;background:#5b6fd6;height:6px;border-radius:4px;"></div>
                                        </div>
                                        <span style="font-size:12px;color:#7a8aaa;white-space:nowrap;"><?= $d[8] ?>%</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background:#f5f7fb;font-weight:600;">
                            <td>Total</td>
                            <td><?= $totals[0] ?></td>
                            <td><?= $totals[1] ?></td>
                            <td><?= $totals[2] ?></td>
                            <td><?= $totals[3] ?></td>
                            <td><?= $totals[4] ?></td>
                            <td><?= $totals[5] ?></td>
                            <td><?= $totals[6] ?></td>
                            <td>100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Section 2: Program Participation -->
            <h3 class="db-section-title">
                <i class="fas fa-chart-bar" style="color:#5b6fd6;margin-right:8px;"></i>
                Program Participation
            </h3>

            <div class="db-table-wrap" style="margin-bottom:32px;">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Program Name</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Target</th>
                            <th>Actual Participants</th>
                            <th>Completion Rate</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $participation = [
                            ['001', 'Barangay Basketball League',      'Sports',      'Apr 5, 2026',  80,  76,  95,  'Active'],
                            ['002', 'Livelihood Skills Training',       'Livelihood',  'Apr 12, 2026', 40,  0,   0,   'Upcoming'],
                            ['003', 'Youth Health & Wellness Seminar',  'Health',      'Mar 28, 2026', 60,  58,  97,  'Active'],
                            ['004', 'Brigada Eskwela Volunteer Drive',  'Education',   'May 3, 2026',  50,  0,   0,   'Upcoming'],
                            ['005', 'Coastal Clean-Up Drive',           'Environment', 'Mar 15, 2026', 35,  35,  100, 'Completed'],
                        ];
                        $badgeMap = [
                            'Active'    => 'db-badge--approved',
                            'Upcoming'  => 'db-badge--pending',
                            'Completed' => 'db-badge--completed',
                        ];
                        foreach ($participation as $p): ?>
                            <tr>
                                <td><?= $p[0] ?></td>
                                <td><strong><?= $p[1] ?></strong></td>
                                <td><?= $p[2] ?></td>
                                <td><?= $p[3] ?></td>
                                <td><?= $p[4] ?></td>
                                <td><?= $p[5] > 0 ? $p[5] : '<span style="color:#bbb;">—</span>' ?></td>
                                <td>
                                    <?php if ($p[6] > 0): ?>
                                        <div style="display:flex;align-items:center;gap:8px;">
                                            <div style="flex:1;background:#eaeef6;border-radius:4px;height:6px;min-width:70px;">
                                                <div style="width:<?= $p[6] ?>%;background:<?= $p[6] >= 90 ? '#16c79a' : ($p[6] >= 70 ? '#ffc107' : '#dc3545') ?>;height:6px;border-radius:4px;"></div>
                                            </div>
                                            <span style="font-size:12px;color:#7a8aaa;white-space:nowrap;"><?= $p[6] ?>%</span>
                                        </div>
                                    <?php else: ?>
                                        <span style="color:#bbb;font-size:13px;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="db-badge <?= $badgeMap[$p[7]] ?>"><?= $p[7] ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div><!-- /.db-content -->
    </div><!-- /.db-main -->

    <style>
        .db-badge--completed {
            background: rgba(108, 117, 125, .12);
            color: #6c757d;
            font-size: 12px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
        }

        tfoot td {
            padding: 10px 14px;
            font-size: 13px;
            color: #1d2448;
        }
    </style>

    <script>
        document.querySelectorAll('.db-nav-item').forEach(item => {
            item.addEventListener('click', () => document.getElementById('sidebar').classList.remove('open'));
        });
    </script>

</body>

</html>