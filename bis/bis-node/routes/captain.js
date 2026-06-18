const express    = require('express');
const router     = express.Router();
const { requireRole } = require('../middleware/auth');
const ui         = require('../controllers/uiController');
const auth       = require('../controllers/authController');
const census     = require('../controllers/censusController');
const clearance  = require('../controllers/clearanceController');
const blotter    = require('../controllers/blotterController');
const schedule   = require('../controllers/scheduleController');
const settings   = require('../controllers/settingsController');

router.use(requireRole('captain'));

router.get('/dashboard',                    (req, res) => res.redirect('/captain/calendar'));
router.get('/calendar',                     schedule.index);
router.get('/calendar/events',              schedule.listAll);
router.get('/calendar/view/:id',            schedule.view);
router.post('/calendar/store',              schedule.store);
router.post('/calendar/update/:id',         schedule.update);
router.post('/calendar/delete/:id',         schedule.delete);

router.get('/census',                       ui.captainCensus);
router.get('/household/:householdNo',       ui.captainHousehold);
router.post('/census/store',                census.store);
router.post('/census/update/:householdNo',  census.updateHousehold);
router.post('/census/delete/:householdNo',  census.deleteHousehold);
router.post('/census/member/add/:householdNo',  census.addMember);
router.post('/census/member/update/:memberId',  census.updateMember);
router.post('/census/member/delete/:memberId',  census.deleteMember);

router.get('/clearance',                    clearance.adminIndex);
router.get('/clearance/request/:userId',    clearance.residentDetail);
router.post('/clearance/approve/:id',       clearance.approve);
router.post('/clearance/reject/:id',        clearance.reject);

router.get('/blotter',                      blotter.adminIndex);
router.get('/blotter/:id',                  blotter.show);
router.post('/blotter/status/:id',          blotter.updateStatus);
router.post('/blotter/summons/:id',         blotter.sendSummons);
router.post('/blotter/reschedule/:id',      blotter.reschedule);

router.get('/reports',                      ui.captainReports);
router.get('/settings',                     ui.captainSettings);
router.post('/settings/profile',            settings.updateProfile);
router.post('/settings/avatar',             settings.uploadMiddleware, settings.uploadAvatar);
router.post('/settings/request-otp',        settings.requestPasswordOtp);
router.post('/settings/verify-otp',         settings.verifyPasswordOtp);
router.post('/settings/change-password',    settings.changePassword);

router.get('/pending-accounts',             auth.getPendingAccounts);
router.post('/approve-account/:id',         auth.approveAccount);
router.post('/reject-account/:id',          auth.rejectAccount);

router.get('/create-account',               ui.captainCreateAccount);
router.post('/create-account/store',        auth.createOfficialAccount);

module.exports = router;
