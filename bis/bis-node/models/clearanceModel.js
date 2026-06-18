const pool = require('../config/db');

const ClearanceModel = {
  async getByUser(userId) {
    const [rows] = await pool.query(
      'SELECT * FROM clearance_requests WHERE user_id = ? ORDER BY created_at DESC',
      [userId]
    );
    return rows;
  },

  async findById(id) {
    const [rows] = await pool.query('SELECT * FROM clearance_requests WHERE id = ? LIMIT 1', [id]);
    return rows[0] || null;
  },

  async insert(data) {
    const fields = Object.keys(data);
    const placeholders = fields.map(() => '?').join(', ');
    const values = fields.map(f => data[f]);
    const [result] = await pool.query(
      `INSERT INTO clearance_requests (${fields.join(', ')}) VALUES (${placeholders})`,
      values
    );
    return result.insertId;
  },

  async update(id, data) {
    const fields = Object.keys(data).map(k => `${k} = ?`).join(', ');
    const values = [...Object.values(data), id];
    await pool.query(`UPDATE clearance_requests SET ${fields} WHERE id = ?`, values);
  },

  async delete(id) {
    await pool.query('DELETE FROM clearance_requests WHERE id = ?', [id]);
  },

  async adminList({ statusFilter = '', typeFilter = '', search = '', page = 1, perPage = 10 } = {}) {
    const offset = (page - 1) * perPage;
    let having = [];
    let where = [];
    let params = [];

    if (typeFilter) { where.push('cr.document_type = ?'); params.push(typeFilter); }
    if (search) { where.push('u.full_name LIKE ?'); params.push(`%${search}%`); }

    const whereStr = where.length ? 'WHERE ' + where.join(' AND ') : '';

    let havingStr = '';
    if (statusFilter) {
      havingStr = `HAVING SUM(cr.status = ?) > 0`;
      params.push(statusFilter);
    }

    const baseSql = `
      SELECT
        cr.user_id,
        u.full_name AS resident_name,
        u.username,
        h.zone,
        h.address,
        h.contact_number,
        COUNT(cr.id) AS total_requests,
        SUM(cr.status = 'pending')  AS pending_count,
        SUM(cr.status = 'approved') AS approved_count,
        SUM(cr.status = 'rejected') AS rejected_count,
        MAX(cr.created_at) AS latest_filed
      FROM clearance_requests cr
      LEFT JOIN users u ON u.id = cr.user_id
      LEFT JOIN households h ON h.household_no = u.household_no
      ${whereStr}
      GROUP BY cr.user_id
      ${havingStr}
      ORDER BY latest_filed DESC
    `;

    const countParams = statusFilter ? [...params] : [...params];
    const [[{ filteredTotal }]] = await pool.query(
      `SELECT COUNT(*) AS filteredTotal FROM (${baseSql}) AS sub`,
      countParams
    );

    const listParams = statusFilter ? [...params, perPage, offset] : [...params, perPage, offset];
    const [residents] = await pool.query(`${baseSql} LIMIT ? OFFSET ?`, listParams);

    return { residents, filteredTotal };
  },

  async getStats() {
    const [[stats]] = await pool.query(`
      SELECT
        SUM(status = 'pending')  AS pending,
        SUM(status = 'approved') AS approved,
        SUM(status = 'rejected') AS rejected,
        COUNT(*) AS total
      FROM clearance_requests
    `);
    return stats;
  },

  async getByUserGrouped(userId) {
    const [rows] = await pool.query(
      'SELECT * FROM clearance_requests WHERE user_id = ? ORDER BY created_at DESC',
      [userId]
    );
    return rows;
  },
};

module.exports = ClearanceModel;
