const bcrypt = require('bcryptjs');
const User = require('../models/User');
const Donor = require('../models/Donor');
const MedicalFacility = require('../models/MedicalFacility');
const BloodInventory = require('../models/BloodInventory');
const BloodRequest = require('../models/BloodRequest');
const Donation = require('../models/Donation');

async function seedAdmin(req, res, next) {
  try {
    const adminPassword = await bcrypt.hash('admin123', 10);
    await User.upsertByEmail('admin@bloodlink.test', { name: 'BloodLink Admin', password: adminPassword, role: 'admin' });

    const facilities = [
      { email: 'hch@bloodlink.test', name: 'Hinunangan Community Hospital', category: 'Hospital' },
      { email: 'ztlmh@bloodlink.test', name: 'Zenon T. Lagumbay Memorial Hospital', category: 'Hospital' },
      { email: 'hrhu@bloodlink.test', name: 'Hinunangan Rural Health Unit', category: 'Rural Health Unit' },
      { email: 'prc@bloodlink.test', name: 'Philippine Red Cross-Southern Leyte Chapter', category: 'Red Cross' }
    ];
    const facilityPassword = await bcrypt.hash('facility123', 10);
    for (const item of facilities) {
      const user = await User.upsertByEmail(item.email, { name: item.name, password: facilityPassword, role: 'facility' });
      await MedicalFacility.firstOrCreateByUserId(user.id, {
        facility_category: item.category,
        facility_name: item.name,
        license_number: 'FIXED-LICENSE-' + item.email.slice(0, 3).toUpperCase(),
        contact_person: item.name
      });
    }
    req.flash('success', 'Admin and facility accounts ready. Admin: admin@bloodlink.test / admin123. Facilities use facility123.');
    return res.redirect('/login');
  } catch (error) {
    return next(error);
  }
}

async function requests(req, res, next) {
  try {
    const requests = await BloodRequest.all();
    return res.render('admin/requests', { title: 'All Blood Requests', requests });
  } catch (error) {
    return next(error);
  }
}

async function reports(req, res, next) {
  try {
    const year = new Date().getFullYear();
    const [donors, facilities, inventory, donations, usersMonthly, requestsMonthly] = await Promise.all([
      Donor.allWithUsers(),
      MedicalFacility.allWithUsers(),
      BloodInventory.latest(5),
      Donation.allWithDonors(),
      User.monthlyCounts(year),
      BloodRequest.monthlyCounts(year)
    ]);
    const usersChartData = fillMonthly(usersMonthly);
    const requestsChartData = fillMonthly(requestsMonthly);
    return res.render('admin/reports', { title: 'Reports', donors, facilities, inventory, donations, usersChartData, requestsChartData });
  } catch (error) {
    return next(error);
  }
}

function fillMonthly(rows) {
  const result = Array(12).fill(0);
  for (const row of rows) result[Number(row.month) - 1] = Number(row.total);
  return result;
}

module.exports = { seedAdmin, requests, reports };
