const express  = require('express');
const router   = express.Router();
const { requireRole } = require('../middleware/auth');
const ui       = require('../controllers/uiController');

router.use(requireRole('treasurer'));

router.get('/dashboard', ui.treasurerDashboard);

module.exports = router;
