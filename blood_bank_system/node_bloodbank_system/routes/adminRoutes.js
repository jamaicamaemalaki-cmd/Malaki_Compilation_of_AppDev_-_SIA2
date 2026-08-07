const express = require('express');
const admin = require('../controllers/adminController');
const { requireRole } = require('../middleware/auth');
const router = express.Router();

router.get('/create-default', admin.seedAdmin);
router.get('/requests', requireRole('admin'), admin.requests);
router.get('/reports', requireRole('admin'), admin.reports);

module.exports = router;
