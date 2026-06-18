const pool = require('../config/db');

const HouseholdModel = {
  async findAll({ search = '', zone = '', page = 1, perPage = 15 } = {}) {
    const offset = (page - 1) * perPage;
    let where = [];
    let params = [];
    if (search) {
      where.push('(last_name LIKE ? OR first_name LIKE ? OR household_no LIKE ?)');
      params.push(`%${search}%`, `%${search}%`, `%${search}%`);
    }
    if (zone) { where.push('zone = ?'); params.push(zone); }
    const whereStr = where.length ? 'WHERE ' + where.join(' AND ') : '';
    const [rows] = await pool.query(
      `SELECT * FROM households ${whereStr} ORDER BY household_no ASC LIMIT ? OFFSET ?`,
      [...params, perPage, offset]
    );
    const [[{ total }]] = await pool.query(
      `SELECT COUNT(*) AS total FROM households ${whereStr}`, params
    );
    return { rows, total };
  },

  async findById(householdNo) {
    const [rows] = await pool.query('SELECT * FROM households WHERE household_no = ? LIMIT 1', [householdNo]);
    return rows[0] || null;
  },

  async getWithMembers(householdNo) {
    const household = await this.findById(householdNo);
    if (!household) return null;
    const [members] = await pool.query(
      'SELECT * FROM household_members WHERE household_no = ? ORDER BY relationship ASC',
      [householdNo]
    );
    household.members = members;
    return household;
  },

  async create(data) {
    const fields = Object.keys(data);
    const placeholders = fields.map(() => '?').join(', ');
    const values = fields.map(f => data[f]);
    await pool.query(
      `INSERT INTO households (${fields.join(', ')}) VALUES (${placeholders})`,
      values
    );
  },

  async update(householdNo, data) {
    const fields = Object.keys(data).map(k => `${k} = ?`).join(', ');
    const values = [...Object.values(data), householdNo];
    await pool.query(`UPDATE households SET ${fields} WHERE household_no = ?`, values);
  },

  async delete(householdNo) {
    await pool.query('DELETE FROM household_members WHERE household_no = ?', [householdNo]);
    await pool.query('DELETE FROM households WHERE household_no = ?', [householdNo]);
  },

  async generateHouseholdNo() {
    let no;
    do {
      no = String(Math.floor(10000 + Math.random() * 90000));
      const [rows] = await pool.query('SELECT household_no FROM households WHERE household_no = ?', [no]);
      if (!rows.length) break;
    } while (true);
    return no;
  },

  async getStats(zone = '') {
    const zoneWhere = zone ? `WHERE zone = '${zone.replace(/'/g, "''")}'` : '';
    const [[stats]] = await pool.query(`
      SELECT
        COUNT(*) AS totalHouseholds,
        SUM(gender = 'Male') AS totalMale,
        SUM(gender = 'Female') AS totalFemale,
        SUM(is_pwd = 1) AS pwds,
        SUM(is_4ps = 1) AS fourPs,
        SUM(is_senior_citizen = 1) AS seniors,
        SUM(is_solo_parent = 1) AS soloParent
      FROM households ${zoneWhere}
    `);
    return stats;
  },
};

module.exports = HouseholdModel;
