const express  = require('express');
const router   = express.Router();
const { requireRole } = require('../middleware/auth');
const ui       = require('../controllers/uiController');
const sk       = require('../controllers/skController');
const settings = require('../controllers/settingsController');

router.use(requireRole('sk'));

router.get('/dashboard',                    ui.skDashboard);
router.get('/profiling',                    sk.profiling);
router.get('/household/:householdNo',       ui.skHousehold);
router.get('/programs',                     ui.skPrograms);
router.get('/reports',                      ui.skReports);
router.get('/settings',                     ui.skSettings);
router.post('/settings/request-otp',        settings.requestPasswordOtp);
router.post('/settings/verify-otp',         settings.verifyPasswordOtp);
router.post('/settings/change-password',    settings.changePassword);
router.post('/settings/profile',            settings.updateProfile);
router.post('/settings/avatar',             settings.uploadMiddleware, settings.uploadAvatar);

module.exports = router;
