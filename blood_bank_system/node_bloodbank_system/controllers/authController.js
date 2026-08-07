const bcrypt = require('bcryptjs');
const User = require('../models/User');
const Donor = require('../models/Donor');
const MedicalFacility = require('../models/MedicalFacility');
const { BLOOD_TYPES, FACILITY_OPTIONS, FACILITY_NAMES } = require('../config/constants');
const { redirectBackWithErrors } = require('../helpers/flash');

function showLogin(req, res) {
  res.render('auth/login', { title: 'Login', onLoginPage: true });
}

function chooseRole(req, res) {
  res.render('auth/choose-role', { title: 'Create Account' });
}

function showRegister(req, res) {
  const role = req.params.role;
  if (!['donor', 'facility', 'admin'].includes(role)) return res.status(404).render('error', { title: 'Not Found', status: 404, message: 'Role not found.' });
  return res.render('auth/register', { title: 'Register', role, bloodTypes: BLOOD_TYPES, facilities: FACILITY_OPTIONS });
}

async function login(req, res, next) {
  try {
    const email = String(req.body.email || '').trim().toLowerCase();
    const password = String(req.body.password || '');
    const trimmedPassword = password.trim();
    if (!email || !password) return redirectBackWithErrors(req, res, 'Email and password are required.', '/login');

    const user = await User.findByEmail(email);
    const passwordMatches = user && (
      await bcrypt.compare(password, user.password)
      || (trimmedPassword !== password && await bcrypt.compare(trimmedPassword, user.password))
    );

    if (!user || !passwordMatches) {
      return redirectBackWithErrors(req, res, 'Invalid email or password.', '/login');
    }

    req.session.regenerate((error) => {
      if (error) return next(error);
      req.session.user = { id: user.id, name: user.name, email: user.email, role: user.role };
      req.flash('success', 'Welcome back, ' + user.name + '.');
      return res.redirect('/dashboard');
    });
  } catch (error) {
    return next(error);
  }
}

async function register(req, res, next) {
  try {
    const role = req.params.role;
    if (!['donor', 'facility', 'admin'].includes(role)) return res.status(404).render('error', { title: 'Not Found', status: 404, message: 'Role not found.' });

    const errors = validateRegistration(role, req.body);
    if (errors.length) return redirectBackWithErrors(req, res, errors, '/register/' + role);

    const existing = await User.findByEmail(req.body.email);
    if (existing) return redirectBackWithErrors(req, res, 'Email is already registered.', '/register/' + role);

    const passwordHash = await bcrypt.hash(req.body.password, 10);
    const user = await User.create({
      name: req.body.name.trim(),
      email: req.body.email.trim().toLowerCase(),
      password: passwordHash,
      role,
      phone: req.body.phone,
      address: req.body.address
    });

    if (role === 'donor') {
      await Donor.create({
        user_id: user.id,
        blood_type: req.body.blood_type,
        age: Number(req.body.age),
        gender: req.body.gender,
        weight: req.body.weight ? Number(req.body.weight) : null,
        declaration_confirmed: true,
        medical_notes: req.body.medical_notes
      });
    }

    if (role === 'facility') {
      await MedicalFacility.create({
        user_id: user.id,
        facility_category: req.body.facility_category,
        facility_name: req.body.facility_name,
        license_number: req.body.license_number,
        contact_person: req.body.contact_person
      });
    }

    req.session.regenerate((error) => {
      if (error) return next(error);
      req.session.user = { id: user.id, name: user.name, email: user.email, role: user.role };
      req.flash('success', 'Account created successfully.');
      return res.redirect('/dashboard');
    });
  } catch (error) {
    return next(error);
  }
}

function logout(req, res, next) {
  req.session.destroy((error) => {
    if (error) return next(error);
    res.clearCookie('bloodlink_sid');
    return res.redirect('/login');
  });
}

function validateRegistration(role, body) {
  const errors = [];
  if (!body.name || body.name.trim().length > 120) errors.push('Name is required and must be 120 characters or less.');
  if (!body.email || !body.email.includes('@')) errors.push('A valid email is required.');
  if (!body.password || body.password.length < 6) errors.push('Password must be at least 6 characters.');
  if (role === 'donor') {
    const age = Number(body.age);
    const weight = body.weight ? Number(body.weight) : null;
    if (!BLOOD_TYPES.includes(body.blood_type)) errors.push('Select a valid blood type.');
    if (!Number.isInteger(age) || age < 18 || age > 65) errors.push('Donor age must be 18 to 65.');
    if (!body.gender) errors.push('Gender is required.');
    if (weight !== null && weight < 40) errors.push('Weight must be at least 40 kg.');
    if (!body.declaration_confirmed) errors.push('Please confirm the donor declaration.');
  }
  if (role === 'facility') {
    if (!Object.keys(FACILITY_OPTIONS).includes(body.facility_category)) errors.push('Select a valid facility category.');
    if (!FACILITY_NAMES.includes(body.facility_name)) errors.push('Select a valid facility name.');
  }
  return errors;
}

module.exports = { showLogin, chooseRole, showRegister, login, register, logout };
