const User        = require('../models/User');
const bcrypt      = require('bcryptjs');
const nodemailer  = require('nodemailer');
require('dotenv').config();

function mailer() {
  return nodemailer.createTransport({
    host: process.env.EMAIL_HOST,
    port: parseInt(process.env.EMAIL_PORT || '2525'),
    auth: { user: process.env.EMAIL_USER, pass: process.env.EMAIL_PASS },
  });
}

async function sendOtpEmail(to, name, otp, subject, body) {
  const t = mailer();
  await t.sendMail({
    from: `"${process.env.EMAIL_FROM_NAME}" <${process.env.EMAIL_FROM}>`,
    to,
    subject,
    html: `<p>Hello <strong>${name}</strong>,</p><p>${body}</p><h2 style="letter-spacing:6px">${otp}</h2><p>This code expires in 15 minutes.</p>`,
  });
}

// GET /login
exports.getLogin = (req, res) => {
  if (req.session.userId) return res.redirect(`/${req.session.role}/dashboard`);
  res.render('auth/login', { title: 'Login' });
};

// POST /login
exports.postLogin = async (req, res) => {
  const { username, password } = req.body;
  if (!username || !password) {
    req.flash('error', 'Please enter your username and password.');
    return res.redirect('/login');
  }
  const user = await User.findByCredentials(username, password);
  if (!user) {
    req.flash('error', 'Invalid username or password.');
    return res.redirect('/login');
  }
  if (!user.email_verified) {
    req.flash('error', 'Please verify your email address first.');
    return res.redirect('/login');
  }
  if (user.status === 'pending') {
    req.flash('error', 'Your account is pending approval by the barangay secretary.');
    return res.redirect('/login');
  }
  if (user.status === 'rejected') {
    req.flash('error', 'Your account registration was not approved. Please contact the barangay office.');
    return res.redirect('/login');
  }
  req.session.userId   = user.id;
  req.session.username = user.username;
  req.session.fullName = user.full_name;
  req.session.role     = user.role;
  req.session.avatar   = user.avatar || null;

  const roleHome = { captain: '/captain/calendar', secretary: '/secretary/calendar' };
  return res.redirect(roleHome[user.role] || `/${user.role}/dashboard`);
};

// GET /logout
exports.logout = (req, res) => {
  req.session.destroy(() => res.redirect('/login'));
};

// GET /signup
exports.getSignup = (req, res) => {
  res.render('auth/signup', { title: 'Sign Up' });
};

// POST /signup/store
exports.postSignup = async (req, res) => {
  const { full_name, email, username, password, confirm_password, role, household_no } = req.body;
  const allowedRoles = ['resident', 'sk'];
  if (!allowedRoles.includes(role)) {
    req.flash('error', 'That role cannot be self-registered.');
    return res.redirect('/signup');
  }
  if (password !== confirm_password) {
    req.flash('error', 'Passwords do not match.');
    return res.redirect('/signup');
  }
  if (password.length < 8) {
    req.flash('error', 'Password must be at least 8 characters.');
    return res.redirect('/signup');
  }

  if (role === 'resident') {
    if (!household_no) {
      req.flash('error', 'Household number is required for resident registration.');
      return res.redirect('/signup');
    }
    const Household = require('../models/Household');
    const HouseholdMember = require('../models/HouseholdMember');
    const hh = await Household.findByNo(household_no.trim());
    if (!hh) {
      req.flash('error', `Household number ${household_no} was not found in the census.`);
      return res.redirect('/signup');
    }
    const entered = full_name.trim().toUpperCase();
    const headFwd = `${hh.first_name} ${hh.last_name}`.toUpperCase();
    const headRev = `${hh.last_name} ${hh.first_name}`.toUpperCase();
    let found = entered === headFwd || entered === headRev;
    if (!found) {
      const members = await HouseholdMember.getByHousehold(household_no.trim());
      for (const m of members) {
        const mFwd = `${m.first_name} ${m.last_name}`.toUpperCase();
        const mRev = `${m.last_name} ${m.first_name}`.toUpperCase();
        if (entered === mFwd || entered === mRev) { found = true; break; }
      }
    }
    if (!found) {
      req.flash('error', `Your name does not match any member under Household #${household_no}.`);
      return res.redirect('/signup');
    }
  }

  const otp     = String(Math.floor(100000 + Math.random() * 900000));
  const expires = new Date(Date.now() + 15 * 60 * 1000).toISOString().slice(0, 19).replace('T', ' ');

  try {
    await User.create({
      full_name, email, username, password,
      role, status: 'unverified', email_verified: 0,
      verify_token: otp, verify_token_expires: expires,
      household_no: role === 'resident' ? household_no : null,
    });
    await sendOtpEmail(email, full_name, otp, 'BIS Email Verification', 'Your verification code is:');
    req.session.pendingVerifyEmail = email;
    return res.redirect('/verify-email');
  } catch (err) {
    req.flash('error', err.message || 'Registration failed. Please try again.');
    return res.redirect('/signup');
  }
};

