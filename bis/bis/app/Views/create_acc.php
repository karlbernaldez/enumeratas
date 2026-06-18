<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Barangay Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f6fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1a1d2e;
            padding: 24px 16px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            padding: 40px 36px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
        }

        .login-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 28px;
            text-align: center;
        }

        .login-logo img {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: contain;
            margin-bottom: 12px;
        }

        .login-logo h1 {
            font-size: 18px;
            font-weight: 700;
            color: #1d2448;
        }

        .login-logo p {
            font-size: 13px;
            color: #9aa0b4;
            margin-top: 2px;
        }

        .alert {
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .alert--error {
            background: #fff0f1;
            color: #c0392b;
            border: 1px solid #fad4d4;
        }

        .alert--success {
            background: #f0faf6;
            color: #1a7a55;
            border: 1px solid #c3e8d8;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #4a5068;
            margin-bottom: 6px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #e2e5ef;
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #1a1d2e;
            background: #fff;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            appearance: none;
            -webkit-appearance: none;
        }

        .form-group input::placeholder {
            color: #b0b6cc;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #1d2448;
            box-shadow: 0 0 0 3px rgba(29, 36, 72, 0.08);
        }

        /* Custom select arrow */
        .select-wrap {
            position: relative;
        }

        .select-wrap::after {
            content: '\f107';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #b0b6cc;
            pointer-events: none;
            font-size: 13px;
        }

        /* Role hint badge */
        .role-hint {
            font-size: 11.5px;
            color: #9aa0b4;
            margin-top: 5px;
            display: none;
        }

        .role-hint.visible {
            display: block;
        }

        .role-hint.pending {
            color: #e67e22;
        }

        .role-hint.active {
            color: #16a085;
        }

        .password-wrap {
            position: relative;
        }

        .password-wrap input {
            padding-right: 44px;
        }

        .eye-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #b0b6cc;
            cursor: pointer;
            font-size: 14px;
            padding: 4px;
            transition: color 0.2s;
        }

        .eye-btn:hover {
            color: #1d2448;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background: #1d2448;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            margin-top: 8px;
            transition: background 0.2s, transform 0.15s;
        }

        .submit-btn:hover {
            background: #2e3a6e;
            transform: translateY(-1px);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .divider {
            height: 1px;
            background: #eef0f6;
            margin: 20px 0 16px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #9aa0b4;
            text-decoration: none;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #1d2448;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 32px 24px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">

            <div class="login-logo">
                <img src="/bacolod.png" alt="Bacolod Logo">
                <h1>Create Account</h1>
                <p>Register for Barangay Management System</p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert--error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="/signup/store" method="post">
                <?= csrf_field() ?>

                <!-- Role Dropdown -->
                <div class="form-group">
                    <label for="role">I am a <span style="color:#c0392b;">*</span></label>
                    <div class="select-wrap">
                        <select id="role" name="role" required onchange="onRoleChange(this.value)">
                            <option value="" disabled selected>-- Select your role --</option>
                            <option value="resident">Resident</option>
                            <option value="sk">SK (Sangguniang Kabataan)</option>
                        </select>
                    </div>
                    <p class="role-hint pending" id="hint-resident">
                        <i class="fas fa-clock"></i> You will receive a verification email first. After verifying, the barangay captain or secretary will check if you are recorded in the census before activating your account.
                    </p>
                    <p class="role-hint pending" id="hint-sk">
                        <i class="fas fa-clock"></i> You will receive a verification email first. After verifying, your account requires approval from the barangay captain or secretary before you can log in.
                    </p>
                </div>

                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name"
                        value="<?= old('full_name') ?>"
                        placeholder="Enter your full name" required>
                </div>

                <!-- Household Number — shown only for Resident -->
                <div class="form-group" id="household-group" style="display:none;">
                    <label for="household_no">
                        Household Number <span style="color:#c0392b;">*</span>
                    </label>
                    <input type="text" id="household_no" name="household_no"
                        value="<?= old('household_no') ?>"
                        placeholder="Enter your 5-digit household number"
                        maxlength="5" inputmode="numeric"
                        pattern="[0-9]{5}">
                    <p style="font-size:11.5px;color:#9aa0b4;margin-top:5px;">
                        <i class="fas fa-info-circle"></i>
                        Your household number is assigned by the barangay. Ask your household head or the barangay office.
                    </p>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                        value="<?= old('email') ?>"
                        placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username"
                        value="<?= old('username') ?>"
                        placeholder="Choose a username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrap">
                        <input type="password" id="password" name="password"
                            placeholder="Minimum 8 characters" required>
                        <button type="button" class="eye-btn" onclick="togglePass('password','eye1')" aria-label="Toggle password">
                            <i class="fas fa-eye" id="eye1"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="password-wrap">
                        <input type="password" id="confirm_password" name="confirm_password"
                            placeholder="Confirm your password" required>
                        <button type="button" class="eye-btn" onclick="togglePass('confirm_password','eye2')" aria-label="Toggle confirm password">
                            <i class="fas fa-eye" id="eye2"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>

            <div class="divider"></div>

            <a href="/login" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to sign in
            </a>

        </div>
    </div>

    <script>
        function onRoleChange(val) {
            document.getElementById('hint-resident').classList.toggle('visible', val === 'resident');
            document.getElementById('hint-sk').classList.toggle('visible', val === 'sk');

            // Show household number only for residents
            const hhGroup = document.getElementById('household-group');
            const hhInput = document.getElementById('household_no');
            if (val === 'resident') {
                hhGroup.style.display = 'block';
                hhInput.required = true;
            } else {
                hhGroup.style.display = 'none';
                hhInput.required = false;
                hhInput.value = '';
            }
        }

        function togglePass(inputId, iconId) {
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

        // Restore selected role on validation error redirect
        const savedRole = '<?= old('role') ?>';
        if (savedRole) {
            document.getElementById('role').value = savedRole;
            onRoleChange(savedRole);
        }

        // Numbers only for household number
        document.getElementById('household_no').addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
    </script>
</body>

</html>