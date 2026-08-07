const User = require('../models/User');
const Donor = require('../models/Donor');
const MedicalFacility = require('../models/MedicalFacility');
const BloodInventory = require('../models/BloodInventory');
const BloodRequest = require('../models/BloodRequest');
const DonationRequest = require('../models/DonationRequest');
const Donation = require('../models/Donation');

async function index(req, res, next) {
  try {
    if (req.session.user.role === 'admin') return await admin(req, res);
    if (req.session.user.role === 'donor') return await donor(req, res);
    if (req.session.user.role === 'facility') return await facility(req, res);
    return res.redirect('/login');
  } catch (error) {
    return next(error);
  }
}

async function admin(req, res) {
  const threshold = Number(process.env.LOW_STOCK_THRESHOLD || 3);
  const [users, donors, facilities, inventoryUnits, pendingRequests, requests, donations, lowStocks] = await Promise.all([
    User.countAll(),
    Donor.countAll(),
    MedicalFacility.countAll(),
    BloodInventory.sumUnits(),
    BloodRequest.countPending(),
    BloodRequest.latest(8),
    Donation.latest(8),
    BloodInventory.allLowStock(threshold)
  ]);
  return res.render('admin/dashboard', { title: 'Admin Dashboard', users, donors, facilities, inventoryUnits, pendingRequests, requests, donations, lowStocks });
}

async function donor(req, res) {
  const donorRecord = await Donor.findByUserId(req.session.user.id);
  const [requests, inventories] = await Promise.all([
    DonationRequest.byDonor(donorRecord.id),
    BloodInventory.available()
  ]);
  return res.render('donor/dashboard', { title: 'Donor Dashboard', donor: donorRecord, requests, inventories });
}

async function facility(req, res) {
  const facilityRecord = await MedicalFacility.findByUserId(req.session.user.id);
  const [bloodRequests, incomingFacilityRequests, donorRequests, inventories] = await Promise.all([
    BloodRequest.byRequester(req.session.user.id),
    BloodRequest.incomingForFacility(facilityRecord.facility_name, req.session.user.id),
    DonationRequest.byFacilityName(facilityRecord.facility_name),
    BloodInventory.byFacility(facilityRecord.id)
  ]);
  return res.render('facility/dashboard', {
    title: 'Facility Dashboard',
    facility: facilityRecord,
    bloodRequests,
    incomingFacilityRequests,
    donorRequests,
    pendingDonorRequests: donorRequests.filter((item) => item.status === 'pending').length,
    inventories
  });
}

module.exports = { index };
