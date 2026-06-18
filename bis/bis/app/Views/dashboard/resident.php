<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Dashboard - Bacolod BIS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/style.css">
    <style>
        /* ── Service Cards ── */
        .svc-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        .svc-card {
            background: #fff;
            border: 1.5px solid #e8ecf4;
            border-radius: 16px;
            padding: 32px 24px 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
            cursor: default;
        }

        .svc-card:hover {
            border-color: #1d2448;
            box-shadow: 0 6px 24px rgba(29, 36, 72, 0.1);
            transform: translateY(-2px);
        }

        .svc-card.active-card {
            border-color: #1d2448;
            box-shadow: 0 4px 20px rgba(29, 36, 72, 0.12);
        }

        .svc-icon-wrap {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #1d2448;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 18px;
            flex-shrink: 0;
        }

        .svc-card h4 {
            font-size: 15px;
            font-weight: 700;
            color: #1a1d2e;
            margin: 0 0 10px;
        }

        .svc-card p {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.6;
            margin: 0 0 20px;
            flex: 1;
        }

        .svc-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 20px;
            border: 1.5px solid #1d2448;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #1d2448;
            background: #fff;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            font-family: 'Poppins', sans-serif;
        }

        .svc-btn:hover {
            background: #1d2448;
            color: #fff;
        }

        .svc-btn i {
            font-size: 12px;
        }

        /* Recent requests table section */
        .res-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        @media (max-width: 900px) {
            .svc-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (min-width: 600px) and (max-width: 900px) {
            .svc-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

<body class="db-body">
    <?php
    $role      = 'resident';
    $active    = 'dashboard';
    $pageTitle = 'Resident Dashboard';
    include(APPPATH . 'Views/dashboard/sidebar.php');
    ?>
    <div class="db-main">
        <?php include(APPPATH . 'Views/dashboard/topbar.php'); ?>
        <div class="db-content">

            <!-- Welcome -->
            <div class="db-welcome">

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="db-alert db-alert--success" style="margin-bottom:16px;">
                        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('blotter_error')): ?>
                    <div class="db-alert db-alert--error" style="margin-bottom:16px;">
                        <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('blotter_error') ?>
                    </div>
                <?php endif; ?>
                <div>
                    <h2>Hello, <?= esc(session()->get('full_name') ?? session()->get('username') ?? 'Resident') ?> 👋</h2>
                    <p>Barangay Bacolod, Bato, Camarines Sur — Barangay Information System</p>
                </div>
                <div class="db-welcome-icon"><i class="fas fa-users"></i></div>
            </div>

            <!-- Stats -->
            <div class="db-stats" style="margin-bottom:32px;">
                <?php
                $totalRequests  = $totalRequests  ?? 0;
                $approved       = $approved       ?? 0;
                $pending        = $pending        ?? 0;
                $blotterCount   = $blotterCount   ?? 0;
                $recentRequests = $recentRequests ?? [];
                ?>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(91,111,214,0.15);color:#5b6fd6;">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <span class="db-stat-num"><?= $totalRequests ?></span>
                        <span class="db-stat-label">My Requests</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(22,199,154,0.15);color:#16c79a;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <span class="db-stat-num"><?= $approved ?></span>
                        <span class="db-stat-label">Approved</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(255,193,7,0.15);color:#ffc107;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <span class="db-stat-num"><?= $pending ?></span>
                        <span class="db-stat-label">Pending</span>
                    </div>
                </div>
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(220,53,69,0.15);color:#dc3545;">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div>
                        <span class="db-stat-num"><?= $blotterCount ?></span>
                        <span class="db-stat-label">Blotter Cases</span>
                    </div>
                </div>
            </div>

            <!-- Barangay Services -->
            <h3 class="db-section-title">Barangay Services</h3>
            <div class="svc-grid">

                <!-- Barangay Clearances -->
                <div class="svc-card active-card">
                    <div class="svc-icon-wrap">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h4>Barangay Clearances</h4>
                    <p>Issuance of barangay clearance, certificate of indigency, or certificate of residency for residents' official transactions.</p>
                    <a href="/resident/clearance" class="svc-btn">
                        Request Now <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <!-- Blotter / File Concern -->
                <div class="svc-card">
                    <div class="svc-icon-wrap" style="background:#c0392b;">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <h4>Blotters</h4>
                    <p>Recording of incidents, complaints, or community disputes reported to the barangay for documentation and resolution.</p>
                    <button class="svc-btn" onclick="openModal('blotterModal')">
                        File Report <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

                <!-- Raise a Concern -->
                <div class="svc-card">
                    <div class="svc-icon-wrap" style="background:#16a085;">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h4>Raise a Concern</h4>
                    <p>Submit feedback, suggestions, or concerns directly to the barangay officials for prompt attention and action.</p>
                    <button class="svc-btn" onclick="openModal('concernModal')">
                        Submit <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

            </div>

            <!-- Recent Requests -->
            <div class="res-section-header">
                <h3 class="db-section-title" style="margin:0;">My Recent Requests</h3>
                <a href="/resident/clearance" class="db-btn db-btn--outline db-btn--sm">View All</a>
            </div>
            <div class="db-table-wrap">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Document</th>
                            <th>Purpose</th>
                            <th>Date Filed</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentRequests)): ?>
                            <tr>
                                <td colspan="5" style="text-align:center;padding:28px;color:#9aa0b4;">
                                    <i class="fas fa-file-alt" style="font-size:22px;display:block;margin-bottom:8px;color:#d0d5e8;"></i>
                                    No requests yet. <a href="/resident/clearance" style="color:#1d2448;font-weight:600;">Request a document</a> to get started.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php
                            $badgeMap = [
                                'pending'  => 'db-badge--pending',
                                'approved' => 'db-badge--approved',
                                'rejected' => 'db-badge--danger',
                                'released' => 'db-badge--info',
                            ];
                            foreach ($recentRequests as $r):
                                $badgeClass = $badgeMap[$r['status']] ?? 'db-badge--pending';
                                $label      = ucfirst($r['status']);
                                $filed      = date('M d, Y', strtotime($r['created_at']));
                            ?>
                                <tr>
                                    <td><strong>#<?= str_pad($r['id'], 3, '0', STR_PAD_LEFT) ?></strong></td>
                                    <td><?= esc($r['document_type']) ?></td>
                                    <td><?= esc($r['purpose']) ?></td>
                                    <td><?= $filed ?></td>
                                    <td><span class="db-badge <?= $badgeClass ?>"><?= $label ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- ══ BLOTTER MODAL ══ -->
    <div class="db-modal-overlay" id="blotterModal">
        <div class="db-modal" style="max-width:580px;border-radius:14px;overflow:hidden;">
            <!-- Header -->
            <div style="background:linear-gradient(135deg,#c0392b,#922b21);padding:20px 24px;display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;color:#fff;">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <div>
                        <h3 style="color:#fff;font-size:15px;font-weight:700;margin:0 0 2px;">File a Blotter Report</h3>
                        <p style="color:rgba(255,255,255,0.65);font-size:12px;margin:0;">Barangay Bacolod — Official Record</p>
                    </div>
                </div>
                <button class="db-modal-close" onclick="closeModal('blotterModal')" style="background:rgba(255,255,255,0.15);color:#fff;"><i class="fas fa-times"></i></button>
            </div>

            <form action="/resident/blotter/store" method="post">
                <?= csrf_field() ?>
                <div class="db-modal-body" style="padding:24px;background:#f9fafb;max-height:65vh;overflow-y:auto;">

                    <div style="display:grid;grid-template-columns:1fr;gap:14px;">

                        <!-- Incident type -->
                        <div>
                            <label style="display:block;font-size:11px;font-weight:700;color:#9aa0b4;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;">Type of Incident <span style="color:#c0392b;">*</span></label>
                            <select name="incident_type" required style="width:100%;padding:10px 14px;border:1.5px solid #e2e5ef;border-radius:8px;font-size:13.5px;font-family:'Poppins',sans-serif;color:#1a1d2e;background:#fff;outline:none;">
                                <option value="">-- Select --</option>
                                <option>Physical Altercation / Fight</option>
                                <option>Verbal Abuse / Harassment</option>
                                <option>Theft / Robbery</option>
                                <option>Property Damage</option>
                                <option>Noise Complaint</option>
                                <option>Domestic Dispute</option>
                                <option>Trespassing</option>
                                <option>Threat / Intimidation</option>
                                <option>Other</option>
                            </select>
                        </div>

                        <!-- Date & Time -->
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                            <div>
                                <label style="display:block;font-size:11px;font-weight:700;color:#9aa0b4;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;">Date of Incident</label>
                                <input type="date" name="incident_date" style="width:100%;padding:10px 14px;border:1.5px solid #e2e5ef;border-radius:8px;font-size:13.5px;font-family:'Poppins',sans-serif;color:#1a1d2e;background:#fff;outline:none;box-sizing:border-box;">
                            </div>
                            <div>
                                <label style="display:block;font-size:11px;font-weight:700;color:#9aa0b4;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;">Time of Incident</label>
                                <input type="time" name="incident_time" style="width:100%;padding:10px 14px;border:1.5px solid #e2e5ef;border-radius:8px;font-size:13.5px;font-family:'Poppins',sans-serif;color:#1a1d2e;background:#fff;outline:none;box-sizing:border-box;">
                            </div>
                        </div>

                        <!-- Location -->
                        <div>
                            <label style="display:block;font-size:11px;font-weight:700;color:#9aa0b4;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;">Location / Place of Incident</label>
                            <input type="text" name="location" placeholder="e.g. Zone 1, near the basketball court" style="width:100%;padding:10px 14px;border:1.5px solid #e2e5ef;border-radius:8px;font-size:13.5px;font-family:'Poppins',sans-serif;color:#1a1d2e;background:#fff;outline:none;box-sizing:border-box;">
                        </div>

                        <!-- Persons involved -->
                        <div>
                            <label style="display:block;font-size:11px;font-weight:700;color:#9aa0b4;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;">Person(s) Involved</label>
                            <input type="text" name="persons_involved" placeholder="Names of persons involved (if known)" style="width:100%;padding:10px 14px;border:1.5px solid #e2e5ef;border-radius:8px;font-size:13.5px;font-family:'Poppins',sans-serif;color:#1a1d2e;background:#fff;outline:none;box-sizing:border-box;">
                        </div>

                        <!-- Narrative -->
                        <div>
                            <label style="display:block;font-size:11px;font-weight:700;color:#9aa0b4;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;">Narrative / Description <span style="color:#c0392b;">*</span></label>
                            <textarea name="narrative" rows="4" required placeholder="Describe what happened in detail..." style="width:100%;padding:10px 14px;border:1.5px solid #e2e5ef;border-radius:8px;font-size:13.5px;font-family:'Poppins',sans-serif;color:#1a1d2e;background:#fff;outline:none;resize:vertical;box-sizing:border-box;"></textarea>
                        </div>

                        <!-- Info note -->
                        <div style="background:#f0f4ff;border:1px solid #d0d8f5;border-radius:8px;padding:10px 14px;font-size:12.5px;color:#4a5068;display:flex;gap:8px;align-items:flex-start;">
                            <i class="fas fa-info-circle" style="color:#5b6fd6;margin-top:1px;flex-shrink:0;"></i>
                            <span>Your report will be recorded and reviewed by the barangay captain or secretary. Both parties will receive a summons letter once a hearing is scheduled.</span>
                        </div>
                    </div>
                </div>

                <div class="db-modal-footer" style="background:#fff;border-top:1px solid #f0f2f8;">
                    <button type="button" class="db-btn db-btn--outline" onclick="closeModal('blotterModal')">Cancel</button>
                    <button type="submit" style="padding:10px 24px;background:linear-gradient(135deg,#c0392b,#922b21);color:#fff;border:none;border-radius:8px;font-size:13.5px;font-weight:600;font-family:'Poppins',sans-serif;cursor:pointer;display:flex;align-items:center;gap:7px;">
                        <i class="fas fa-paper-plane"></i> Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ══ CONCERN MODAL ══ -->
    <div class="db-modal-overlay" id="concernModal">
        <div class="db-modal" style="max-width:520px;">
            <div class="db-modal-header">
                <h3><i class="fas fa-comments"></i> Raise a Concern</h3>
                <button class="db-modal-close" onclick="closeModal('concernModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="db-modal-body">
                <div class="db-form-grid">
                    <div class="db-form-group db-form-group--full">
                        <label>Category</label>
                        <select>
                            <option value="">-- Select Category --</option>
                            <option>Infrastructure / Roads</option>
                            <option>Garbage / Sanitation</option>
                            <option>Street Lighting</option>
                            <option>Water Supply</option>
                            <option>Peace and Order</option>
                            <option>Barangay Services</option>
                            <option>Health and Sanitation</option>
                            <option>Suggestion / Feedback</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="db-form-group db-form-group--full">
                        <label>Subject</label>
                        <input type="text" placeholder="Brief subject of your concern">
                    </div>
                    <div class="db-form-group db-form-group--full">
                        <label>Details</label>
                        <textarea rows="4" placeholder="Describe your concern or suggestion in detail..."></textarea>
                    </div>
                    <div class="db-form-group db-form-group--full">
                        <label>Preferred Contact Method</label>
                        <select>
                            <option>Email</option>
                            <option>In-person at Barangay Hall</option>
                            <option>No follow-up needed</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="db-modal-footer">
                <button class="db-btn db-btn--outline" onclick="closeModal('concernModal')">Cancel</button>
                <button class="db-btn db-btn--primary"><i class="fas fa-paper-plane"></i> Submit Concern</button>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        document.querySelectorAll('.db-nav-item').forEach(item => {
            item.addEventListener('click', () => document.getElementById('sidebar').classList.remove('open'));
        });
    </script>
</body>

</html>