function redirectBackWithErrors(req, res, errors, fallback = '/') {
  req.flash('validationErrors', Array.isArray(errors) ? errors : [errors]);
  req.flash('formData', req.body);
  return res.redirect(req.get('Referrer') || fallback);
}

module.exports = { redirectBackWithErrors };
