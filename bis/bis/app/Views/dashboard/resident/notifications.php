<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Bacolod BIS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/style.css">
    <style>
        /* ── Notifications page ── */
        .notif-wrap {
            max-width: 720px;
        }

        /* Header row */
        .notif-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .notif-header-left h3 {
            font-size: 16px;
            font-weight: 700;
            color: #1a1d2e;
            margin: 0 0 2px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .notif-count-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #1d2448;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            min-width: 20px;
            height: 20px;
            border-radius: 100px;
            padding: 0 6px;
        }

        .notif-header-left p {
            font-size: 12.5px;
            color: #9aa0b4;
            margin: 0;
        }

        .notif-mark-all {
            font-size: 12.5px;
            font-weight: 600;
            color: #1d2448;
            background: none;
            border: 1.5px solid #1d2448;
            border-radius: 7px;
            padding: 7px 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s, color 0.2s;
            font-family: 'Poppins', sans-serif;
        }

        .notif-mark-all:hover {
            background: #1d2448;
            color: #fff;
        }

        /* Filter tabs */
        .notif-tabs {
            display: flex;
            gap: 6px;
            margin-bottom: 16px;
        }

        .notif-tab {
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            border: 1.5px solid #e2e5ef;
            background: #fff;
            color: #9aa0b4;
            transition: all 0.2s;
            font-family: 'Poppins', sans-serif;
        }

        .notif-tab.active {
            background: #1d2448;
            color: #fff;
            border-color: #1d2448;
        }

        .notif-tab:hover:not(.active) {
            border-color: #1d2448;
            color: #1d2448;
        }

        /* Notification card */
        .notif-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 8px rgba(29, 36, 72, 0.06);
            overflow: hidden;
            margin-bottom: 10px;
            border: 1.5px solid transparent;
            transition: border-color 0.2s, box-shadow 0.2s;
            cursor: pointer;
            position: relative;
        }

        .notif-card.unread {
            border-color: #e8ecf4;
            background: #f8f9ff;
        }

        .notif-card.unread:hover {
            border-color: #1d2448;
            box-shadow: 0 4px 16px rgba(29, 36, 72, 0.1);
        }

        .notif-card.read {
            border-color: #f0f2f8;
            opacity: 0.75;
        }

        .notif-card.read:hover {
            opacity: 1;
            border-color: #e2e5ef;
        }

        .notif-card-inner {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px 18px;
        }

        /* Icon */
        .notif-icon-wrap {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .notif-icon-wrap.success {
            background: rgba(22, 199, 154, 0.12);
            color: #16c79a;
        }

        .notif-icon-wrap.warning {
            background: rgba(255, 193, 7, 0.12);
            color: #e6a817;
        }

        .notif-icon-wrap.info {
            background: rgba(91, 111, 214, 0.12);
            color: #5b6fd6;
        }

        .notif-icon-wrap.danger {
            background: rgba(220, 53, 69, 0.12);
            color: #dc3545;
        }

        .notif-icon-wrap.announce {
            background: rgba(29, 36, 72, 0.08);
            color: #1d2448;
        }

        /* Body */
        .notif-body {
            flex: 1;
            min-width: 0;
        }

        .notif-title {
            font-size: 13.5px;
            font-weight: 600;
            color: #1a1d2e;
            margin: 0 0 4px;
            line-height: 1.5;
        }

        .notif-card.read .notif-title {
            font-weight: 500;
            color: #4a5068;
        }

        .notif-desc {
            font-size: 12.5px;
            color: #6b7280;
            margin: 0 0 6px;
            line-height: 1.6;
        }

        .notif-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 11.5px;
            color: #b0b6cc;
        }

        .notif-meta i {
            font-size: 10px;
        }

        /* Unread dot */
        .notif-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #1d2448;
            flex-shrink: 0;
            margin-top: 6px;
            transition: opacity 0.3s;
        }

        .notif-card.read .notif-dot {
            opacity: 0;
        }

        /* Read indicator */
        .notif-read-check {
            position: absolute;
            top: 12px;
            right: 14px;
            font-size: 11px;
            color: #16c79a;
            display: none;
        }

        .notif-card.read .notif-read-check {
            display: block;
        }

        /* Empty state */
        .notif-empty {
            text-align: center;
            padding: 48px 20px;
            color: #9aa0b4;
        }

        .notif-empty i {
            font-size: 40px;
            margin-bottom: 12px;
            display: block;
            color: #d0d5e8;
        }

        .notif-empty p {
            font-size: 14px;
            margin: 0;
        }
    </style>
</head>

