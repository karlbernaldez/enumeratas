const ClearanceRequest = require('../models/ClearanceRequest');
const Household        = require('../models/Household');
const HouseholdMember  = require('../models/HouseholdMember');
const User             = require('../models/User');
const db               = require('../config/db');

// ── Resident: show clearance page ─────────────────────────────────────────────
exports.residentIndex = async (req, res) => {
  const userId = req.session.userId;
  const user   = await User.findById(userId);
  const requests = await ClearanceRequest.getByUser(userId);

  let members = [];
  let householdTotalIncome = 0;
  let occupation = '';
  let isEmployed = false;

  if (user && user.household_no) {
    const hh = await Household.findByNo(user.household_no);
    const rawMembers = await HouseholdMember.getByHousehold(user.household_no);
    if (hh) {
      members.push({ name: `${hh.first_name} ${hh.last_name}`.trim(), relationship: 'Household Head' });
      householdTotalIncome += parseFloat(hh.monthly_income || 0);
      occupation = hh.occupation || '';
    }
    for (const m of rawMembers) {
      members.push({ name: `${m.first_name} ${m.last_name}`.trim(), relationship: m.relationship });
      householdTotalIncome += parseFloat(m.monthly_income || 0);
    }
  }

  const occ = (occupation || '').toLowerCase().trim();
  isEmployed = occ && !['none','n/a','unemployed','student','out-of-school',''].includes(occ);

  res.render('dashboard/resident/clearance', {
    title: 'My Clearances',
    requests, members, user,
    householdTotalIncome, occupation, isEmployed,
  });
};

// ── Resident: submit new request ──────────────────────────────────────────────
exports.store = async (req, res) => {
  const userId = req.session.userId;
  const user   = await User.findById(userId);
  const { for_member, member_relationship, document_type, purpose, notes } = req.body;

  if (!for_member || !document_type || !purpose) {
    req.flash('error', 'Please fill in all required fields.');
    return res.redirect('/resident/clearance');
  }

  // Indigency income check
  if (document_type === 'Certificate of Indigency' && user && user.household_no) {
    const hh      = await Household.findByNo(user.household_no);
    const members = await HouseholdMember.getByHousehold(user.household_no);
    const headInc = parseFloat(hh ? hh.monthly_income : 0);
    const memInc  = members.reduce((s, m) => s + parseFloat(m.monthly_income || 0), 0);
    const total   = headInc + memInc;
    if (total > 12000) {
      await ClearanceRequest.create({
        user_id: userId, household_no: user.household_no || null,
        for_member, member_relationship, document_type, purpose,
        notes: notes || null, status: 'rejected',
        remarks: `Automatically rejected: household income ₱${total.toFixed(2)} exceeds ₱12,000.00 threshold.`,
        processed_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
        est_release_date: null,
      });
      req.flash('error', `Request rejected. Household income ₱${total.toFixed(2)} exceeds the ₱12,000.00 indigency threshold.`);
      return res.redirect('/resident/clearance');
    }
  }

  // Add 2 business days
  const estRelease = addBusinessDays(new Date(), 2).toISOString().slice(0, 10);

  await ClearanceRequest.create({
    user_id: userId, household_no: user ? user.household_no : null,
    for_member, member_relationship, document_type, purpose,
    notes: notes || null, status: 'pending', est_release_date: estRelease,
  });

  req.flash('success', `Request submitted! Estimated release: ${formatDate(estRelease)}`);
  return res.redirect('/resident/clearance');
};

// ── Resident: cancel ──────────────────────────────────────────────────────────
exports.cancel = async (req, res) => {
  const userId  = req.session.userId;
  const request = await ClearanceRequest.findById(parseInt(req.params.id));
  if (!request || request.user_id !== userId || request.status !== 'pending') {
    req.flash('error', 'Cannot cancel this request.');
    return res.redirect('/resident/clearance');
  }
  await ClearanceRequest.delete(request.id);
  req.flash('success', 'Request cancelled.');
  return res.redirect('/resident/clearance');
};

// ── Admin: list all requests grouped by resident ──────────────────────────────
exports.adminIndex = async (req, res) => {
  const role         = req.session.role;
  const statusFilter = req.query.status || '';
  const typeFilter   = req.query.type   || '';
  const search       = req.query.search || '';
  const page         = parseInt(req.query.page || 1);
  const perPage      = 10;

  const stats = await ClearanceRequest.getStats();
  const { rows: residents, total: filteredTotal } = await ClearanceRequest.getGroupedByResident({
    statusFilter, typeFilter, search, page, perPage,
  });

  res.render(`dashboard/${role}/clearance`, {
    title: 'Clearance Requests',
    residents, filteredTotal, perPage, currentPage: page,
    statusFilter, typeFilter, search,
    pending: stats.pending, approved: stats.approved,
    rejected: stats.rejected, total: stats.total,
  });
};

// ── Admin: view one resident's requests ───────────────────────────────────────
exports.residentDetail = async (req, res) => {
  const role   = req.session.role;
  const userId = parseInt(req.params.userId);
  const user   = await User.findById(userId);
  if (!user) { req.flash('error', 'Resident not found.'); return res.redirect(`/${role}/clearance`); }
  const hh       = user.household_no ? await Household.findByNo(user.household_no) : null;
  const requests = await ClearanceRequest.getByResidentUser(userId);
  res.render('dashboard/captain/clearance_detail', { title: 'Clearance Detail', role, user, household: hh, requests, requestId: userId });
};

// ── Admin: approve / reject ───────────────────────────────────────────────────
exports.approve = async (req, res) => {
  await ClearanceRequest.update(parseInt(req.params.id), {
    status: 'approved', processed_by: req.session.userId,
    processed_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
  });
  req.flash('success', 'Request approved.');
  return res.redirect('back');
};

exports.reject = async (req, res) => {
  await ClearanceRequest.update(parseInt(req.params.id), {
    status: 'rejected', remarks: req.body.remarks || '',
    processed_by: req.session.userId,
    processed_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
  });
  req.flash('success', 'Request rejected.');
  return res.redirect('back');
};

// ── Helpers ───────────────────────────────────────────────────────────────────
function addBusinessDays(date, days) {
  let d = new Date(date);
  let added = 0;
  while (added < days) {
    d.setDate(d.getDate() + 1);
    const dow = d.getDay();
    if (dow !== 0 && dow !== 6) added++;
  }
  return d;
}

function formatDate(str) {
  const d = new Date(str);
  return d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
}
