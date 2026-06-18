<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK Programs & Events - Bacolod BIS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/style.css">
</head>

<body class="db-body">
    <?php
    $role      = 'sk';
    $active    = 'programs';
    $pageTitle = 'SK Programs & Events';
    include(APPPATH . 'Views/dashboard/sidebar.php');
    ?>
    <div class="db-main">
        <?php include(APPPATH . 'Views/dashboard/topbar.php'); ?>
        <div class="db-content">

            <!-- Stats -->
            <div class="db-stats" style="margin-bottom:24px;">
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(91,111,214,0.15);color:#5b6fd6;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">8</span>
                        <span class="db-stat-label">Total Programs</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(22,199,154,0.15);color:#16c79a;">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">4</span>
                        <span class="db-stat-label">Active</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(255,193,7,0.15);color:#e6a800;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">3</span>
                        <span class="db-stat-label">Upcoming</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(108,117,125,0.15);color:#6c757d;">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">1</span>
                        <span class="db-stat-label">Completed</span>
                    </div>
                </div>
            </div>

            <!-- Toolbar -->
            <div class="db-toolbar">
                <div class="db-search-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search programs..." id="searchInput" oninput="filterTable()">
                </div>
                <div class="db-toolbar-actions">
                    <select class="db-filter-select" id="categoryFilter" onchange="filterTable()">
                        <option value="">All Categories</option>
                        <option>Sports</option>
                        <option>Livelihood</option>
                        <option>Health</option>
                        <option>Education</option>
                        <option>Environment</option>
                        <option>Cultural</option>
                    </select>
                    <select class="db-filter-select" id="progStatusFilter" onchange="filterTable()">
                        <option value="">All Status</option>
                        <option>Active</option>
                        <option>Upcoming</option>
                        <option>Completed</option>
                    </select>
                    <button class="db-btn db-btn--primary" onclick="openModal('addProgramModal')">
                        <i class="fas fa-plus"></i> Add Program
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="db-table-wrap">
                <table class="db-table" id="programsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Program Name</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Venue</th>
                            <th>Target Participants</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $programs = [
                            ['001', 'Barangay Basketball League',       'Sports',      'Apr 5, 2026',  'Bacolod Basketball Court',    80,  'Active'],
                            ['002', 'Livelihood Skills Training',        'Livelihood',  'Apr 12, 2026', 'Barangay Hall, Bacolod',       40,  'Upcoming'],
                            ['003', 'Youth Health & Wellness Seminar',   'Health',      'Mar 28, 2026', 'Bacolod Health Center',        60,  'Active'],
                            ['004', 'Brigada Eskwela Volunteer Drive',   'Education',   'May 3, 2026',  'Bacolod Elementary School',    50,  'Upcoming'],
                            ['005', 'Coastal Clean-Up Drive',            'Environment', 'Mar 15, 2026', 'Bacolod Shoreline',            35,  'Completed'],
                        ];
                        $badgeMap = [
                            'Active'    => 'db-badge--approved',
                            'Upcoming'  => 'db-badge--pending',
                            'Completed' => 'db-badge--completed',
                        ];
                        foreach ($programs as $p): ?>
                            <tr>
                                <td><?= $p[0] ?></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <div class="prog-cat-icon prog-cat-<?= strtolower($p[2]) ?>">
                                            <?php
                                            $catIcons = [
                                                'Sports'      => 'fa-futbol',
                                                'Livelihood'  => 'fa-briefcase',
                                                'Health'      => 'fa-heartbeat',
                                                'Education'   => 'fa-graduation-cap',
                                                'Environment' => 'fa-leaf',
                                                'Cultural'    => 'fa-music',
                                            ];
                                            ?>
                                            <i class="fas <?= $catIcons[$p[2]] ?? 'fa-calendar' ?>"></i>
                                        </div>
                                        <span style="font-weight:500;"><?= $p[1] ?></span>
                                    </div>
                                </td>
                                <td><?= $p[2] ?></td>
                                <td><?= $p[3] ?></td>
                                <td><?= $p[4] ?></td>
                                <td><?= $p[5] ?></td>
                                <td>
                                    <span class="db-badge <?= $badgeMap[$p[6]] ?>">
                                        <?= $p[6] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="db-action-group">
                                        <button class="db-action-btn" title="View"><i class="fas fa-eye"></i> View</button>
                                        <button class="db-icon-btn" title="Edit"><i class="fas fa-edit"></i></button>
                                        <button class="db-icon-btn" style="color:#dc3545;" title="Delete"
                                            onclick="confirmDelete('<?= $p[1] ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div><!-- /.db-content -->
    </div><!-- /.db-main -->

    <!-- ===================== ADD PROGRAM MODAL ===================== -->
    <div class="db-modal-overlay" id="addProgramModal" onclick="overlayClose(event,'addProgramModal')">
        <div class="db-modal" style="max-width:620px;width:96%;">
            <div class="db-modal-header">
                <h3><i class="fas fa-calendar-plus" style="color:#5b6fd6;margin-right:8px;"></i>Add Program / Event</h3>
                <button class="db-modal-close" onclick="closeModal('addProgramModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="db-modal-body">
                <form id="addProgramForm">

                    <!-- Program Name -->
                    <div style="margin-bottom:16px;">
                        <label class="sk-form-label">Program Name <span style="color:#dc3545;">*</span></label>
                        <input type="text" class="sk-form-input" placeholder="e.g. Youth Leadership Summit" required>
                    </div>

                    <!-- Category -->
                    <div style="margin-bottom:16px;">
                        <label class="sk-form-label">Category <span style="color:#dc3545;">*</span></label>
                        <select class="sk-form-input" required>
                            <option value="">Select Category</option>
                            <option>Sports</option>
                            <option>Livelihood</option>
                            <option>Health</option>
                            <option>Education</option>
                            <option>Environment</option>
                            <option>Cultural</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div style="margin-bottom:16px;">
                        <label class="sk-form-label">Description</label>
                        <textarea class="sk-form-input" rows="3" placeholder="Brief description of the program or event..." style="resize:vertical;"></textarea>
                    </div>

                    <!-- Date / End Date -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                        <div>
                            <label class="sk-form-label">Start Date <span style="color:#dc3545;">*</span></label>
                            <input type="date" class="sk-form-input" required>
                        </div>
                        <div>
                            <label class="sk-form-label">End Date</label>
                            <input type="date" class="sk-form-input">
                        </div>
                    </div>

                    <!-- Venue -->
                    <div style="margin-bottom:16px;">
                        <label class="sk-form-label">Venue <span style="color:#dc3545;">*</span></label>
                        <input type="text" class="sk-form-input" placeholder="e.g. Barangay Hall, Bacolod" required>
                    </div>

                    <!-- Target Participants / Budget -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:8px;">
                        <div>
                            <label class="sk-form-label">Target Participants</label>
                            <input type="number" class="sk-form-input" placeholder="e.g. 50" min="1">
                        </div>
                        <div>
                            <label class="sk-form-label">Budget (₱)</label>
                            <input type="number" class="sk-form-input" placeholder="e.g. 5000" min="0">
                        </div>
                    </div>

                </form>
            </div>
            <div class="db-modal-footer">
                <button class="db-btn db-btn--outline" onclick="closeModal('addProgramModal')">Cancel</button>
                <button class="db-btn db-btn--primary">
                    <i class="fas fa-save"></i> Save Program
                </button>
            </div>
        </div>
    </div>

    <style>
        .sk-form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #444;
            margin-bottom: 6px;
        }

        .sk-form-input {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #dde3f0;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            color: #1d2448;
            background: #fff;
            box-sizing: border-box;
            transition: border-color .2s;
        }

        .sk-form-input:focus {
            outline: none;
            border-color: #5b6fd6;
            box-shadow: 0 0 0 3px rgba(91, 111, 214, .12);
        }

        .db-action-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Category icon chips */
        .prog-cat-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }

        .prog-cat-sports {
            background: rgba(91, 111, 214, .12);
            color: #5b6fd6;
        }

        .prog-cat-livelihood {
            background: rgba(255, 193, 7, .12);
            color: #e6a800;
        }

        .prog-cat-health {
            background: rgba(220, 53, 69, .12);
            color: #dc3545;
        }

        .prog-cat-education {
            background: rgba(22, 199, 154, .12);
            color: #16c79a;
        }

        .prog-cat-environment {
            background: rgba(40, 167, 69, .12);
            color: #28a745;
        }

        .prog-cat-cultural {
            background: rgba(111, 66, 193, .12);
            color: #6f42c1;
        }

        /* Completed badge override */
        .db-badge--completed {
            background: rgba(108, 117, 125, .12);
            color: #6c757d;
            font-size: 12px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
        }
    </style>

    <script>
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function overlayClose(e, id) {
            if (e.target === document.getElementById(id)) closeModal(id);
        }

        function filterTable() {
            const q = document.getElementById('searchInput').value.toLowerCase();
            const cat = document.getElementById('categoryFilter').value.toLowerCase();
            const status = document.getElementById('progStatusFilter').value.toLowerCase();

            document.querySelectorAll('#programsTable tbody tr').forEach(row => {
                const text = row.textContent.toLowerCase();
                const catVal = row.querySelectorAll('td')[2]?.textContent.toLowerCase() ?? '';
                const sVal = row.querySelectorAll('td')[6]?.textContent.toLowerCase() ?? '';

                const qMatch = text.includes(q);
                const catMatch = cat === '' || catVal.includes(cat);
                const sMatch = status === '' || sVal.includes(status);

                row.style.display = (qMatch && catMatch && sMatch) ? '' : 'none';
            });
        }

        function confirmDelete(name) {
            if (confirm('Delete program "' + name + '"? This cannot be undone.')) {
                alert('Program deleted (demo only).');
            }
        }

        document.querySelectorAll('.db-nav-item').forEach(item => {
            item.addEventListener('click', () => document.getElementById('sidebar').classList.remove('open'));
        });
    </script>

</body>

</html>