<body class="db-body">
    <?php
    $role      = 'resident';
    $active    = 'notif';
    $pageTitle = 'Notifications';
    include(APPPATH . 'Views/dashboard/sidebar.php');

    $notifs = [
        [
            'id'     => 1,
            'type'   => 'success',
            'icon'   => 'fas fa-check-circle',
            'title'  => 'Clearance Request Approved',
            'desc'   => 'Your Barangay Clearance request has been approved. You may pick it up at the barangay hall during office hours.',
            'time'   => 'Mar 17, 2026 · 10:30 AM',
            'unread' => true,
        ],
        [
            'id'     => 2,
            'type'   => 'success',
            'icon'   => 'fas fa-file-check',
            'title'  => 'Document Ready for Pickup',
            'desc'   => 'Your Certificate of Residency is ready for pickup at the barangay hall.',
            'time'   => 'Mar 12, 2026 · 2:00 PM',
            'unread' => true,
        ],
        [
            'id'     => 3,
            'type'   => 'warning',
            'icon'   => 'fas fa-clock',
            'title'  => 'Request Still Pending',
            'desc'   => 'Your clearance request #001 is still pending review. We will notify you once it has been processed.',
            'time'   => 'Mar 17, 2026 · 8:00 AM',
            'unread' => false,
        ],
        [
            'id'     => 4,
            'type'   => 'announce',
            'icon'   => 'fas fa-bullhorn',
            'title'  => 'Census Update Announcement',
            'desc'   => 'Barangay Bacolod will be conducting a census update on March 20, 2026. Please prepare your household information.',
            'time'   => 'Mar 15, 2026 · 9:00 AM',
            'unread' => false,
        ],
        [
            'id'     => 5,
            'type'   => 'info',
            'icon'   => 'fas fa-info-circle',
            'title'  => 'Office Hours Reminder',
            'desc'   => 'The barangay hall is open Monday to Friday, 8:00 AM – 5:00 PM. Closed on weekends and holidays.',
            'time'   => 'Mar 10, 2026 · 7:00 AM',
            'unread' => false,
        ],
    ];

    $unreadCount = count(array_filter($notifs, fn($n) => $n['unread']));
    ?>

    <div class="db-main">
        <?php include(APPPATH . 'Views/dashboard/topbar.php'); ?>
        <div class="db-content">
            <div class="notif-wrap">

                <!-- Header -->
                <div class="notif-header">
                    <div class="notif-header-left">
                        <h3>
                            Notifications
                            <?php if ($unreadCount > 0): ?>
                                <span class="notif-count-badge" id="unreadBadge"><?= $unreadCount ?></span>
                            <?php endif; ?>
                        </h3>
                        <p><?= count($notifs) ?> total · <?= $unreadCount ?> unread</p>
                    </div>
                    <?php if ($unreadCount > 0): ?>
                        <button class="notif-mark-all" onclick="markAllRead()">
                            <i class="fas fa-check-double"></i> Mark all as read
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Filter tabs -->
                <div class="notif-tabs">
                    <button class="notif-tab active" onclick="filterNotifs('all', this)">All</button>
                    <button class="notif-tab" onclick="filterNotifs('unread', this)">Unread</button>
                    <button class="notif-tab" onclick="filterNotifs('read', this)">Read</button>
                </div>

                <!-- Notification list -->
                <div id="notifList">
                    <?php foreach ($notifs as $n): ?>
                        <div class="notif-card <?= $n['unread'] ? 'unread' : 'read' ?>"
                            id="notif-<?= $n['id'] ?>"
                            data-unread="<?= $n['unread'] ? '1' : '0' ?>"
                            onclick="markRead(<?= $n['id'] ?>)">
                            <div class="notif-card-inner">
                                <div class="notif-icon-wrap <?= $n['type'] ?>">
                                    <i class="<?= $n['icon'] ?>"></i>
                                </div>
                                <div class="notif-body">
                                    <div class="notif-title"><?= esc($n['title']) ?></div>
                                    <div class="notif-desc"><?= esc($n['desc']) ?></div>
                                    <div class="notif-meta">
                                        <i class="fas fa-clock"></i> <?= esc($n['time']) ?>
                                        <?php if ($n['unread']): ?>
                                            <span style="color:#1d2448;font-weight:600;">· Unread</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="notif-dot" id="dot-<?= $n['id'] ?>"></div>
                            </div>
                            <i class="fas fa-check-circle notif-read-check" id="check-<?= $n['id'] ?>"></i>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </div>

    <script>
        let unreadCount = <?= $unreadCount ?>;

        function markRead(id) {
            const card = document.getElementById('notif-' + id);
            if (!card || card.dataset.unread === '0') return;

            card.classList.remove('unread');
            card.classList.add('read');
            card.dataset.unread = '0';

            unreadCount = Math.max(0, unreadCount - 1);
            updateBadge();

            // Brief visual feedback
            card.style.transition = 'background 0.4s';
            card.style.background = '#f0faf6';
            setTimeout(() => {
                card.style.background = '';
            }, 600);
        }

        function markAllRead() {
            document.querySelectorAll('.notif-card.unread').forEach(card => {
                const id = card.id.replace('notif-', '');
                card.classList.remove('unread');
                card.classList.add('read');
                card.dataset.unread = '0';
            });
            unreadCount = 0;
            updateBadge();

            // Hide the mark-all button
            const btn = document.querySelector('.notif-mark-all');
            if (btn) btn.style.display = 'none';
        }

        function updateBadge() {
            const badge = document.getElementById('unreadBadge');
            if (badge) {
                if (unreadCount > 0) {
                    badge.textContent = unreadCount;
                } else {
                    badge.remove();
                }
            }
            // Update subtitle
            const total = document.querySelectorAll('.notif-card').length;
            const sub = document.querySelector('.notif-header-left p');
            if (sub) sub.textContent = total + ' total · ' + unreadCount + ' unread';
        }

        function filterNotifs(filter, btn) {
            // Update active tab
            document.querySelectorAll('.notif-tab').forEach(t => t.classList.remove('active'));
            btn.classList.add('active');

            document.querySelectorAll('.notif-card').forEach(card => {
                const isUnread = card.dataset.unread === '1';
                if (filter === 'all') {
                    card.style.display = '';
                } else if (filter === 'unread') {
                    card.style.display = isUnread ? '' : 'none';
                } else {
                    card.style.display = !isUnread ? '' : 'none';
                }
            });
        }

        document.querySelectorAll('.db-nav-item').forEach(i =>
            i.addEventListener('click', () => document.getElementById('sidebar').classList.remove('open'))
        );
    </script>
</body>

</html>