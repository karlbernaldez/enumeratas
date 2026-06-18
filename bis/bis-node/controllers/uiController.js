const Household        = require('../models/Household');
const HouseholdMember  = require('../models/HouseholdMember');
const ClearanceRequest = require('../models/ClearanceRequest');
const BlotterReport    = require('../models/BlotterReport');
const Schedule         = require('../models/Schedule');
const User             = require('../models/User');
const db               = require('../config/db');

// ── Public ────────────────────────────────────────────────────────────────────
exports.home          = (req, res) => res.render('index',            { title: 'Bacolod BIS' });
exports.getLogin      = (req, res) => res.redirect('/login');
exports.faqs          = (req, res) => res.render('faqs',             { title: 'FAQs' });
exports.privacyPolicy = (req, res) => res.render('privacy_policy',   { title: 'Privacy Policy' });
exports.terms         = (req, res) => res.render('terms',            { title: 'Terms of Use' });
exports.selectRole    = (req, res) => res.render('auth/select_role', { title: 'Select Role' });

// ── Resident Dashboard ────────────────────────────────────────────────────────
exports.residentDashboard = async (req, res) => {
  const userId = req.session.userId;
  const allRequests = await ClearanceRequest.getByUser(userId);
  const totalRequests = allRequests.length;
  const approved      = allRequests.filter(r => r.status === 'approved').length;
  const pending       = allRequests.filter(r => r.status === 'pending').length;
  const blotterCount  = await BlotterReport.countByUser(userId);
  const recentRequests = allRequests.slice(0, 5);
  res.render('dashboard/resident/index', {
    title: 'Resident Dashboard',
    totalRequests, approved, pending, blotterCount, recentRequests,
  });
};

exports.residentProfile = async (req, res) => {
  const userId = req.session.userId;
  const user   = await User.findById(userId);
  let household = null, members = [];
  if (user && user.household_no) {
    household = await Household.findByNo(user.household_no);
    members   = await HouseholdMember.getByHousehold(user.household_no);
  }
  res.render('dashboard/resident/profile', { title: 'My Profile', user, household, members });
};

exports.residentNotifications = (req, res) => {
  res.render('dashboard/resident/notifications', { title: 'Notifications' });
};

// ── Captain ───────────────────────────────────────────────────────────────────
exports.captainCalendar = async (req, res) => {
  const events = await Schedule.getAll({ role: 'captain' });
  res.render('dashboard/captain/index', { title: 'Dashboard', events });
};

exports.captainCensus = async (req, res) => {
  const filters = {
    zone:   req.query.zone   || '',
    search: req.query.search || '',
  };
  const page    = parseInt(req.query.page || 1);
  const perPage = 15;
  const stats   = await Household.getStats(filters.zone);
  const { rows: households, total } = await Household.findAll({ ...filters, page, perPage });
  res.render('dashboard/captain/census', {
    title: 'Census Records',
    households, filters, ...stats,
    totalHouseholdsFiltered: total, perPage, currentPage: page,
    persons: [], hasSpecialFilter: false, filteredTotal: total,
  });
};

exports.captainHousehold = async (req, res) => {
  const hh = await Household.findByNo(req.params.householdNo);
  if (!hh) { req.flash('error', 'Household not found.'); return res.redirect('/captain/census'); }
  const members = await HouseholdMember.getByHousehold(req.params.householdNo);
  res.render('dashboard/captain/household', { title: 'Household', household: hh, members, householdId: hh.household_no, role: 'captain' });
};

exports.captainReports = async (req, res) => {
  const data = await _buildReports();
  res.render('dashboard/captain/reports', { title: 'Reports', ...data });
};

exports.captainSettings = async (req, res) => {
  res.render('dashboard/captain/settings', { title: 'Settings' });
};

exports.captainCreateAccount = async (req, res) => {
  const activeCaptain   = await User.getActiveByRole('captain');
  const activeSecretary = await User.getActiveByRole('secretary');
  res.render('dashboard/captain/create_account', { title: 'Create Account', activeCaptain, activeSecretary });
};

