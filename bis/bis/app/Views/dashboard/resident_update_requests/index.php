<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Update Requests - Bacolod BIS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/style.css">
</head>

<body class="db-body">
    <?php
    $role = service('uri')->getSegment(1) ?: 'secretary';
    $active = 'resident-update-requests';
    $pageTitle = 'Resident Update Requests';
    include(APPPATH . 'Views/dashboard/sidebar.php');
    ?>

    <div class="db-main">
        <?php include(APPPATH . 'Views/dashboard/topbar.php'); ?>

        <div class="db-content">

            <div class="db-stats" style="margin-bottom:24px;">
                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(255,193,7,0.15);color:#ffc107;">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">Pending</span>
                        <span class="db-stat-label">For Staff Verification</span>
                    </div>
                </div>

                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(91,111,214,0.15);color:#5b6fd6;">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">Chatbot</span>
                        <span class="db-stat-label">Request Source</span>
                    </div>
                </div>

                <div class="db-stat-card">
                    <div class="db-stat-icon" style="background:rgba(22,199,154,0.15);color:#16c79a;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <span class="db-stat-num">Manual</span>
                        <span class="db-stat-label">Staff Approval Required</span>
                    </div>
                </div>
            </div>

            <div class="db-card" style="margin-bottom:20px;">
                <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
                    <div>
                        <h3 style="margin:0;">Resident Update Requests</h3>
                        <p style="margin:6px 0 0;color:#777;">
                            Requests submitted through the chatbot. Staff must verify these before updating official census records.
                        </p>
                    </div>
                </div>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <div class="db-card" style="margin-bottom:20px;">
                <form method="get" style="display:flex;gap:12px;align-items:end;flex-wrap:wrap;">
                    <div>
                        <label for="status" style="display:block;margin-bottom:6px;font-weight:600;">Filter by status</label>
                        <select name="status" id="status" class="form-select" style="padding:10px 12px;border:1px solid #ddd;border-radius:8px;">
                            <?php
                            $statuses = [
                                'pending' => 'Pending',
                                'under_review' => 'Under Review',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'cancelled' => 'Cancelled',
                                'all' => 'All',
                            ];
                            ?>

                            <?php foreach ($statuses as $value => $label): ?>
                                <option value="<?= esc($value) ?>" <?= $status === $value ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="chatbot-open-btn" style="border:none;">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </form>
            </div>

            <div class="db-card">
                <div style="overflow-x:auto;">
                    <?php if (empty($requests)): ?>
                        <div style="text-align:center;color:#777;padding:32px;">
                            No resident update requests found.
                        </div>
                    <?php else: ?>
                        <table style="width:100%;border-collapse:collapse;">
                            <thead>
                                <tr style="background:#f8f9fb;text-align:left;">
                                    <th style="padding:12px;border-bottom:1px solid #eee;">ID</th>
                                    <th style="padding:12px;border-bottom:1px solid #eee;">Type</th>
                                    <th style="padding:12px;border-bottom:1px solid #eee;">Requested Data</th>
                                    <th style="padding:12px;border-bottom:1px solid #eee;">Status</th>
                                    <th style="padding:12px;border-bottom:1px solid #eee;">Source</th>
                                    <th style="padding:12px;border-bottom:1px solid #eee;">Date Submitted</th>
                                    <th style="padding:12px;border-bottom:1px solid #eee;">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($requests as $request): ?>
                                    <tr>
                                        <td style="padding:12px;border-bottom:1px solid #eee;">
                                            <?= esc($request['id']) ?>
                                        </td>

                                        <td style="padding:12px;border-bottom:1px solid #eee;">
                                            <?= esc(ucwords(str_replace('_', ' ', $request['request_type']))) ?>
                                        </td>

                                        <td style="padding:12px;border-bottom:1px solid #eee;max-width:360px;">
                                            <?= esc(strlen($request['requested_data']) > 100
                                                ? substr($request['requested_data'], 0, 100) . '...'
                                                : $request['requested_data']) ?>
                                        </td>

                                        <td style="padding:12px;border-bottom:1px solid #eee;">
                                            <?php
                                            $statusColor = match ($request['status']) {
                                                'pending' => '#ffc107',
                                                'under_review' => '#17a2b8',
                                                'approved' => '#16c79a',
                                                'rejected' => '#dc3545',
                                                'cancelled' => '#6c757d',
                                                default => '#6c757d',
                                            };
                                            ?>

                                            <span style="background:<?= esc($statusColor) ?>;color:#fff;padding:5px 10px;border-radius:999px;font-size:12px;">
                                                <?= esc(ucwords(str_replace('_', ' ', $request['status']))) ?>
                                            </span>
                                        </td>

                                        <td style="padding:12px;border-bottom:1px solid #eee;">
                                            <?= esc($request['source_channel']) ?>
                                        </td>

                                        <td style="padding:12px;border-bottom:1px solid #eee;">
                                            <?= esc($request['created_at']) ?>
                                        </td>

                                        <td style="padding:12px;border-bottom:1px solid #eee;">
                                            <a href="<?= site_url($role . '/resident-update-requests/' . $request['id']) ?>"
                                               style="display:inline-block;padding:7px 12px;border:1px solid #5b6fd6;color:#5b6fd6;border-radius:8px;text-decoration:none;">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.querySelectorAll('.db-nav-item').forEach(i => i.addEventListener('click', () => {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) sidebar.classList.remove('open');
        }));
    </script>
</body>

</html>