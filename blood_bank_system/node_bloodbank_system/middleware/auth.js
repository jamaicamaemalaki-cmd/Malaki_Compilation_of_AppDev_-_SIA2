function ensureGuest(req, res, next) {
  if (req.session.user) return res.redirect('/dashboard');
  return next();
}

function requireAuth(req, res, next) {
  if (!req.session.user) {
    req.flash('error', 'Please log in first.');
    return res.redirect('/login');
  }
  return next();
}

function requireRole(role) {
  return (req, res, next) => {
    if (!req.session.user) {
      req.flash('error', 'Please log in first.');
      return res.redirect('/login');
    }
    if (req.session.user.role !== role) {
      return res.status(403).render('error', {
        title: 'Forbidden',
        status: 403,
        message: 'You do not have access to this page.'
      });
    }
    return next();
  };
}

module.exports = { ensureGuest, requireAuth, requireRole };
