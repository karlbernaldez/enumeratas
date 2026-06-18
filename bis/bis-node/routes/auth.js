const express = require('express');
const router  = express.Router();
const auth    = require('../controllers/authController');

router.get('/login',  auth.getLogin);
router.post('/login', auth.postLogin);
router.get('/logout', auth.logout);

router.get('/signup',        auth.getSignup);
router.post('/signup/store', auth.postSignup);

router.get('/verify-email',  auth.getVerifyEmail);
router.post('/verify-email', auth.postVerifyEmail);
router.get('/resend-otp',    auth.resendOtp);

router.get('/forgot-password',                auth.getForgotPassword);
router.post('/forgot-password',               auth.postForgotPassword);
router.get('/forgot-password/verify',         auth.getForgotPasswordOtp);
router.post('/forgot-password/verify',        auth.postForgotPasswordOtp);
router.get('/forgot-password/resend',         auth.resendForgotOtp);
router.get('/forgot-password/new-password',   auth.getNewPassword);
router.post('/forgot-password/reset',         auth.postNewPassword);

module.exports = router;
