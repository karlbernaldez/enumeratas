<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Official Account - Bacolod BIS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/style.css">
    <style>
        .ca-wrap {
            max-width: 640px;
        }

        .ca-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(29, 36, 72, 0.07);
            overflow: hidden;
        }

        /* Card header banner */
        .ca-card-header {
            background: linear-gradient(135deg, #1d2448 0%, #2e3a6e 100%);
            padding: 28px 32px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .ca-card-header-icon {
            width: 52px;
            height: 52px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #fff;
            flex-shrink: 0;
        }

        .ca-card-header-text h3 {
            color: #fff;
            font-size: 17px;
            font-weight: 600;
            margin: 0 0 4px;
        }

        .ca-card-header-text p {
            color: rgba(255, 255, 255, 0.65);
            font-size: 12.5px;
            margin: 0;
        }

        /* Role selector pills */
        .ca-role-pills {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }

        .ca-role-pill {
            flex: 1;
            border: 2px solid #e2e5ef;
            border-radius: 12px;
            padding: 14px 16px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
        }

        .ca-role-pill:hover {
            border-color: #1d2448;
            background: #f8f9ff;
        }

        .ca-role-pill.selected {
            border-color: #1d2448;
            background: #f0f2ff;
        }

        .ca-role-pill input[type="radio"] {
            display: none;
        }

        .ca-role-pill-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .ca-role-pill--captain .ca-role-pill-icon {
            background: rgba(29, 36, 72, 0.1);
            color: #1d2448;
        }

        .ca-role-pill--resident .ca-role-pill-icon {
            background: rgba(91, 111, 214, 0.12);
            color: #5b6fd6;
        }

        .ca-role-pill--sk .ca-role-pill-icon {
            background: rgba(22, 199, 154, 0.12);
            color: #16a085;
        }

        .ca-role-pill-label {
            font-size: 13.5px;
            font-weight: 600;
            color: #1a1d2e;
        }

        .ca-role-pill-sub {
            font-size: 11.5px;
            color: #9aa0b4;
            margin-top: 1px;
        }

        /* Blocked pill — active account already exists */
        .ca-role-pill--blocked {
            opacity: 0.6;
            cursor: not-allowed !important;
            pointer-events: none;
            border-color: #fad4d4 !important;
            background: #fff5f5 !important;
        }

        .ca-role-pill--blocked .ca-role-pill-icon {
            background: rgba(192, 57, 43, 0.1) !important;
            color: #c0392b !important;
        }

        /* Active-account warning banner */
        .ca-active-warn {
            background: #fff8f0;
            border: 1px solid #fde8c8;
            border-radius: 10px;
            padding: 13px 16px;
            font-size: 12.5px;
            color: #7a4200;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 20px;
        }

        .ca-active-warn i {
            color: #e67e22;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .ca-active-warn strong {
            color: #5a3000;
        }

        .ca-deactivate-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            padding: 6px 14px;
            background: #c0392b;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .ca-deactivate-btn:hover {
            opacity: 0.88;
        }

        .ca-role-pill .ca-check {
            margin-left: auto;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #e2e5ef;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .ca-role-pill.selected .ca-check {
            background: #1d2448;
            border-color: #1d2448;
            color: #fff;
            font-size: 10px;
        }

        /* Form body */
        .ca-body {
            padding: 28px 32px 32px;
        }

        .ca-section-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #9aa0b4;
            margin: 0 0 14px;
        }

        .ca-divider {
            height: 1px;
            background: #f0f2f8;
            margin: 24px 0;
        }

        .ca-form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        .ca-form-row--full {
            grid-template-columns: 1fr;
        }

        .ca-form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .ca-form-group label {
            font-size: 12.5px;
            font-weight: 600;
            color: #4a5068;
        }

        .ca-input-wrap {
            position: relative;
        }

        .ca-input-wrap .ca-input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #b0b6cc;
            font-size: 13px;
            pointer-events: none;
        }

        .ca-form-group input,
        .ca-form-group select {
            width: 100%;
            padding: 10px 14px 10px 36px;
            border: 1.5px solid #e2e5ef;
            border-radius: 9px;
            font-size: 13.5px;
            font-family: 'Poppins', sans-serif;
            color: #1a1d2e;
            background: #fff;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            box-sizing: border-box;
        }

        .ca-form-group input:focus,
        .ca-form-group select:focus {
            border-color: #1d2448;
            box-shadow: 0 0 0 3px rgba(29, 36, 72, 0.08);
        }

        .ca-form-group input::placeholder {
            color: #c0c6d8;
        }

        /* Password eye toggle */
        .ca-pw-wrap {
            position: relative;
        }

        .ca-pw-wrap input {
            padding-right: 42px;
        }

        .ca-eye-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #b0b6cc;
            cursor: pointer;
            font-size: 13px;
            padding: 4px;
            transition: color 0.2s;
            line-height: 1;
        }

        .ca-eye-btn:hover {
            color: #1d2448;
        }

        /* Password strength bar */
        .ca-pw-strength {
            margin-top: 6px;
            display: none;
        }

        .ca-pw-strength.visible {
            display: block;
        }

        .ca-pw-bar-track {
            height: 4px;
            background: #e2e5ef;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 4px;
        }

        .ca-pw-bar-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s, background 0.3s;
            width: 0%;
        }

        .ca-pw-label {
            font-size: 11px;
            color: #9aa0b4;
        }

        /* Alerts */
        .ca-alert {
            padding: 11px 14px;
            border-radius: 9px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 20px;
        }

        .ca-alert--success {
            background: #f0faf6;
            color: #1a7a55;
            border: 1px solid #c3e8d8;
        }

        .ca-alert--error {
            background: #fff0f1;
            color: #c0392b;
            border: 1px solid #fad4d4;
        }

        /* Submit button */
        .ca-submit-btn {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #1d2448, #2e3a6e);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
            transition: opacity 0.2s, transform 0.15s;
        }

        .ca-submit-btn:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }

        .ca-submit-btn:active {
            transform: translateY(0);
        }

        /* Info note */
        .ca-info-note {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #f5f7ff;
            border: 1px solid #dde2f5;
            border-radius: 9px;
            padding: 12px 14px;
            font-size: 12.5px;
            color: #4a5068;
            line-height: 1.6;
            margin-bottom: 22px;
        }

        .ca-info-note i {
            color: #5b6fd6;
            margin-top: 1px;
            flex-shrink: 0;
        }

        @media (max-width: 560px) {
            .ca-form-row {
                grid-template-columns: 1fr;
            }

            .ca-role-pills {
                flex-direction: column;
            }

            .ca-body {
                padding: 22px 20px 24px;
            }

            .ca-card-header {
                padding: 22px 20px;
            }
        }
    </style>
