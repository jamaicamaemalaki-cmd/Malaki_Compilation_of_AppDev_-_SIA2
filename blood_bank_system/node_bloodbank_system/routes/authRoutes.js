const express = require('express');
const auth = require('../controllers/authController');
const { ensureGuest } = require('../middleware/auth');
const router = express.Router();

router.get('/login', ensureGuest, auth.showLogin);
router.post('/login', ensureGuest, auth.login);
router.get('/register', ensureGuest, auth.chooseRole);
router.get('/register/:role', ensureGuest, auth.showRegister);
router.post('/register/:role', ensureGuest, auth.register);
router.post('/logout', auth.logout);

module.exports = router;
