require('dotenv').config();
const express        = require('express');
const session        = require('express-session');
const MySQLStore     = require('express-mysql-session')(session);
const flash          = require('connect-flash');
const methodOverride = require('method-override');
const path           = require('path');
const fs             = require('fs');

const app = express();

// ── View engine ───────────────────────────────────────────────────────────────
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// ── Static files ──────────────────────────────────────────────────────────────
app.use(express.static(path.join(__dirname, 'public')));

// ── Body parsing ──────────────────────────────────────────────────────────────
app.use(express.urlencoded({ extended: true }));
app.use(express.json());

// ── Method override (for PUT/DELETE via forms) ────────────────────────────────
app.use(methodOverride('_method'));

// ── Session store ─────────────────────────────────────────────────────────────
const sessionStore = new MySQLStore({
  host:     process.env.DB_HOST     || 'localhost',
  port:     parseInt(process.env.DB_PORT || '3306'),
  database: process.env.DB_NAME     || 'bis_db',
  user:     process.env.DB_USER     || 'root',
  password: process.env.DB_PASS     || '',
});

app.use(session({
  key:               'bis_session',
  secret:            process.env.SESSION_SECRET || 'bis-secret',
  store:             sessionStore,
  resave:            false,
  saveUninitialized: false,
  cookie: { maxAge: 1000 * 60 * 60 * 8 }, // 8 hours
}));

// ── Flash messages ────────────────────────────────────────────────────────────
app.use(flash());

// ── Locals available in all views ─────────────────────────────────────────────
app.use((req, res, next) => {
  res.locals.session      = req.session;
  res.locals.success      = req.flash('success');
  res.locals.error        = req.flash('error');
  res.locals.currentYear  = new Date().getFullYear();
  res.locals.currentDate  = new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
  next();
});

// ── Routes ────────────────────────────────────────────────────────────────────
app.use('/',           require('./routes/index'));
app.use('/',           require('./routes/auth'));
app.use('/captain',    require('./routes/captain'));
app.use('/secretary',  require('./routes/secretary'));
app.use('/resident',   require('./routes/resident'));
app.use('/sk',         require('./routes/sk'));
app.use('/treasurer',  require('./routes/treasurer'));

// ── 404 ───────────────────────────────────────────────────────────────────────
app.use((req, res) => {
  res.status(404).render('errors/404', { title: 'Page Not Found' });
});

// ── 500 ───────────────────────────────────────────────────────────────────────
app.use((err, req, res, next) => {
  console.error(err.stack);
  res.status(500).render('errors/500', { title: 'Server Error', error: err.message });
});

// ── Start ─────────────────────────────────────────────────────────────────────
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`BIS Node.js running at http://localhost:${PORT}`);
});
