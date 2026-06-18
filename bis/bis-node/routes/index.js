const express  = require('express');
const router   = express.Router();
const ui       = require('../controllers/uiController');
const auth     = require('../controllers/authController');
const blotter  = require('../controllers/blotterController');

router.get('/',               ui.home);
router.get('/index',          ui.home);
router.get('/faqs',           ui.faqs);
router.get('/privacy-policy', ui.privacyPolicy);
router.get('/terms',          ui.terms);
router.get('/select-role',    ui.selectRole);

// Public blotter
router.post('/public/blotter/store', blotter.storePublic);

module.exports = router;
