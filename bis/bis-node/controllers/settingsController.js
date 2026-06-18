const User   = require('../models/User');
const bcrypt = require('bcryptjs');
const multer = require('multer');
const path   = require('path');
const fs     = require('fs');

const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    const dir = path.join(__dirname, '../public/uploads/avatars');
    if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
    cb(null, dir);
  },
  filename: (req, file, cb) => {
    const ext = path.extname(file.originalname).toLowerCase();
    cb(null, `avatar_${req.session.userId}_${Date.now()}${ext}`);
  },
});
const upload = multer({ storage, limits: { fileSize: 2 * 1024 * 1024 } });
exports.uploadMiddleware = upload.single('avatar');

exports.updateProfile = async (req, res) => {
  const role = req.session.role;
  const { full_name, email, contact_number } = req.body;
  await User.update(req.session.userId, { full_name, email, contact_number: contact_number || null });
  req.session.fullName = full_name;
  req.flash('success', 'Profile updated.');
  return res.redirect(`/${role}/settings`);
};

exports.uploadAvatar = async (req, res) => {
  const role = req.session.role;
  if (!req.file) { req.flash('error', 'No file uploaded.'); return res.redirect(`/${role}/settings`); }
  const avatarPath = `/uploads/avatars/${req.file.filename}`;
  await User.update(req.session.userId, { avatar: avatarPath });
  req.session.avatar = avatarPath;
  req.flash('success', 'Avatar updated.');
  return res.redirect(`/${role}/settings`);
};

// OTP-based password change
exports.requestPasswordOtp = async (req, res) => {
  const role = req.session.role;
  const user = await User.findById(req.session.userId);
  const otp     = String(Math.floor(100000 + Math.random() * 900000));
  const expires = new Date(Date.now() + 15 * 60 * 1000).toISOString().slice(0, 19).replace('T', ' ');
  await User.update(user.id, { verify_token: otp, verify_token_expires: expires });
  // In dev just flash the OTP; in prod send email
  req.flash('success', `OTP sent to your email. (Dev: ${otp})`);
  return res.redirect(`/${role}/settings`);
};

exports.verifyPasswordOtp = async (req, res) => {
  const role = req.session.role;
  const { otp } = req.body;
  const user = await User.findById(req.session.userId);
  if (!user || user.verify_token !== otp.trim()) {
    req.flash('error', 'Incorrect OTP.'); return res.redirect(`/${role}/settings`);
  }
  if (new Date(user.verify_token_expires) < new Date()) {
    req.flash('error', 'OTP expired.'); return res.redirect(`/${role}/settings`);
  }
  req.session.pwdOtpVerified = true;
  req.flash('success', 'OTP verified. You may now change your password.');
  return res.redirect(`/${role}/settings`);
};

exports.changePassword = async (req, res) => {
  const role = req.session.role;
  if (!req.session.pwdOtpVerified) {
    req.flash('error', 'Please verify your OTP first.'); return res.redirect(`/${role}/settings`);
  }
  const { new_password, confirm_password } = req.body;
  if (new_password.length < 8) { req.flash('error', 'Password must be at least 8 characters.'); return res.redirect(`/${role}/settings`); }
  if (new_password !== confirm_password) { req.flash('error', 'Passwords do not match.'); return res.redirect(`/${role}/settings`); }
  const hash = await bcrypt.hash(new_password, 10);
  await User.update(req.session.userId, { password: hash, verify_token: null, verify_token_expires: null });
  delete req.session.pwdOtpVerified;
  req.flash('success', 'Password changed successfully.');
  return res.redirect(`/${role}/settings`);
};

exports.adminResetPassword = async (req, res) => {
  const role = req.session.role;
  const { new_password } = req.body;
  if (!new_password || new_password.length < 8) {
    req.flash('error', 'Password must be at least 8 characters.'); return res.redirect(`/${role}/settings`);
  }
  const hash = await bcrypt.hash(new_password, 10);
  await User.update(parseInt(req.params.userId), { password: hash });
  req.flash('success', 'Password reset.');
  return res.redirect(`/${role}/settings`);
};

exports.deactivateUser = async (req, res) => {
  await User.update(parseInt(req.params.userId), { status: 'inactive' });
  req.flash('success', 'User deactivated.');
  return res.redirect(`/${req.session.role}/settings`);
};
