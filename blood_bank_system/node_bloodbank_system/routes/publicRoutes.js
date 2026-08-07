const express = require('express');
const publicController = require('../controllers/publicController');
const router = express.Router();

router.get('/', publicController.home);
router.get('/availability', publicController.availability);

module.exports = router;
