<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Libraries\EmailService;

class AuthController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // ── Login ─────────────────────────────────────────────────────────────────

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        if (empty($username) || empty($password)) {
            return redirect()->to('/login')->with('error', 'Please enter your username and password.');
        }

        $user = $this->userModel->findByCredentials($username, $password);

        if (! $user) {
            return redirect()->to('/login')->with('error', 'Invalid username or password.');
        }

        // Block unverified email
        if (! $user['email_verified']) {
            return redirect()->to('/login')->with('error', 'Please verify your email address first. Check your inbox for the verification link.');
        }

        // Block pending accounts
        if ($user['status'] === 'pending') {
            return redirect()->to('/login')->with('error', 'Your account is pending approval by the barangay secretary.');
        }

        // Block rejected accounts
        if ($user['status'] === 'rejected') {
            return redirect()->to('/login')->with('error', 'Your account registration was not approved. Please contact the barangay office.');
        }

        // Set session
        session()->set([
            'user_id'   => $user['id'],
            'username'  => $user['username'],
            'full_name' => $user['full_name'],
            'role'      => $user['role'],
            'avatar'    => $user['avatar'] ?? null,
        ]);

        return redirect()->to('/' . $user['role'] . '/dashboard');
    }

    // ── Logout ────────────────────────────────────────────────────────────────

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }

    // ── Public Registration (Resident & SK only) ──────────────────────────────

    public function register()
    {
        $role = strtolower($this->request->getPost('role'));

        $allowedRoles = ['resident', 'sk'];
        if (! in_array($role, $allowedRoles, true)) {
            return redirect()->to('/signup')->with('error', 'That role cannot be self-registered. Please contact the barangay office.');
        }

        $fullName        = $this->request->getPost('full_name');
        $email           = $this->request->getPost('email');
        $username        = $this->request->getPost('username');
        $password        = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if ($password !== $confirmPassword) {
            return redirect()->back()->with('error', 'Passwords do not match.')->withInput();
        }

        if (strlen($password) < 8) {
            return redirect()->back()->with('error', 'Password must be at least 8 characters.')->withInput();
        }

        // ── Resident census verification ──────────────────────────────────
        if ($role === 'resident') {
            $householdNo = trim($this->request->getPost('household_no') ?? '');

            if (empty($householdNo)) {
                return redirect()->back()->with('error', 'Household number is required for resident registration.')->withInput();
            }

            // Look up the household in the census
            $householdModel = new \App\Models\HouseholdModel();
            $household      = $householdModel->find($householdNo);

            if (! $household) {
                return redirect()->back()->with('error', 'Household number ' . esc($householdNo) . ' was not found in the census. Please check your household number or contact the barangay office.')->withInput();
            }

            // Verify the full name matches the household head OR any member
            $enteredName = strtoupper(trim($fullName));

            // Check household head
            $headFullName = strtoupper(trim($household['first_name'] . ' ' . $household['last_name']));
            $headFullNameAlt = strtoupper(trim($household['last_name'] . ' ' . $household['first_name']));

            $memberModel = new \App\Models\HouseholdMemberModel();
            $members     = $memberModel->where('household_no', $householdNo)->findAll();

            $nameFound = ($enteredName === $headFullName || $enteredName === $headFullNameAlt);

            if (! $nameFound) {
                foreach ($members as $m) {
                    $mName    = strtoupper(trim($m['first_name'] . ' ' . $m['last_name']));
                    $mNameAlt = strtoupper(trim($m['last_name'] . ' ' . $m['first_name']));
                    if ($enteredName === $mName || $enteredName === $mNameAlt) {
                        $nameFound = true;
                        break;
                    }
                }
            }

            if (! $nameFound) {
                return redirect()->back()->with('error', 'Your name does not match any member recorded under Household #' . esc($householdNo) . '. Please check your name and household number, or contact the barangay office.')->withInput();
            }
        }
        $otp     = strval(random_int(100000, 999999));
        $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        $saved = $this->userModel->save([
            'full_name'            => $fullName,
            'email'                => $email,
            'username'             => $username,
            'password'             => password_hash($password, PASSWORD_BCRYPT),
            'role'                 => $role,
            'status'               => 'unverified',
            'email_verified'       => 0,
            'verify_token'         => $otp,
            'verify_token_expires' => $expires,
            'household_no'         => ($role === 'resident') ? ($householdNo ?? null) : null,
        ]);

        if (! $saved) {
            $errors = implode(' ', $this->userModel->errors());
            return redirect()->back()->with('error', $errors)->withInput();
        }

        // Send OTP email
        try {
            $emailService = new EmailService();
            $emailService->sendVerificationEmail($email, $fullName, $otp);
        } catch (\Throwable $e) {
            log_message('error', 'Verification email failed: ' . $e->getMessage());
            // In development, show the actual error
            if (ENVIRONMENT === 'development') {
                throw $e;
            }
            return redirect()->to('/login')->with('error', 'Account created but we could not send the verification email. Please contact the barangay office.');
        }

        // Store email in session so the verify page knows who to verify
        session()->set('pending_verify_email', $email);

        return redirect()->to('/verify-email');
    }

    // ── Show OTP entry page ───────────────────────────────────────────────────

    public function showVerifyEmail()
    {
        // Must have come from registration
        if (! session()->get('pending_verify_email')) {
            return redirect()->to('/login');
        }

        return view('verify_email');
    }

    // ── Handle OTP submission ─────────────────────────────────────────────────

    public function verifyEmail()
    {
        $email = session()->get('pending_verify_email');

        if (! $email) {
            return redirect()->to('/login')->with('error', 'Session expired. Please register again.');
        }

        $enteredOtp = trim($this->request->getPost('otp'));

        // Find user by email
        $user = $this->userModel->where('email', $email)->where('email_verified', 0)->first();

        if (! $user) {
            return redirect()->to('/login')->with('error', 'Account not found or already verified.');
        }

        // Check expiry
        if (strtotime($user['verify_token_expires']) < time()) {
            return redirect()->to('/verify-email')->with('error', 'Your code has expired. Please register again.');
        }

        // Check OTP
        if ($user['verify_token'] !== $enteredOtp) {
            return redirect()->to('/verify-email')->with('error', 'Incorrect verification code. Please try again.');
        }

        // Mark verified → status becomes pending (awaiting captain/secretary approval)
        $this->userModel->markEmailVerified($user['id']);
        session()->remove('pending_verify_email');

        return redirect()->to('/login')->with('success', 'Email verified! Your account is now pending approval by the barangay captain or secretary.');
    }

    // ── Resend OTP ────────────────────────────────────────────────────────────

    public function resendOtp()
    {
        $email = session()->get('pending_verify_email');

        if (! $email) {
            return redirect()->to('/login')->with('error', 'Session expired. Please register again.');
        }

        $user = $this->userModel->where('email', $email)->where('email_verified', 0)->first();

        if (! $user) {
            return redirect()->to('/login')->with('error', 'Account not found or already verified.');
        }

        // Generate a fresh OTP
        $otp     = strval(random_int(100000, 999999));
        $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        $this->userModel->update($user['id'], [
            'verify_token'         => $otp,
            'verify_token_expires' => $expires,
        ]);

        try {
            $emailService = new EmailService();
            $emailService->sendVerificationEmail($email, $user['full_name'], $otp);
        } catch (\Throwable $e) {
            log_message('error', 'Resend OTP failed: ' . $e->getMessage());
            if (ENVIRONMENT === 'development') {
                throw $e;
            }
            return redirect()->to('/verify-email')->with('error', 'Could not resend the code. Please try again.');
        }

        return redirect()->to('/verify-email')->with('success', 'A new verification code has been sent to your email.');
    }

    // ── Forgot Password — Step 1: Show form ──────────────────────────────────

    public function showForgotPassword()
    {
        return view('forgot_password');
    }

    // ── Forgot Password — Step 2: Send OTP to email ───────────────────────────

    public function sendForgotPasswordOtp()
    {
        $email = trim($this->request->getPost('email') ?? '');

        if (empty($email)) {
            return redirect()->back()->with('error', 'Please enter your email address.');
        }

        // Look up user by email — don't reveal whether it exists (security)
        $user = $this->userModel
            ->select('id, full_name, email, status, email_verified')
            ->where('email', $email)
            ->first();

        if ($user && $user['email_verified'] && $user['status'] === 'active') {
            $otp     = strval(random_int(100000, 999999));
            $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            $this->userModel->update($user['id'], [
                'verify_token'         => $otp,
                'verify_token_expires' => $expires,
            ]);

            try {
                $emailService = new EmailService();
                $emailService->sendPasswordResetOtp($user['email'], $user['full_name'], $otp);
            } catch (\Throwable $e) {
                log_message('error', 'Forgot password OTP failed: ' . $e->getMessage());
                if (ENVIRONMENT === 'development') throw $e;
                return redirect()->back()->with('error', 'Could not send the reset code. Please try again.');
            }
        }

        // Always store email in session and redirect — prevents email enumeration
        session()->set('fp_email', $email);

        return redirect()->to('/forgot-password/verify')
            ->with('success', 'If that email is registered, a reset code has been sent.');
    }

    // ── Forgot Password — Step 3: Show OTP entry ─────────────────────────────

    public function showForgotPasswordOtp()
    {
        if (! session()->get('fp_email')) {
            return redirect()->to('/forgot-password');
        }
        return view('reset_password_otp');
    }

    // ── Forgot Password — Step 4: Verify OTP ─────────────────────────────────

    public function verifyForgotPasswordOtp()
    {
        $email = session()->get('fp_email');
        if (! $email) {
            return redirect()->to('/forgot-password');
        }

        $otp = trim($this->request->getPost('otp') ?? '');

        $user = $this->userModel
            ->select('id, verify_token, verify_token_expires')
            ->where('email', $email)
            ->first();

        if (! $user || $user['verify_token'] !== $otp) {
            return redirect()->back()->with('error', 'Incorrect code. Please try again.');
        }

        if (strtotime($user['verify_token_expires']) < time()) {
            session()->remove('fp_email');
            return redirect()->to('/forgot-password')
                ->with('error', 'Your reset code has expired. Please request a new one.');
        }

        // OTP valid — mark as verified for the reset step
        session()->set('fp_verified', true);
        session()->set('fp_user_id', $user['id']);

        // Clear the token so it can't be reused
        $this->userModel->update($user['id'], [
            'verify_token'         => null,
            'verify_token_expires' => null,
        ]);

        return redirect()->to('/forgot-password/new-password');
    }

    // ── Forgot Password — Step 5: Resend OTP ─────────────────────────────────

    public function resendForgotPasswordOtp()
    {
        $email = session()->get('fp_email');
        if (! $email) {
            return redirect()->to('/forgot-password');
        }

        $user = $this->userModel
            ->select('id, full_name, email')
            ->where('email', $email)
            ->first();

        if ($user) {
            $otp     = strval(random_int(100000, 999999));
            $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            $this->userModel->update($user['id'], [
                'verify_token'         => $otp,
                'verify_token_expires' => $expires,
            ]);

            try {
                $emailService = new EmailService();
                $emailService->sendPasswordResetOtp($user['email'], $user['full_name'], $otp);
            } catch (\Throwable $e) {
                log_message('error', 'Resend forgot password OTP failed: ' . $e->getMessage());
                if (ENVIRONMENT === 'development') throw $e;
                return redirect()->to('/forgot-password/verify')
                    ->with('error', 'Could not resend the code. Please try again.');
            }
        }

        return redirect()->to('/forgot-password/verify')
            ->with('success', 'A new reset code has been sent to your email.');
    }

    // ── Forgot Password — Step 6: Show new password form ─────────────────────

    public function showNewPassword()
    {
        if (! session()->get('fp_verified')) {
            return redirect()->to('/forgot-password');
        }
        return view('reset_password_new');
    }

    // ── Forgot Password — Step 7: Save new password ───────────────────────────

    public function saveNewPassword()
    {
        if (! session()->get('fp_verified') || ! session()->get('fp_user_id')) {
            return redirect()->to('/forgot-password');
        }

        $newPassword     = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if (strlen($newPassword) < 8) {
            return redirect()->back()->with('error', 'Password must be at least 8 characters.');
        }

        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'Passwords do not match.');
        }

        $userId = (int) session()->get('fp_user_id');

        $this->userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_BCRYPT),
        ]);

        // Clear all forgot-password session data
        session()->remove(['fp_email', 'fp_verified', 'fp_user_id']);

        return redirect()->to('/login')
            ->with('success', 'Password reset successfully. You can now sign in with your new password.');
    }

    // ── Pending Accounts (Captain & Secretary) ────────────────────────────────

    public function pendingAccounts()
    {
        $pending = $this->userModel->getPendingAccounts();
        $role    = session()->get('role');

        return view('dashboard/' . $role . '/pending_accounts', [
            'pending' => $pending,
        ]);
    }

    public function approveAccount(int $id)
    {
        $this->userModel->approveUser($id);
        $role = session()->get('role');
        return redirect()->to('/' . $role . '/pending-accounts')->with('success', 'Account approved successfully.');
    }

    public function rejectAccount(int $id)
    {
        $this->userModel->rejectUser($id);
        $role = session()->get('role');
        return redirect()->to('/' . $role . '/pending-accounts')->with('success', 'Account rejected.');
    }

    // ── Create Official Account (Secretary super admin only) ─────────────────

    public function createOfficialAccount()
    {
        $callerRole = session()->get('role'); // 'secretary' or 'captain'
        $role       = strtolower($this->request->getPost('role'));

        // Secretary can create: captain, resident, sk
        // Captain can create: secretary, treasurer
        $allowedByRole = [
            'secretary' => ['captain', 'resident', 'sk'],
            'captain'   => ['secretary', 'treasurer'],
        ];

        $allowed = $allowedByRole[$callerRole] ?? [];

        if (! in_array($role, $allowed, true)) {
            return redirect()->back()->with('error', 'Invalid role selected.')->withInput();
        }

        // ── Single-instance enforcement for captain and secretary ─────────────
        if (in_array($role, ['captain', 'secretary'], true)) {
            $existing = $this->userModel->getActiveByRole($role);
            if ($existing) {
                return redirect()->back()->with(
                    'error',
                    'An active ' . ucfirst($role) . ' account already exists (' . esc($existing['full_name']) . '). ' .
                        'You must deactivate that account before creating a new one.'
                )->withInput();
            }
        }

        $password        = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if ($password !== $confirmPassword) {
            return redirect()->back()->with('error', 'Passwords do not match.')->withInput();
        }

        if (strlen($password) < 8) {
            return redirect()->back()->with('error', 'Password must be at least 8 characters.')->withInput();
        }

        $householdNo = null;
        if ($role === 'resident') {
            $householdNo = trim($this->request->getPost('household_no') ?? '');
            if (! empty($householdNo)) {
                $householdModel = new \App\Models\HouseholdModel();
                if (! $householdModel->find($householdNo)) {
                    return redirect()->back()->with('error', 'Household number ' . esc($householdNo) . ' was not found in the census.')->withInput();
                }
            }
        }

        $saved = $this->userModel->save([
            'full_name'      => $this->request->getPost('full_name'),
            'email'          => $this->request->getPost('email'),
            'username'       => $this->request->getPost('username'),
            'password'       => password_hash($password, PASSWORD_BCRYPT),
            'role'           => $role,
            'status'         => 'active',
            'email_verified' => 1,
            'household_no'   => $householdNo,
        ]);

        if (! $saved) {
            $errors = implode(' ', $this->userModel->errors());
            return redirect()->back()->with('error', $errors)->withInput();
        }

        $redirectPath = '/' . $callerRole . '/create-account';
        return redirect()->to($redirectPath)->with('success', ucfirst($role) . ' account created successfully.');
    }
}
