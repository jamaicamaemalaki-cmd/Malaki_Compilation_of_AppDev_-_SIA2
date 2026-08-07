const BloodInventory = require('../models/BloodInventory');
const { BLOOD_TYPES } = require('../config/constants');

async function home(req, res) {
  if (req.session.user) return res.redirect('/dashboard');
  return res.render('home', { title: 'BloodLink' });
}

async function availability(req, res, next) {
  try {
    const inventories = await BloodInventory.available();
    return res.render('availability', { title: 'Availability | BloodLink', inventories, bloodTypes: BLOOD_TYPES });
  } catch (error) {
    return next(error);
  }
}

module.exports = { home, availability };