// ── Secretary ─────────────────────────────────────────────────────────────────
exports.secretaryCalendar = async (req, res) => {
  const events = await Schedule.getAll({ role: 'secretary' });
  res.render('dashboard/secretary/index', { title: 'Dashboard', events });
};

exports.secretaryCensus = async (req, res) => {
  const filters = { zone: req.query.zone || '', search: req.query.search || '' };
  const page    = parseInt(req.query.page || 1);
  const perPage = 15;
  const stats   = await Household.getStats(filters.zone);
  const { rows: households, total } = await Household.findAll({ ...filters, page, perPage });
  res.render('dashboard/secretary/census', {
    title: 'Census Records',
    households, filters, ...stats,
    totalHouseholdsFiltered: total, perPage, currentPage: page,
    persons: [], hasSpecialFilter: false, filteredTotal: total,
  });
};

exports.secretaryHousehold = async (req, res) => {
  const hh = await Household.findByNo(req.params.householdNo);
  if (!hh) { req.flash('error', 'Household not found.'); return res.redirect('/secretary/census'); }
  const members = await HouseholdMember.getByHousehold(req.params.householdNo);
  res.render('dashboard/captain/household', { title: 'Household', household: hh, members, householdId: hh.household_no, role: 'secretary' });
};

exports.secretaryReports = async (req, res) => {
  const data = await _buildReports();
  res.render('dashboard/secretary/reports', { title: 'Reports', ...data });
};

exports.secretarySettings = async (req, res) => {
  const allUsers = await User.getAll();
  res.render('dashboard/secretary/settings', { title: 'Settings', allUsers });
};

exports.secretaryCreateAccount = async (req, res) => {
  const activeCaptain   = await User.getActiveByRole('captain');
  const activeSecretary = await User.getActiveByRole('secretary');
  res.render('dashboard/secretary/create_account', { title: 'Create Account', activeCaptain, activeSecretary });
};

// ── SK ────────────────────────────────────────────────────────────────────────
exports.skDashboard = (req, res) => res.render('dashboard/sk/index', { title: 'SK Dashboard' });
exports.skPrograms  = (req, res) => res.render('dashboard/sk/programs', { title: 'Programs & Events' });
exports.skReports   = (req, res) => res.render('dashboard/sk/reports', { title: 'SK Reports' });
exports.skSettings  = (req, res) => res.render('dashboard/sk/settings', { title: 'Settings' });

exports.skHousehold = async (req, res) => {
  const hh = await Household.findByNo(req.params.householdNo);
  if (!hh) { req.flash('error', 'Household not found.'); return res.redirect('/sk/profiling'); }
  const members = await HouseholdMember.getByHousehold(req.params.householdNo);
  res.render('dashboard/captain/household', { title: 'Household', household: hh, members, householdId: hh.household_no, role: 'sk' });
};

// ── Treasurer ─────────────────────────────────────────────────────────────────
exports.treasurerDashboard = (req, res) => res.render('dashboard/treasurer/index', { title: 'Treasurer Dashboard' });

