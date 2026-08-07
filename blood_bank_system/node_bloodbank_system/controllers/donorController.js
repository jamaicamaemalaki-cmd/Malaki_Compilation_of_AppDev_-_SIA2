const Donor = require('../models/Donor');
const DonationRequest = require('../models/DonationRequest');
const { BLOOD_COMPONENTS, FACILITY_OPTIONS, FACILITY_NAMES } = require('../config/constants');
const { redirectBackWithErrors } = require('../helpers/flash');

function create(req, res) {
  return res.render('donor/request', { title: 'Request Donation Schedule', components: BLOOD_COMPONENTS, facilities: FACILITY_OPTIONS });
}

async function store(req, res, next) {
  try {
    const errors = [];
    if (!Object.keys(FACILITY_OPTIONS).includes(req.body.facility_category)) errors.push('Select a valid facility category.');
    if (!FACILITY_NAMES.includes(req.body.facility_name)) errors.push('Select a valid facility name.');
    if (!BLOOD_COMPONENTS.includes(req.body.component)) errors.push('Select a valid blood component.');
    const units = Number(req.body.units);
    if (!Number.isInteger(units) || units < 1 || units > 2) errors.push('Donation units must be 1 or 2.');
    if (errors.length) return redirectBackWithErrors(req, res, errors, '/donor/request');

    const donor = await Donor.findByUserId(req.session.user.id);
    await DonationRequest.create({
      donor_id: donor.id,
      blood_type: donor.blood_type,
      facility_category: req.body.facility_category,
      facility_name: req.body.facility_name,
      component: req.body.component,
      units,
      donor_note: req.body.donor_note
    });
    req.flash('success', 'Donation request submitted. The facility will set the schedule.');
    return res.redirect('/dashboard');
  } catch (error) {
    return next(error);
  }
}

module.exports = { create, store };
