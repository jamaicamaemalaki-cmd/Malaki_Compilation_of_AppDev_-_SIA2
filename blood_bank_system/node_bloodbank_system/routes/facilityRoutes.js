const express = require('express');
const facility = require('../controllers/facilityController');
const { requireRole } = require('../middleware/auth');
const router = express.Router();

router.use(requireRole('facility'));
router.get('/request-blood', facility.requestBlood);
router.post('/request-blood', facility.storeBloodRequest);
router.get('/requests', facility.facilityRequests);
router.patch('/addressed-requests/:id', facility.updateAddressedRequest);
router.get('/donor-requests', facility.donorRequests);
router.patch('/donor-requests/:id', facility.scheduleDonor);
router.get('/inventory', facility.inventory);
router.post('/inventory', facility.storeInventory);

module.exports = router;
