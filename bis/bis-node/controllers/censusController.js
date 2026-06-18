const Household       = require('../models/Household');
const HouseholdMember = require('../models/HouseholdMember');

exports.store = async (req, res) => {
  const role = req.session.role;
  const b    = req.body;

  let householdNo = b.household_no;
  const existing = householdNo ? await Household.findByNo(householdNo) : null;
  if (!householdNo || existing) householdNo = await Household.generateNo();

  // Solo parent validation
  if (b.is_solo_parent) {
    const childNames = [].concat(b.child_last_name || []).filter(n => n.trim());
    if (!childNames.length) {
      req.flash('error', 'A Solo Parent record requires at least one child.');
      return res.redirect(`/${role}/census`);
    }
  }

  const householdData = {
    household_no:           householdNo,
    zone:                   b.zone                   || null,
    last_name:              b.last_name              || '',
    first_name:             b.first_name             || '',
    middle_name:            b.middle_name            || null,
    suffix:                 b.suffix                 || null,
    date_of_birth:          b.date_of_birth          || null,
    place_of_birth:         b.place_of_birth         || null,
    gender:                 b.gender                 || 'Male',
    civil_status:           b.civil_status           || 'Single',
    nationality:            b.nationality            || 'Filipino',
    religion:               b.religion               || null,
    occupation:             b.occupation             || null,
    monthly_income:         b.monthly_income         || 0,
    contact_number:         b.contact_number         || null,
    educational_attainment: b.educational_attainment || null,
    philhealth_no:          b.philhealth_no          || null,
    address:                b.address                || null,
    years_of_residency:     b.years_of_residency     || 0,
    house_ownership:        b.house_ownership        || 'Owned',
    is_4ps:                 b.is_4ps          ? 1 : 0,
    is_pwd:                 b.is_pwd          ? 1 : 0,
    is_senior_citizen:      b.is_senior_citizen ? 1 : 0,
    is_solo_parent:         b.is_solo_parent  ? 1 : 0,
    is_indigenous:          b.is_indigenous   ? 1 : 0,
    registered_voter:       b.registered_voter === '1' ? 1 : 0,
    num_families:           Math.max(1, parseInt(b.num_families || 1)),
    water_source_level:     b.water_source    || null,
    water_safety_managed:   b.water_managed   ? (b.water_managed === 'yes' ? 1 : 0) : null,
    sanitation_basic:       b.sanitation_basic    || null,
    sanitation_managed:     b.sanitation_managed  || null,
    recorded_by:            req.session.userId,
    recorded_date:          b.recorded_date || new Date().toISOString().slice(0, 10),
  };

  try {
    await Household.create(householdData);
  } catch (e) {
    req.flash('error', e.message || 'Failed to save household.');
    return res.redirect(`/${role}/census`);
  }

  const members = [];

  // Spouse
  if (b.spouse_last_name && b.spouse_last_name.trim()) {
    members.push({
      relationship:           'spouse',
      last_name:              b.spouse_last_name,
      first_name:             b.spouse_first_name             || '',
      middle_name:            b.spouse_middle_name            || null,
      suffix:                 b.spouse_suffix                 || null,
      date_of_birth:          b.spouse_dob                    || null,
      gender:                 b.spouse_gender                 || null,
      occupation:             b.spouse_occupation             || null,
      monthly_income:         b.spouse_income                 || 0,
      philhealth_no:          b.spouse_philhealth             || null,
      educational_attainment: b.spouse_educational_attainment || null,
      registered_voter:       b.spouse_registered_voter === '1' ? 1 : 0,
    });
  }

  // Children
  const childLastNames = [].concat(b.child_last_name || []);
  childLastNames.forEach((ln, i) => {
    if (!ln || !ln.trim()) return;
    members.push({
      relationship:   'child',
      last_name:      ln,
      first_name:     ([].concat(b.child_first_name  || []))[i] || '',
      middle_name:    ([].concat(b.child_middle_name || []))[i] || null,
      suffix:         ([].concat(b.child_suffix      || []))[i] || null,
      date_of_birth:  ([].concat(b.child_dob         || []))[i] || null,
      gender:         ([].concat(b.child_gender      || []))[i] || null,
      occupation:     ([].concat(b.child_occupation  || []))[i] || null,
      monthly_income: ([].concat(b.child_income      || []))[i] || 0,
      philhealth_no:  ([].concat(b.child_philhealth  || []))[i] || null,
      educational_attainment: null,
      registered_voter: (([].concat(b.child_registered_voter || []))[i] === '1') ? 1 : 0,
    });
  });

  // Other members
  const otherLastNames = [].concat(b.other_last_name || []);
  otherLastNames.forEach((ln, i) => {
    if (!ln || !ln.trim()) return;
    members.push({
      relationship:   ([].concat(b.other_relationship || []))[i] || 'other_relative',
      last_name:      ln,
      first_name:     ([].concat(b.other_first_name  || []))[i] || '',
      middle_name:    ([].concat(b.other_middle_name || []))[i] || null,
      suffix:         ([].concat(b.other_suffix      || []))[i] || null,
      date_of_birth:  ([].concat(b.other_dob         || []))[i] || null,
      gender:         ([].concat(b.other_gender      || []))[i] || null,
      occupation:     null,
      monthly_income: 0,
      philhealth_no:  null,
      educational_attainment: null,
      registered_voter: (([].concat(b.other_registered_voter || []))[i] === '1') ? 1 : 0,
    });
  });

  if (members.length) await HouseholdMember.replaceMembers(householdNo, members);

  req.flash('success', 'Household record saved successfully.');
  return res.redirect(`/${role}/census`);
};