// GET /verify-email
exports.getVerifyEmail = (req, res) => {
  if (!req.session.pendingVerifyEmail) return res.redirect('/login');
  res.render('auth/verify_email', { title: 'Verify Email' });
};

// POST /verify-email
exports.postVerifyEmail = async (req, res) => {
  const email = req.session.pendingVerifyEmail;
  if (!email) { req.flash('error', 'Session expired.'); return res.redirect('/login'); }
  const { otp } = req.body;
  const [rows] = await require('../config/db').query(
    'SELECT * FROM users WHERE email = ? AND email_verified = 0', [email]
  );
  const user = rows[0];
  if (!user) { req.flash('error', 'Account not found or already verified.'); return res.redirect('/login'); }
  if (new Date(user.verify_token_expires) < new Date()) {
    req.flash('error', 'Your code has expired. Please register again.');
    return res.redirect('/verify-email');
  }
  if (user.verify_token !== otp.trim()) {
    req.flash('error', 'Incorrect verification code.');
    return res.redirect('/verify-email');
  }
  await User.markEmailVerified(user.id);
  delete req.session.pendingVerifyEmail;
  req.flash('success', 'Email verified! Your account is pending approval.');
  return res.redirect('/login');
};

// GET /resend-otp
exports.resendOtp = async (req, res) => {
  const email = req.session.pendingVerifyEmail;
  if (!email) return res.redirect('/login');
  const [rows] = await require('../config/db').query(
    'SELECT * FROM users WHERE email = ? AND email_verified = 0', [email]
  );
  const user = rows[0];
  if (!user) return res.redirect('/login');
  const otp     = String(Math.floor(100000 + Math.random() * 900000));
  const expires = new Date(Date.now() + 15 * 60 * 1000).toISOString().slice(0, 19).replace('T', ' ');
  await User.update(user.id, { verify_token: otp, verify_token_expires: expires });
  await sendOtpEmail(email, user.full_name, otp, 'BIS Email Verification', 'Your new verification code is:');
  req.flash('success', 'A new verification code has been sent.');
  return res.redirect('/verify-email');
};

// GET /forgot-password
exports.getForgotPassword = (req, res) => res.render('auth/forgot_password', { title: 'Forgot Password' });

// POST /forgot-password
exports.postForgotPassword = async (req, res) => {
  const { email } = req.body;
  if (!email) { req.flash('error', 'Please enter your email.'); return res.redirect('/forgot-password'); }
  const db = require('../config/db');
  const [[user]] = await db.query(
    "SELECT id, full_name, email FROM users WHERE email = ? AND email_verified = 1 AND status = 'active'", [email]
  );
  if (user) {
    const otp     = String(Math.floor(100000 + Math.random() * 900000));
    const expires = new Date(Date.now() + 15 * 60 * 1000).toISOString().slice(0, 19).replace('T', ' ');
    await User.update(user.id, { verify_token: otp, verify_token_expires: expires });
    await sendOtpEmail(email, user.full_name, otp, 'BIS Password Reset', 'Your password reset code is:');
  }
  req.session.fpEmail = email;
  req.flash('success', 'If that email is registered, a reset code has been sent.');
  return res.redirect('/forgot-password/verify');
};

// GET /forgot-password/verify
exports.getForgotPasswordOtp = (req, res) => {
  if (!req.session.fpEmail) return res.redirect('/forgot-password');
  res.render('auth/reset_password_otp', { title: 'Enter Reset Code' });
};

