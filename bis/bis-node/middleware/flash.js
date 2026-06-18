/**
 * Flash message middleware — exposes flash messages as res.locals
 * so every EJS view can access them without explicit passing.
 */
function flashLocals(req, res, next) {
  res.locals.success  = req.flash('success')[0]  || null;
  res.locals.error    = req.flash('error')[0]    || null;
  res.locals.info     = req.flash('info')[0]     || null;
  res.locals.warning  = req.flash('warning')[0]  || null;
  // Extra named flash keys used in the app
  res.locals.pw_error      = req.flash('pw_error')[0]      || null;
  res.locals.pw_otp_sent   = req.flash('pw_otp_sent')[0]   || null;
  res.locals.cal_success   = req.flash('cal_success')[0]   || null;
  res.locals.cal_error     = req.flash('cal_error')[0]     || null;
  res.locals.blotter_error = req.flash('blotter_error')[0] || null;
  res.locals.reset_error   = req.flash('reset_error')[0]   || null;
  // Current user available in all views
  res.locals.user = req.session && req.session.user ? req.session.user : null;
  next();
}

module.exports = flashLocals;
