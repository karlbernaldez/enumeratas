const express   = require('express');
const router    = express.Router();
const { requireRole } = require('../middleware/auth');
const ui        = require('../controllers/uiController');
const clearance = require('../controllers/clearanceController');
const blotter   = require('../controllers/blotterController');
const settings  = require('../controllers/settingsController');

router.use(requireRole('resident'));

router.get('/dashboard',                    ui.residentDashboard);
router.get('/clearance',                    clearance.residentIndex);
router.post('/clearance/store',             clearance.store);
router.post('/clearance/cancel/:id',        clearance.cancel);
router.get('/profile',                      ui.residentProfile);
router.get('/notifications',                ui.residentNotifications);
router.post('/blotter/store',               blotter.store);
router.post('/settings/request-otp',        settings.requestPasswordOtp);
router.post('/settings/verify-otp',         settings.verifyPasswordOtp);
router.post('/settings/change-password',    settings.changePassword);
router.post('/settings/avatar',             settings.uploadMiddleware, settings.uploadAvatar);

module.exports = router;
