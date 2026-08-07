const MedicalFacility = require('../models/MedicalFacility');
const BloodInventory = require('../models/BloodInventory');
const BloodRequest = require('../models/BloodRequest');
const DonationRequest = require('../models/DonationRequest');
const Donation = require('../models/Donation');
const { BLOOD_TYPES, BLOOD_COMPONENTS, FACILITY_OPTIONS, FACILITY_NAMES } = require('../config/constants');
const { redirectBackWithErrors } = require('../helpers/flash');

async function requestBlood(req, res) {
  res.render('facility/request-blood', { title: 'Request Blood', bloodTypes: BLOOD_TYPES, components: BLOOD_COMPONENTS, facilities: FACILITY_OPTIONS });
}

async function storeBloodRequest(req, res, next) {
  try {
    const errors = validateBloodRequest(req.body);
    if (errors.length) return redirectBackWithErrors(req, res, errors, '/facility/request-blood');
    await BloodRequest.create({ ...req.body, requester_id: req.session.user.id, units: Number(req.body.units) });
    req.flash('success', 'Blood request sent to ' + req.body.facility_name + '.');
    return res.redirect('/dashboard');
  } catch (error) {
    return next(error);
  }
}

async function facilityRequests(req, res, next) {
  try {
    const facility = await MedicalFacility.findByUserId(req.session.user.id);
    const requests = await BloodRequest.incomingForFacility(facility.facility_name, req.session.user.id);
    return res.render('facility/facility-requests', { title: 'Facility Requests', facility, requests });
  } catch (error) {
    return next(error);
  }
}

async function updateAddressedRequest(req, res, next) {
  try {
    const facility = await MedicalFacility.findByUserId(req.session.user.id);
    const request = await BloodRequest.findById(req.params.id);
    if (!request || request.facility_name !== facility.facility_name) return res.status(403).render('error', { title: 'Forbidden', status: 403, message: 'You cannot update this request.' });
    if (['approved', 'rejected'].includes(request.status)) return redirectBackWithErrors(req, res, 'This request is already final and cannot be changed.', '/facility/requests');
    if (!['approved', 'rejected'].includes(req.body.status)) return redirectBackWithErrors(req, res, 'Select a valid status.', '/facility/requests');
    await BloodRequest.updateStatus(request.id, { status: req.body.status, admin_note: req.body.admin_note });
    req.flash('success', 'Request updated.');
    return res.redirect('/facility/requests');
  } catch (error) {
    return next(error);
  }
}

async function donorRequests(req, res, next) {
  try {
    const facility = await MedicalFacility.findByUserId(req.session.user.id);
    const requests = await DonationRequest.byFacilityName(facility.facility_name);
    return res.render('facility/donor-requests', {
      title: 'Donor Requests',
      pendingRequests: requests.filter((item) => item.status === 'pending'),
      scheduledRequests: requests.filter((item) => item.status === 'approved'),
      rejectedRequests: requests.filter((item) => item.status === 'rejected')
    });
  } catch (error) {
    return next(error);
  }
}

async function scheduleDonor(req, res, next) {
  try {
    const facility = await MedicalFacility.findByUserId(req.session.user.id);
    const request = await DonationRequest.findById(req.params.id);
    if (!request || request.facility_name !== facility.facility_name) return res.status(403).render('error', { title: 'Forbidden', status: 403, message: 'You cannot update this donation request.' });
    if (request.status !== 'pending') return redirectBackWithErrors(req, res, 'This donor request already has a final decision and cannot be changed.', '/facility/donor-requests');
    const errors = [];
    if (!['approved', 'rejected'].includes(req.body.status)) errors.push('Select a valid status.');
    if (req.body.status === 'approved' && (!req.body.scheduled_date || !req.body.start_time || !req.body.end_time)) errors.push('Approved requests need a date, start time, and end time.');
    if (req.body.status === 'approved' && req.body.start_time >= req.body.end_time) errors.push('End time must be after start time.');
    if (errors.length) return redirectBackWithErrors(req, res, errors, '/facility/donor-requests');
    await DonationRequest.updateSchedule(request.id, req.body);
    if (req.body.status === 'approved') {
      await Donation.saveFromDonationRequest(request, req.body);
    } else {
      await Donation.deleteForDonationRequest(request);
    }

    req.flash('success', req.body.status === 'approved' ? 'Donor schedule saved and donation record auto-saved.' : 'Donor request rejected.');
    return res.redirect('/facility/donor-requests');
  } catch (error) {
    return next(error);
  }
}

async function inventory(req, res, next) {
  try {
    const facility = await MedicalFacility.findByUserId(req.session.user.id);
    const rows = await BloodInventory.byFacility(facility.id);
    const unitsByType = {};
    for (const type of BLOOD_TYPES) {
      unitsByType[type] = {};
      for (const component of BLOOD_COMPONENTS) unitsByType[type][component] = 0;
    }
    for (const row of rows) {
      if (unitsByType[row.blood_type] && Object.prototype.hasOwnProperty.call(unitsByType[row.blood_type], row.component)) {
        unitsByType[row.blood_type][row.component] += Number(row.units_available);
      }
    }
    return res.render('facility/inventory', { title: 'Inventory', facility, bloodTypes: BLOOD_TYPES, components: BLOOD_COMPONENTS, unitsByType });
  } catch (error) {
    return next(error);
  }
}

async function storeInventory(req, res, next) {
  try {
    const facility = await MedicalFacility.findByUserId(req.session.user.id);
    let addedTotal = 0;
    for (const type of BLOOD_TYPES) {
      for (const component of BLOOD_COMPONENTS) {
        const key = type + '|' + component;
        const toAdd = Number(req.body.add_units?.[key] || 0);
        if (Number.isInteger(toAdd) && toAdd > 0) {
          await BloodInventory.firstOrCreateAndIncrement({ medical_facility_id: facility.id, facility_name: facility.facility_name, blood_type: type, component }, toAdd);
          addedTotal += toAdd;
        }
      }
    }
    if (addedTotal === 0) return redirectBackWithErrors(req, res, 'Enter at least one unit to add before saving.', '/facility/inventory');
    req.flash('success', 'Availability saved. Added ' + addedTotal + ' unit(s) to your inventory.');
    return res.redirect('/facility/inventory');
  } catch (error) {
    return next(error);
  }
}

function validateBloodRequest(body) {
  const errors = [];
  if (!Object.keys(FACILITY_OPTIONS).includes(body.facility_category)) errors.push('Select a valid facility category.');
  if (!FACILITY_NAMES.includes(body.facility_name)) errors.push('Select a valid facility name.');
  if (!BLOOD_TYPES.includes(body.blood_type)) errors.push('Select a valid blood type.');
  if (!BLOOD_COMPONENTS.includes(body.component)) errors.push('Select a valid blood component.');
  const units = Number(body.units);
  if (!Number.isInteger(units) || units < 1) errors.push('Units must be at least 1.');
  if (!['low', 'medium', 'high', 'critical'].includes(body.urgency)) errors.push('Select a valid urgency.');
  return errors;
}

module.exports = { requestBlood, storeBloodRequest, facilityRequests, updateAddressedRequest, donorRequests, scheduleDonor, inventory, storeInventory };
