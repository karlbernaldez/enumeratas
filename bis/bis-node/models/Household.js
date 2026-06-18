const db = require('../config/db');

class Household {
  static async findAll({ zone = '', search = '', page = 1, perPage = 15 } = {}) {
    let where = [];
    let params = [];
    if (zone)   { where.push('zone = ?');   params.push(zone); }
    if (search) { where.push('(last_name LIKE ? OR first_name LIKE ? OR household_no LIKE ?)'); params.push(`%${search}%`, `%${search}%`, `%${search}%`); }
    const whereSQL = where.length ? 'WHERE ' + where.join(' AND ') : '';
    const offset = (page - 1) * perPage;
    const [[{ total }]] = await db.query(`SELECT COUNT(*) AS total FROM households ${whereSQL}`, params);
    const [rows] = await db.query(`SELECT * FROM households ${whereSQL} ORDER BY household_no ASC LIMIT ? OFFSET ?`, [...params, perPage, offset]);
    return { rows, total: parseInt(total) };
  }

  static async findByNo(no) {
    const [rows] = await db.query('SELECT * FROM households WHERE household_no = ?', [no]);
    return rows[0] || null;
  }

  static async create(data) {
    const cols = Object.keys(data).join(', ');
    const placeholders = Object.keys(data).map(() => '?').join(', ');
    await db.query(
      `INSERT INTO households (${cols}, created_at, updated_at) VALUES (${placeholders}, NOW(), NOW())`,
      Object.values(data)
    );
  }

  static async update(no, data) {
    const fields = Object.keys(data).map(k => `${k} = ?`).join(', ');
    await db.query(`UPDATE households SET ${fields}, updated_at = NOW() WHERE household_no = ?`, [...Object.values(data), no]);
  }

  static async delete(no) {
    await db.query('DELETE FROM households WHERE household_no = ?', [no]);
  }

  static async generateNo() {
    let no;
    do {
      no = String(Math.floor(10000 + Math.random() * 90000));
      const [[{ c }]] = await db.query('SELECT COUNT(*) AS c FROM households WHERE household_no = ?', [no]);
      if (parseInt(c) === 0) break;
    } while (true);
    return no;
  }

  static async getStats(zone = '') {
    const zoneWhere = zone ? 'WHERE zone = ?' : '';
    const params = zone ? [zone] : [];
    const [[stats]] = await db.query(
      `SELECT
        COUNT(*) AS totalHouseholds,
        SUM(gender = 'Male') AS totalMale,
        SUM(gender = 'Female') AS totalFemale,
        SUM(is_pwd = 1) AS pwds,
        SUM(is_4ps = 1) AS fourPs,
        SUM(is_senior_citizen = 1) AS seniors,
        SUM(is_solo_parent = 1) AS soloParent
       FROM households ${zoneWhere}`,
      params
    );
    return stats;
  }
}

module.exports = Household;
