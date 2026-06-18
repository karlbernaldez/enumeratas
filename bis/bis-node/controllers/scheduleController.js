const Schedule = require('../models/Schedule');

exports.index = async (req, res) => {
  const role   = req.session.role;
  const events = await Schedule.getAll({ role });
  res.render(`dashboard/${role}/index`, { title: 'Calendar', events });
};

exports.listAll = async (req, res) => {
  const role   = req.session.role;
  const events = await Schedule.getAll({ role });
  res.json(events);
};

exports.view = async (req, res) => {
  const role  = req.session.role;
  const event = await Schedule.findById(parseInt(req.params.id));
  if (!event) { req.flash('error', 'Event not found.'); return res.redirect(`/${role}/calendar`); }
  res.render(`dashboard/${role}/event_detail`, { title: 'Event Detail', event });
};

exports.store = async (req, res) => {
  const role = req.session.role;
  const { title, description, event_date, event_time, event_type, is_shared } = req.body;
  await Schedule.create({
    title, description: description || null,
    event_date, event_time: event_time || null,
    event_type: event_type || 'general',
    is_shared: is_shared ? 1 : 0,
    created_by: req.session.userId,
    created_by_role: role,
  });
  req.flash('success', 'Event added.');
  return res.redirect(`/${role}/calendar`);
};

exports.update = async (req, res) => {
  const role = req.session.role;
  const { title, description, event_date, event_time, event_type, is_shared } = req.body;
  await Schedule.update(parseInt(req.params.id), {
    title, description: description || null,
    event_date, event_time: event_time || null,
    event_type: event_type || 'general',
    is_shared: is_shared ? 1 : 0,
  });
  req.flash('success', 'Event updated.');
  return res.redirect(`/${role}/calendar`);
};

exports.delete = async (req, res) => {
  const role = req.session.role;
  await Schedule.delete(parseInt(req.params.id));
  req.flash('success', 'Event deleted.');
  return res.redirect(`/${role}/calendar`);
};
