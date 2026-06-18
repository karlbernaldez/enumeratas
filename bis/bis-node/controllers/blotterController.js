const BlotterReport = require('../models/BlotterReport');
const User          = require('../models/User');

exports.adminIndex = async (req, res) => {
  const role    = req.session.role;
  const reports = await BlotterReport.getAll();
  res.render(`dashboard/${role}/blotter`, { title: 'Blotter Reports', reports });
};

exports.show = async (req, res) => {
  const role   = req.session.role;
  const report = await BlotterReport.findById(parseInt(req.params.id));
  if (!report) { req.flash('error', 'Report not found.'); return res.redirect(`/${role}/blotter`); }
  res.render('dashboard/captain/blotter_detail', { title: 'Blotter Detail', report, role });
};

exports.store = async (req, res) => {
  const userId = req.session.userId;
  const user   = await User.findById(userId);
  const { incident_type, incident_date, incident_time, location, persons_involved, narrative } = req.body;
  if (!incident_type || !narrative) {
    req.flash('error', 'Incident type and narrative are required.');
    return res.redirect('/resident/dashboard');
  }
  await BlotterReport.create({
    complainant_user_id: userId,
    complainant_name:    user ? user.full_name : '',
    complainant_email:   user ? user.email     : '',
    incident_type, incident_date: incident_date || null,
    incident_time: incident_time || null,
    location: location || null,
    persons_involved: persons_involved || null,
    narrative, status: 'pending',
  });
  req.flash('success', 'Blotter report submitted.');
  return res.redirect('/resident/dashboard');
};

exports.storePublic = async (req, res) => {
  const { complainant_name, complainant_email, incident_type, incident_date, incident_time, location, persons_involved, narrative } = req.body;
  if (!incident_type || !narrative || !complainant_name) {
    req.flash('error', 'Please fill in all required fields.');
    return res.redirect('/');
  }
  await BlotterReport.create({
    complainant_user_id: null,
    complainant_name, complainant_email: complainant_email || null,
    incident_type, incident_date: incident_date || null,
    incident_time: incident_time || null,
    location: location || null,
    persons_involved: persons_involved || null,
    narrative, status: 'pending',
  });
  req.flash('success', 'Your blotter report has been submitted.');
  return res.redirect('/');
};

exports.updateStatus = async (req, res) => {
  const role   = req.session.role;
  const { status, remarks } = req.body;
  await BlotterReport.update(parseInt(req.params.id), {
    status: status || 'pending',
    remarks: remarks || null,
    processed_by: req.session.userId,
  });
  req.flash('success', 'Status updated.');
  return res.redirect(`/${role}/blotter/${req.params.id}`);
};

exports.sendSummons = async (req, res) => {
  const role = req.session.role;
  const id   = parseInt(req.params.id);
  const { hearing_date, hearing_time } = req.body;
  await BlotterReport.update(id, {
    hearing_date: hearing_date || null,
    hearing_time: hearing_time || null,
    summons_sent_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
    status: 'under_investigation',
  });
  req.flash('success', 'Summons sent and hearing scheduled.');
  return res.redirect(`/${role}/blotter/${id}`);
};

exports.reschedule = async (req, res) => {
  const role = req.session.role;
  const id   = parseInt(req.params.id);
  const { hearing_date, hearing_time } = req.body;
  await BlotterReport.update(id, {
    hearing_date: hearing_date || null,
    hearing_time: hearing_time || null,
  });
  req.flash('success', 'Hearing rescheduled.');
  return res.redirect(`/${role}/blotter/${id}`);
};
