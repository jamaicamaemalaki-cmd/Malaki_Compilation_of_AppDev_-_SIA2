require('dotenv').config();

const path = require('path');
const express = require('express');
const session = require('express-session');
const flash = require('connect-flash');
const methodOverride = require('method-override');
const expressLayouts = require('express-ejs-layouts');
const MySQLStore = require('express-mysql-session')(session);
const { sessionStoreOptions } = require('./config/db');

const authRoutes = require('./routes/authRoutes');
const dashboardRoutes = require('./routes/dashboardRoutes');
const donorRoutes = require('./routes/donorRoutes');
const facilityRoutes = require('./routes/facilityRoutes');
const adminRoutes = require('./routes/adminRoutes');
const publicRoutes = require('./routes/publicRoutes');
const MedicalFacility = require('./models/MedicalFacility');
const BloodRequest = require('./models/BloodRequest');
const DonationRequest = require('./models/DonationRequest');

const app = express();

app.set('trust proxy', 1);
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));
app.use(expressLayouts);
app.set('layout', 'layouts/app');
app.locals.currentUser = null;
app.locals.success = [];
app.locals.error = [];
app.locals.validationErrors = [];
app.locals.formData = {};
app.locals.incomingBloodRequestCount = 0;
app.locals.pendingDonorRequestCount = 0;

app.use(express.static(path.join(__dirname, 'public')));
app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use(methodOverride('_method'));

const sessionOptions = {
  key: 'bloodlink_sid',
  secret: process.env.SESSION_SECRET || 'development_secret',
  resave: false,
  saveUninitialized: false,
  cookie: {
    httpOnly: true,
    secure: process.env.NODE_ENV === 'production',
    sameSite: 'lax',
    maxAge: 1000 * 60 * 60 * 4
  }
};

if (process.env.SESSION_DRIVER === 'mysql') {
  sessionOptions.store = new MySQLStore(sessionStoreOptions);
}

app.use(session(sessionOptions));

app.use(flash());

app.use((req, res, next) => {
  res.locals.currentUser = req.session.user || null;
  res.locals.success = req.flash('success');
  res.locals.error = req.flash('error');
  res.locals.validationErrors = req.flash('validationErrors');
  res.locals.formData = req.flash('formData')[0] || {};
  res.locals.incomingBloodRequestCount = 0;
  res.locals.pendingDonorRequestCount = 0;
  next();
});

app.use(async (req, res, next) => {
  try {
    if (req.session.user?.role === 'facility') {
      const facility = await MedicalFacility.findByUserId(req.session.user.id);
      if (facility?.facility_name) {
        const [incomingBlood, pendingDonors] = await Promise.all([
          BloodRequest.countIncomingForFacility(facility.facility_name, req.session.user.id),
          DonationRequest.countPendingByFacilityName(facility.facility_name)
        ]);
        res.locals.incomingBloodRequestCount = incomingBlood;
        res.locals.pendingDonorRequestCount = pendingDonors;
      }
    }
    next();
  } catch (error) {
    console.warn('Skipping facility notification counts:', error.message);
    next();
  }
});

app.use('/', publicRoutes);
app.use('/', authRoutes);
app.use('/dashboard', dashboardRoutes);
app.use('/donor', donorRoutes);
app.use('/facility', facilityRoutes);
app.use('/admin', adminRoutes);

app.use((req, res) => {
  res.status(404).render('error', {
    title: 'Page Not Found',
    status: 404,
    message: 'The page you requested could not be found.'
  });
});

app.use((err, req, res, next) => {
  console.error(err);
  res.status(err.status || 500).render('error', {
    title: 'Server Error',
    status: err.status || 500,
    message: process.env.NODE_ENV === 'production' ? 'Something went wrong.' : err.message
  });
});

module.exports = app;