exports.updateHousehold = async (req, res) => {
  const { householdNo } = req.params;
  const role = req.session.role;
  const b    = req.body;
  await Household.update(householdNo, {
    zone:                   b.zone                   || null,
    last_name:              (b.last_name   || '').toUpperCase(),
    first_name:             (b.first_name  || '').toUpperCase(),
    middle_name:            (b.middle_name || '').toUpperCase() || null,
    suffix:                 b.suffix                 || null,
    date_of_birth:          b.date_of_birth          || null,
    place_of_birth:         (b.place_of_birth || '').toUpperCase() || null,
    gender:                 b.gender                 || 'Male',
    civil_status:           b.civil_status           || 'Single',
    nationality:            (b.nationality || 'FILIPINO').toUpperCase(),
    religion:               (b.religion    || '').toUpperCase() || null,
    occupation:             (b.occupation  || '').toUpperCase() || null,
    monthly_income:         b.monthly_income         || 0,
    contact_number:         b.contact_number         || null,
    educational_attainment: b.educational_attainment || null,
    philhealth_no:          b.philhealth_no          || null,
    address:                (b.address     || '').toUpperCase() || null,
    years_of_residency:     b.years_of_residency     || 0,
    house_ownership:        b.house_ownership        || 'Owned',
    is_4ps:                 b.is_4ps          ? 1 : 0,
    is_pwd:                 b.is_pwd          ? 1 : 0,
    is_senior_citizen:      b.is_senior_citizen ? 1 : 0,
    is_solo_parent:         b.is_solo_parent  ? 1 : 0,
    is_indigenous:          b.is_indigenous   ? 1 : 0,
    registered_voter:       b.registered_voter === '1' ? 1 : 0,
    num_families:           Math.max(1, parseInt(b.num_families || 1)),
  });
  req.flash('success', 'Household updated.');
  return res.redirect(`/${role}/household/${householdNo}`);
};

exports.deleteHousehold = async (req, res) => {
  await Household.delete(req.params.householdNo);
  req.flash('success', 'Household deleted.');
  return res.redirect(`/${req.session.role}/census`);
};

exports.addMember = async (req, res) => {
  const { householdNo } = req.params;
  const role = req.session.role;
  const b    = req.body;
  await HouseholdMember.add({
    household_no:           householdNo,
    relationship:           b.relationship           || 'other_relative',
    last_name:              (b.last_name   || '').toUpperCase(),
    first_name:             (b.first_name  || '').toUpperCase(),
    middle_name:            (b.middle_name || '').toUpperCase() || null,
    suffix:                 b.suffix                 || null,
    date_of_birth:          b.date_of_birth          || null,
    gender:                 b.gender                 || null,
    occupation:             (b.occupation  || '').toUpperCase() || null,
    monthly_income:         b.monthly_income         || 0,
    philhealth_no:          b.philhealth_no          || null,
    educational_attainment: b.educational_attainment || null,
    registered_voter:       b.registered_voter === '1' ? 1 : 0,
  });
  req.flash('success', 'Member added.');
  return res.redirect(`/${role}/household/${householdNo}`);
};

exports.updateMember = async (req, res) => {
  const { memberId } = req.params;
  const role         = req.session.role;
  const b            = req.body;
  const householdNo  = b.household_no || '';
  await HouseholdMember.update(parseInt(memberId), {
    relationship:           b.relationship           || 'other_relative',
    last_name:              (b.last_name   || '').toUpperCase(),
    first_name:             (b.first_name  || '').toUpperCase(),
    middle_name:            (b.middle_name || '').toUpperCase() || null,
    suffix:                 b.suffix                 || null,
    date_of_birth:          b.date_of_birth          || null,
    gender:                 b.gender                 || null,
    occupation:             (b.occupation  || '').toUpperCase() || null,
    monthly_income:         b.monthly_income         || 0,
    philhealth_no:          b.philhealth_no          || null,
    educational_attainment: b.educational_attainment || null,
    registered_voter:       b.registered_voter === '1' ? 1 : 0,
  });
  req.flash('success', 'Member updated.');
  return res.redirect(`/${role}/household/${householdNo}`);
};

exports.deleteMember = async (req, res) => {
  const role        = req.session.role;
  const householdNo = req.body.household_no || '';
  await HouseholdMember.delete(parseInt(req.params.memberId));
  req.flash('success', 'Member removed.');
  return res.redirect(`/${role}/household/${householdNo}`);
};
