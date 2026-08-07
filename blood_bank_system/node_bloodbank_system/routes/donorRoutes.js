const express = require('express');
const donor = require('../controllers/donorController');
const { requireRole } = require('../middleware/auth');
const router = express.Router();

router.use(requireRole('donor'));
router.get('/request', donor.create);
router.post('/request', donor.store);

module.exports = router;
