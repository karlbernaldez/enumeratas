<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Update Request Details - Bacolod BIS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/style.css">
</head>

<body class="db-body">
    <?php
    $role = service('uri')->getSegment(1) ?: 'secretary';
    $active = 'resident-update-requests';
    $pageTitle = 'Resident Update Request Details';
    include(APPPATH . 'Views/dashboard/sidebar.php');
    ?>

    <div class="db-main">
        <?php include(APPPATH . 'Views/dashboard/topbar.php'); ?>

        <div class="db-content">

            <div style="margin-bottom:16px;">
                <a href="<?= site_url($role . '/resident-update-requests') ?>"
                    style="display:inline-block;padding:8px 14px;border:1px solid #ddd;border-radius:8px;text-decoration:none;color:#333;">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="db-card" style="margin-bottom:20px;">
                <h3 style="margin:0;">Resident Update Request #<?= esc($request['id']) ?></h3>
                <p style="margin:6px 0 0;color:#777;">
                    Review the chatbot-submitted request before any official resident or census record is changed.
                </p>
            </div>

            <div class="db-card">
                <div style="display:grid;grid-template-columns:220px 1fr;gap:12px;margin-bottom:14px;">
                    <strong>Request Type</strong>
                    <span><?= esc(ucwords(str_replace('_', ' ', $request['request_type']))) ?></span>

                    <strong>Status</strong>
                    <span><?= esc(ucwords(str_replace('_', ' ', $request['status']))) ?></span>

                    <strong>Resident ID</strong>
                    <span><?= esc($request['resident_id'] ?? 'Not linked') ?></span>

                    <strong>Source Channel</strong>
                    <span><?= esc($request['source_channel']) ?></span>

                    <?php if (! empty($request['messenger_psid'])): ?>
                        <strong>Messenger PSID</strong>
                        <span><?= esc($request['messenger_psid']) ?></span>
                    <?php endif; ?>

                    <strong>Date Submitted</strong>
                    <span><?= esc($request['created_at']) ?></span>
                </div>

                <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">

                <div style="margin-bottom:18px;">
                    <label style="display:block;font-weight:600;margin-bottom:8px;">Current Data</label>
                    <div style="background:#f8f9fb;border:1px solid #eee;border-radius:10px;padding:14px;">
                        <?= nl2br(esc($request['current_data'] ?? 'No current data provided.')) ?>
                    </div>
                </div>

                <div style="margin-bottom:18px;">
                    <label style="display:block;font-weight:600;margin-bottom:8px;">Requested Data</label>
                    <div style="background:#f8f9fb;border:1px solid #eee;border-radius:10px;padding:14px;">
                        <?= nl2br(esc($request['requested_data'])) ?>
                    </div>
                </div>

                <div style="margin-bottom:18px;">
                    <label style="display:block;font-weight:600;margin-bottom:8px;">Reason</label>
                    <div style="background:#f8f9fb;border:1px solid #eee;border-radius:10px;padding:14px;">
                        <?= nl2br(esc($request['reason'] ?? 'No reason provided.')) ?>
                    </div>
                </div>

                <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">

                <div style="display:grid;grid-template-columns:220px 1fr;gap:12px;margin-bottom:14px;">
                    <strong>Reviewed By</strong>
                    <span><?= esc($request['reviewed_by'] ?? 'Not yet reviewed') ?></span>

                    <strong>Reviewed At</strong>
                    <span><?= esc($request['reviewed_at'] ?? 'Not yet reviewed') ?></span>
                </div>

                <div style="margin-bottom:18px;">
                    <label style="display:block;font-weight:600;margin-bottom:8px;">Review Notes</label>
                    <div style="background:#f8f9fb;border:1px solid #eee;border-radius:10px;padding:14px;">
                        <?= nl2br(esc($request['review_notes'] ?? 'No review notes yet.')) ?>
                    </div>
                </div>

                <div style="background:rgba(255,193,7,0.15);border:1px solid rgba(255,193,7,0.45);color:#7a5a00;padding:14px;border-radius:10px;">
                    <i class="fas fa-exclamation-triangle"></i>
                    This request is for staff verification only. Official resident or census records should not be changed until approved by authorized barangay personnel.
                </div>
                <?php if (session()->getFlashdata('success')): ?>
                    <div style="background:rgba(22,199,154,0.15);border:1px solid rgba(22,199,154,0.45);color:#0f7d61;padding:14px;border-radius:10px;margin-top:16px;">
                        <?= esc(session()->getFlashdata('success')) ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div style="background:rgba(220,53,69,0.15);border:1px solid rgba(220,53,69,0.45);color:#842029;padding:14px;border-radius:10px;margin-top:16px;">
                        <?= esc(session()->getFlashdata('error')) ?>
                    </div>
                <?php endif; ?>

                <?php if (in_array($request['status'], ['pending', 'under_review'], true)): ?>
                    <div style="margin-top:24px;border-top:1px solid #eee;padding-top:20px;">
                        <h4 style="margin:0 0 12px;">Staff Review Action</h4>

                        <?php if ($request['status'] === 'pending'): ?>
                            <form method="post"
                                action="<?= site_url($role . '/resident-update-requests/' . $request['id'] . '/under-review') ?>"
                                style="margin-bottom:16px;">
                                <?= csrf_field() ?>

                                <button type="submit"
                                    style="padding:10px 14px;background:#17a2b8;color:#fff;border:none;border-radius:8px;cursor:pointer;">
                                    <i class="fas fa-search"></i> Mark as Under Review
                                </button>
                            </form>
                        <?php endif; ?>

                        <form method="post"
                            action="<?= site_url($role . '/resident-update-requests/' . $request['id'] . '/approve') ?>"
                            style="margin-bottom:16px;">
                            <?= csrf_field() ?>

                            <label style="display:block;font-weight:600;margin-bottom:8px;">
                                Review Notes for Approval
                            </label>

                            <textarea name="review_notes"
                                rows="3"
                                style="width:100%;padding:12px;border:1px solid #ddd;border-radius:10px;margin-bottom:10px;"
                                placeholder="Example: Verified by staff. Resident should now be updated manually in census records."></textarea>

                            <button type="submit"
                                onclick="return confirm('Approve this request? This will NOT automatically update census records.');"
                                style="padding:10px 14px;background:#16c79a;color:#fff;border:none;border-radius:8px;cursor:pointer;">
                                <i class="fas fa-check"></i> Approve Request
                            </button>
                        </form>

                        <form method="post"
                            action="<?= site_url($role . '/resident-update-requests/' . $request['id'] . '/reject') ?>">
                            <?= csrf_field() ?>

                            <label style="display:block;font-weight:600;margin-bottom:8px;">
                                Review Notes for Rejection <span style="color:#dc3545;">*</span>
                            </label>

                            <textarea name="review_notes"
                                rows="3"
                                required
                                style="width:100%;padding:12px;border:1px solid #ddd;border-radius:10px;margin-bottom:10px;"
                                placeholder="Explain why this request is rejected."></textarea>

                            <button type="submit"
                                onclick="return confirm('Reject this request?');"
                                style="padding:10px 14px;background:#dc3545;color:#fff;border:none;border-radius:8px;cursor:pointer;">
                                <i class="fas fa-times"></i> Reject Request
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div style="margin-top:24px;background:#f8f9fb;border:1px solid #eee;padding:14px;border-radius:10px;color:#555;">
                        This request has already been reviewed. No further action is available.
                    </div>
                <?php endif; ?>
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