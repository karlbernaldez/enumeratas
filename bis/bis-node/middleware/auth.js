/**
 * requireAuth — redirect to /login if no active session
 */
function requireAuth(req, res, next) {
  if (req.session && req.session.userId) return next();
  return res.redirect('/login');
}

/**
 * requireRole(...roles) — redirect to /login if role doesn't match
 */
function requireRole(...roles) {
  return (req, res, next) => {
    if (!req.session || !req.session.userId) return res.redirect('/login');
    if (!roles.includes(req.session.role)) return res.redirect('/login');
    next();
  };
}

module.exports = { requireAuth, requireRole };