// POST /forgot-password/verify
exports.postForgotPasswordOtp = async (req, res) => {
  const email = req.session.fpEmail;
  if (!email) return res.redirect('/forgot-password');
  const { otp } = req.body;
  const db = require('../config/db');
  const [[user]] = await db.query('SELECT id, verify_token, verify_token_expires FROM users WHERE email = ?', [email]);
  if (!user || user.verify_token !== otp.trim()) {
    req.flash('error', 'Incorrect code.'); return res.redirect('/forgot-password/verify');
  }
  if (new Date(user.verify_token_expires) < new Date()) {
    delete req.session.fpEmail;
    req.flash('error', 'Code expired. Please request a new one.'); return res.redirect('/forgot-password');
  }
  await User.update(user.id, { verify_token: null, verify_token_expires: null });
  req.session.fpVerified = true;
  req.session.fpUserId   = user.id;
  return res.redirect('/forgot-password/new-password');
};

// GET /forgot-password/new-password
exports.getNewPassword = (req, res) => {
  if (!req.session.fpVerified) return res.redirect('/forgot-password');
  res.render('auth/reset_password_new', { title: 'Set New Password' });
};

// POST /forgot-password/reset
exports.postNewPassword = async (req, res) => {
  if (!req.session.fpVerified || !req.session.fpUserId) return res.redirect('/forgot-password');
  const { new_password, confirm_password } = req.body;
  if (new_password.length < 8) { req.flash('error', 'Password must be at least 8 characters.'); return res.redirect('/forgot-password/new-password'); }
  if (new_password !== confirm_password) { req.flash('error', 'Passwords do not match.'); return res.redirect('/forgot-password/new-password'); }
  const hash = await bcrypt.hash(new_password, 10);
  await User.update(req.session.fpUserId, { password: hash });
  delete req.session.fpEmail; delete req.session.fpVerified; delete req.session.fpUserId;
  req.flash('success', 'Password reset successfully. You can now sign in.');
  return res.redirect('/login');
};

// GET /forgot-password/resend
exports.resendForgotOtp = async (req, res) => {
  const email = req.session.fpEmail;
  if (!email) return res.redirect('/forgot-password');
  const db = require('../config/db');
  const [[user]] = await db.query('SELECT id, full_name FROM users WHERE email = ?', [email]);
  if (user) {
    const otp     = String(Math.floor(100000 + Math.random() * 900000));
    const expires = new Date(Date.now() + 15 * 60 * 1000).toISOString().slice(0, 19).replace('T', ' ');
    await User.update(user.id, { verify_token: otp, verify_token_expires: expires });
    await sendOtpEmail(email, user.full_name, otp, 'BIS Password Reset', 'Your new reset code is:');
  }
  req.flash('success', 'A new reset code has been sent.');
  return res.redirect('/forgot-password/verify');
};

// Pending accounts
exports.getPendingAccounts = async (req, res) => {
  const pending = await User.getPending();
  res.render(`dashboard/${req.session.role}/pending_accounts`, { title: 'Pending Accounts', pending });
};

exports.approveAccount = async (req, res) => {
  await User.approve(parseInt(req.params.id));
  req.flash('success', 'Account approved.');
  return res.redirect(`/${req.session.role}/pending-accounts`);
};

exports.rejectAccount = async (req, res) => {
  await User.reject(parseInt(req.params.id));
  req.flash('success', 'Account rejected.');
  return res.redirect(`/${req.session.role}/pending-accounts`);
};

// Create official account
exports.createOfficialAccount = async (req, res) => {
  const callerRole = req.session.role;
  const { role, full_name, email, username, password, confirm_password, household_no } = req.body;
  const allowedByRole = { secretary: ['captain', 'resident', 'sk'], captain: ['secretary', 'treasurer'] };
  if (!(allowedByRole[callerRole] || []).includes(role)) {
    req.flash('error', 'Invalid role.'); return res.redirect(`/${callerRole}/create-account`);
  }
  if (['captain', 'secretary'].includes(role)) {
    const existing = await User.getActiveByRole(role);
    if (existing) {
      req.flash('error', `An active ${role} account already exists (${existing.full_name}).`);
      return res.redirect(`/${callerRole}/create-account`);
    }
  }
  if (password !== confirm_password) { req.flash('error', 'Passwords do not match.'); return res.redirect(`/${callerRole}/create-account`); }
  if (password.length < 8) { req.flash('error', 'Password must be at least 8 characters.'); return res.redirect(`/${callerRole}/create-account`); }
  try {
    await User.create({ full_name, email, username, password, role, status: 'active', email_verified: 1, household_no: role === 'resident' ? household_no : null });
    req.flash('success', `${role.charAt(0).toUpperCase() + role.slice(1)} account created.`);
  } catch (e) {
    req.flash('error', e.message || 'Failed to create account.');
  }
  return res.redirect(`/${callerRole}/create-account`);
};
