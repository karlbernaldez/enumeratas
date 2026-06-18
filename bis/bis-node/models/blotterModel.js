const pool = require('../config/db');

const BlotterModel = {
  async getAll({ statusFilter = '', search = '' } = {}) {
    let where = [];
    let params = [];
    if (statusFilter) { where.push('b.status = ?'); params.push(statusFilter); }
    if (search) {
      where.push('(u.full_name LIKE ? OR b.incident_type LIKE ? OR b.persons_involved LIKE ?)');
      params.push(`%${search}%`, `%${search}%`, `%${search}%`);
    }
    const whereStr = where.length ? 'WHERE ' + where.join(' AND ') : '';
    const [rows] = await pool.query(
      `SELECT b.*, u.full_name AS complainant_full_name, u.email AS complainant_email_addr
       FROM blotter_reports b
       LEFT JOIN users u ON u.id = b.complainant_user_id
       ${whereStr}
       ORDER BY b.created_at DESC`,
      params
    );
    return rows;
  },

  async findById(id) {
    const [rows] = await pool.query(
      `SELECT b.*, u.full_name AS complainant_full_name, u.email AS complainant_email_addr
       FROM blotter_reports b
       LEFT JOIN users u ON u.id = b.complainant_user_id
       WHERE b.id = ? LIMIT 1`,
      [id]
    );
    return rows[0] || null;
  },

  async insert(data) {
    const fields = Object.keys(data);
    const placeholders = fields.map(() => '?').join(', ');
    const values = fields.map(f => data[f]);
    const [result] = await pool.query(
      `INSERT INTO blotter_reports (${fields.join(', ')}) VALUES (${placeholders})`,
      values
    );
    return result.insertId;
  },

  async update(id, data) {
    const fields = Object.keys(data).map(k => `${k} = ?`).join(', ');
    const values = [...Object.values(data), id];
    await pool.query(`UPDATE blotter_reports SET ${fields} WHERE id = ?`, values);
  },

  async getByUser(userId) {
    const [rows] = await pool.query(
      'SELECT * FROM blotter_reports WHERE complainant_user_id = ? ORDER BY created_at DESC',
      [userId]
    );
    return rows;
  },

  async getStats() {
    const [[stats]] = await pool.query(`
      SELECT
        SUM(status = 'pending') AS pending,
        SUM(status = 'under_investigation') AS investigating,
        SUM(status = 'resolved') AS resolved,
        COUNT(*) AS total
      FROM blotter_reports
    `);
    return stats;
  },
};

module.exports = BlotterModel;
