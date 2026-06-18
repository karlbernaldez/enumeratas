const db = require('../config/db');

class SkYouth {
  static async getAll({ zone = '', gender = '', ageGroup = '', status = '', search = '', page = 1, perPage = 15 } = {}) {
    let where = [];
    let params = [];

    // Pull from census: households + household_members aged 15-30
    // We UNION heads and members, then filter
    const headSQL = `
      SELECT h.household_no, h.first_name, h.last_name, h.middle_name, h.suffix,
        h.date_of_birth, h.gender, h.civil_status, h.occupation, h.educational_attainment,
        h.zone, h.contact_number, 'Household Head' AS relationship
      FROM households h
      WHERE h.date_of_birth IS NOT NULL
        AND TIMESTAMPDIFF(YEAR, h.date_of_birth, CURDATE()) BETWEEN 15 AND 30
    `;
    const memberSQL = `
      SELECT m.household_no, m.first_name, m.last_name, m.middle_name, m.suffix,
        m.date_of_birth, m.gender, m.civil_status, m.occupation, m.educational_attainment,
        h2.zone, h2.contact_number, m.relationship
      FROM household_members m
      INNER JOIN households h2 ON h2.household_no = m.household_no
      WHERE m.date_of_birth IS NOT NULL
        AND TIMESTAMPDIFF(YEAR, m.date_of_birth, CURDATE()) BETWEEN 15 AND 30
    `;

    const union = `(${headSQL}) UNION ALL (${memberSQL})`;
    const offset = (page - 1) * perPage;

    const [rows] = await db.query(
      `SELECT * FROM (${union}) AS youth ORDER BY last_name ASC LIMIT ? OFFSET ?`,
      [perPage, offset]
    );
    const [[{ total }]] = await db.query(
      `SELECT COUNT(*) AS total FROM (${union}) AS youth`
    );
    return { rows, total: parseInt(total) };
  }
}

module.exports = SkYouth;