// ── Reports helper ────────────────────────────────────────────────────────────
async function _buildReports() {
  const [heads]   = await db.query('SELECT date_of_birth, gender, civil_status, occupation, is_pwd, is_solo_parent, is_4ps, is_senior_citizen, is_indigenous, monthly_income FROM households');
  const [members] = await db.query('SELECT date_of_birth, occupation, monthly_income FROM household_members');

  const age = dob => {
    if (!dob) return null;
    const d = new Date(dob);
    const today = new Date();
    let a = today.getFullYear() - d.getFullYear();
    if (today.getMonth() < d.getMonth() || (today.getMonth() === d.getMonth() && today.getDate() < d.getDate())) a--;
    return a;
  };

  const brackets = [
    { label: 'Children 0 – 5 years old',   min: 0,  max: 5   },
    { label: 'Children 6 – 12 years old',  min: 6,  max: 12  },
    { label: 'Children 13 – 17 years old', min: 13, max: 17  },
    { label: 'Adult 18 – 35 years old',    min: 18, max: 35  },
    { label: 'Adult 36 – 50 years old',    min: 36, max: 50  },
    { label: 'Adult 51 – 65 years old',    min: 51, max: 65  },
    { label: 'Adult 66 years old & above', min: 66, max: 999 },
  ];

  const ageBrackets = brackets.map(b => ({ label: b.label, male: 0, female: 0, total: 0, min: b.min, max: b.max }));

  for (const h of heads) {
    const a = age(h.date_of_birth);
    if (a === null) continue;
    for (const b of ageBrackets) {
      if (a >= b.min && a <= b.max) {
        if ((h.gender || '').toLowerCase() === 'female') b.female++; else b.male++;
        b.total++; break;
      }
    }
  }
  for (const m of members) {
    const a = age(m.date_of_birth);
    if (a === null) continue;
    for (const b of ageBrackets) { if (a >= b.min && a <= b.max) { b.total++; break; } }
  }

  let laborForce = 0, unemployed = 0, osy = 0, osc = 0, pwd = 0, ofw = 0, soloParent = 0, indigenous = 0, civilSingle = 0, civilMarried = 0;
  const totalPop = heads.length + members.length;

  for (const h of heads) {
    const occ = (h.occupation || '').toLowerCase();
    if (occ && !['none','n/a'].includes(occ)) laborForce++;
    if (occ.includes('unemploy')) unemployed++;
    if (h.is_pwd)         pwd++;
    if (h.is_solo_parent) soloParent++;
    if (h.is_indigenous)  indigenous++;
    const cs = (h.civil_status || '').toLowerCase();
    if (cs === 'single')  civilSingle++;
    if (cs === 'married') civilMarried++;
  }
  for (const m of members) {
    const a   = age(m.date_of_birth);
    const occ = (m.occupation || '').toLowerCase();
    if (a !== null && a >= 15 && a <= 24 && (!occ || occ === 'none')) osy++;
    if (a !== null && a >= 6  && a <= 14  && (!occ || occ === 'none')) osc++;
    if (occ && !['none','n/a'].includes(occ)) laborForce++;
    if (occ.includes('unemploy')) unemployed++;
    if (occ.includes('ofw') || occ.includes('overseas')) ofw++;
  }

  const sectorRows = [
    { label: 'Labor Force',                            total: laborForce },
    { label: 'Unemployed',                             total: unemployed },
    { label: 'Out-of-School Youth (OSY) 15–24 y/o',   total: osy },
    { label: 'Out-of-School Children (OSC) 6–14 y/o', total: osc },
    { label: 'Persons with Disabilities (PWDs)',       total: pwd },
    { label: 'Overseas Filipino Workers (OFWs)',       total: ofw },
    { label: 'Solo Parents',                           total: soloParent },
    { label: 'Indigenous Peoples (IPs)',               total: indigenous },
    { label: 'Civil Status: Single',                   total: civilSingle },
    { label: 'Civil Status: Married',                  total: civilMarried },
  ];

  const totalHouseholds = heads.length;
  const totalMale       = ageBrackets.reduce((s, b) => s + b.male, 0);
  const totalFemale     = ageBrackets.reduce((s, b) => s + b.female, 0);
  const [[{ tc }]]      = await db.query("SELECT COUNT(*) AS tc FROM clearance_requests WHERE status = 'approved'");
  const totalClearances = parseInt(tc);
  const avgHHSize       = totalHouseholds > 0 ? (totalPop / totalHouseholds).toFixed(1) : 0;
  const [[{ rv }]]      = await db.query("SELECT COUNT(*) AS rv FROM households WHERE registered_voter = 1");
  const [[{ tf }]]      = await db.query("SELECT COALESCE(SUM(num_families),0) AS tf FROM households");
  const registeredVoters = parseInt(rv);
  const totalFamilies    = parseInt(tf);

  return { totalPop, totalMale, totalFemale, totalHouseholds, totalClearances, avgHHSize, ageBrackets, sectorRows, registeredVoters, totalFamilies };
}