</head>

<body class="db-body">
    <?php
    $role      = 'secretary';
    $active    = 'create_account';
    $pageTitle = 'Create Official Account';
    include(APPPATH . 'Views/dashboard/sidebar.php');
    ?>
    <div class="db-main">
        <?php include(APPPATH . 'Views/dashboard/topbar.php'); ?>
        <div class="db-content">

            <div class="db-page-header" style="margin-bottom:24px;">
                <h2>Create Account</h2>
                <p>Set up a new Captain, Resident, or SK account. The account will be immediately active.</p>
            </div>

            <div class="ca-wrap">
                <div class="ca-card">

                    <!-- Card header -->
                    <div class="ca-card-header">
                        <div class="ca-card-header-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="ca-card-header-text">
                            <h3>New Official Account</h3>
                            <p>Fill in the details below to create an official barangay account.</p>
                        </div>
                    </div>

                    <!-- Form body -->
                    <div class="ca-body">

                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="ca-alert ca-alert--success">
                                <i class="fas fa-check-circle"></i>
                                <?= session()->getFlashdata('success') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="ca-alert ca-alert--error">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>

                        <form action="/secretary/create-account/store" method="post" id="createForm">
                            <?= csrf_field() ?>
                            <input type="hidden" name="role" id="roleInput" value="captain">

                            <!-- Role selection -->
                            <p class="ca-section-label">Select Role</p>

                            <?php
                            $activeCaptain   = $activeCaptain   ?? null;
                            $activeSecretary = $activeSecretary ?? null;
                            ?>

                            <!-- Active-account warnings -->
                            <?php if ($activeCaptain): ?>
                                <div class="ca-active-warn" id="warnCaptain">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <div>
                                        <strong>Active Captain account exists:</strong> <?= esc($activeCaptain['full_name']) ?> (<?= esc($activeCaptain['username']) ?>)<br>
                                        <span style="font-size:12px;">You must deactivate this account before creating a new Captain.</span>
                                        <br>
                                        <form action="/secretary/deactivate-user/<?= $activeCaptain['id'] ?>" method="post" style="display:inline;"
                                            onsubmit="return confirm('Deactivate <?= esc($activeCaptain['full_name']) ?>\'s account? They will no longer be able to log in.')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="ca-deactivate-btn">
                                                <i class="fas fa-user-slash"></i> Deactivate <?= esc($activeCaptain['full_name']) ?>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="ca-role-pills">
                                <label class="ca-role-pill ca-role-pill--captain <?= $activeCaptain ? 'ca-role-pill--blocked' : 'selected' ?>"
                                    id="pill-captain"
                                    <?= $activeCaptain ? '' : 'onclick="selectRole(\'captain\')"' ?>>
                                    <input type="radio" name="_role_ui" value="captain" <?= $activeCaptain ? 'disabled' : '' ?>>
                                    <div class="ca-role-pill-icon"><i class="fas fa-user-tie"></i></div>
                                    <div>
                                        <div class="ca-role-pill-label">Captain</div>
                                        <div class="ca-role-pill-sub"><?= $activeCaptain ? 'Deactivate existing first' : 'Barangay Captain' ?></div>
                                    </div>
                                    <div class="ca-check" id="check-captain"><i class="fas fa-check"></i></div>
                                </label>
                                <label class="ca-role-pill ca-role-pill--resident" id="pill-resident" onclick="selectRole('resident')">
                                    <input type="radio" name="_role_ui" value="resident">
                                    <div class="ca-role-pill-icon"><i class="fas fa-user"></i></div>
                                    <div>
                                        <div class="ca-role-pill-label">Resident</div>
                                        <div class="ca-role-pill-sub">Barangay Resident</div>
                                    </div>
                                    <div class="ca-check" id="check-resident"><i class="fas fa-check"></i></div>
                                </label>
                                <label class="ca-role-pill ca-role-pill--sk" id="pill-sk" onclick="selectRole('sk')">
                                    <input type="radio" name="_role_ui" value="sk">
                                    <div class="ca-role-pill-icon"><i class="fas fa-users"></i></div>
                                    <div>
                                        <div class="ca-role-pill-label">SK</div>
                                        <div class="ca-role-pill-sub">Sangguniang Kabataan</div>
                                    </div>
                                    <div class="ca-check" id="check-sk"><i class="fas fa-check"></i></div>
                                </label>
                            </div>

                            <div class="ca-divider"></div>

                            <!-- Info note -->
                            <div class="ca-info-note">
                                <i class="fas fa-info-circle"></i>
                                <span>This account will be <strong>immediately active</strong> — no email verification or approval required. Share the credentials directly with the user.</span>
                            </div>

                            <!-- Personal info -->
                            <p class="ca-section-label">Account Details</p>

                            <div class="ca-form-row">
                                <div class="ca-form-group">
                                    <label>Full Name</label>
                                    <div class="ca-input-wrap">
                                        <i class="fas fa-user ca-input-icon"></i>
                                        <input type="text" name="full_name" placeholder="e.g. Juan Dela Cruz" required>
                                    </div>
                                </div>
                                <div class="ca-form-group">
                                    <label>Username</label>
                                    <div class="ca-input-wrap">
                                        <i class="fas fa-at ca-input-icon"></i>
                                        <input type="text" name="username" placeholder="e.g. juan_delacruz" required>
                                    </div>
                                </div>
                            </div>

                            <div class="ca-form-row ca-form-row--full" style="margin-bottom:14px;">
                                <div class="ca-form-group">
                                    <label>Email Address</label>
                                    <div class="ca-input-wrap">
                                        <i class="fas fa-envelope ca-input-icon"></i>
                                        <input type="email" name="email" placeholder="e.g. juan@email.com" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Household No — shown only for Resident -->
                            <div class="ca-form-row ca-form-row--full" id="householdRow" style="display:none;margin-bottom:14px;">
                                <div class="ca-form-group">
                                    <label>Household Number <span style="color:#9aa0b4;font-weight:400;">(optional — links resident to census)</span></label>
                                    <div class="ca-input-wrap">
                                        <i class="fas fa-home ca-input-icon"></i>
                                        <input type="text" name="household_no" id="householdNo" placeholder="e.g. 12345" maxlength="5">
                                    </div>
                                </div>
                            </div>

                            <div class="ca-form-row">
                                <div class="ca-form-group">
                                    <label>Password</label>
                                    <div class="ca-input-wrap ca-pw-wrap">
                                        <i class="fas fa-lock ca-input-icon"></i>
                                        <input type="password" name="password" id="pw1" placeholder="Minimum 8 characters" minlength="8" required oninput="checkStrength(this.value)">
                                        <button type="button" class="ca-eye-btn" onclick="togglePw('pw1','eye1')" aria-label="Toggle password">
                                            <i class="fas fa-eye" id="eye1"></i>
                                        </button>
                                    </div>
                                    <div class="ca-pw-strength" id="pwStrength">
                                        <div class="ca-pw-bar-track">
                                            <div class="ca-pw-bar-fill" id="pwBar"></div>
                                        </div>
                                        <span class="ca-pw-label" id="pwLabel"></span>
                                    </div>
                                </div>
                                <div class="ca-form-group">
                                    <label>Confirm Password</label>
                                    <div class="ca-input-wrap ca-pw-wrap">
                                        <i class="fas fa-lock ca-input-icon"></i>
                                        <input type="password" name="confirm_password" id="pw2" placeholder="Re-enter password" required>
                                        <button type="button" class="ca-eye-btn" onclick="togglePw('pw2','eye2')" aria-label="Toggle confirm password">
                                            <i class="fas fa-eye" id="eye2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="ca-submit-btn">
                                <i class="fas fa-user-plus"></i> Create Account
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Role pill selection
        const roles = ['captain', 'resident', 'sk'];
        const captainBlocked = <?= $activeCaptain ? 'true' : 'false' ?>;

        function selectRole(role) {
            roles.forEach(r => {
                const pill = document.getElementById('pill-' + r);
                if (pill) pill.classList.toggle('selected', r === role);
            });
            document.getElementById('roleInput').value = role;

            // Show household number field only for resident
            document.getElementById('householdRow').style.display = (role === 'resident') ? '' : 'none';
        }

        // Auto-select first available role on load
        (function() {
            const firstAvailable = captainBlocked ? 'resident' : 'captain';
            selectRole(firstAvailable);
        })();

        // Block form submission if captain is selected but blocked
        document.getElementById('createForm').addEventListener('submit', function(e) {
            const selectedRole = document.getElementById('roleInput').value;
            if (selectedRole === 'captain' && captainBlocked) {
                e.preventDefault();
                alert('An active Captain account already exists. Deactivate it first.');
                return;
            }
            const pw = document.getElementById('pw1').value;
            const cpw = document.getElementById('pw2').value;
            if (pw !== cpw) {
                e.preventDefault();
                alert('Passwords do not match.');
            }
        });

        // Password toggle
        function togglePw(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Password strength
        function checkStrength(val) {
            const el = document.getElementById('pwStrength');
            const bar = document.getElementById('pwBar');
            const label = document.getElementById('pwLabel');

            if (!val) {
                el.classList.remove('visible');
                return;
            }
            el.classList.add('visible');

            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const levels = [{
                    w: '25%',
                    bg: '#e74c3c',
                    text: 'Weak'
                },
                {
                    w: '50%',
                    bg: '#e67e22',
                    text: 'Fair'
                },
                {
                    w: '75%',
                    bg: '#f1c40f',
                    text: 'Good'
                },
                {
                    w: '100%',
                    bg: '#16c79a',
                    text: 'Strong'
                },
            ];
            const lvl = levels[score - 1] || levels[0];
            bar.style.width = lvl.w;
            bar.style.background = lvl.bg;
            label.textContent = lvl.text;
            label.style.color = lvl.bg;
        }

        document.querySelectorAll('.db-nav-item').forEach(i =>
            i.addEventListener('click', () => document.getElementById('sidebar').classList.remove('open'))
        );
    </script>
</body>

</html>