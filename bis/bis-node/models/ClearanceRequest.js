const db = require('../config/db');

class ClearanceRequest {
  static async getByUser(userId) {
    const [rows] = await db.query(
      'SELECT * FROM clearance_requests WHERE user_id = ? ORDER BY created_at DESC',
      [userId]
    );
    return rows;
  }

  static async findById(id) {
    const [rows] = await db.query('SELECT * FROM clearance_requests WHERE id = ?', [id]);
    return rows[0] || null;
  }

  static async create(data) {
    const cols = Object.keys(data).join(', ');
    const placeholders = Object.keys(data).map(() => '?').join(', ');
    const [result] = await db.query(
      `INSERT INTO clearance_requests (${cols}, created_at, updated_at) VALUES (${placeholders}, NOW(), NOW())`,
      Object.values(data)
    );
    return result.insertId;
  }

  static async update(id, data) {
    const fields = Object.keys(data).map(k => `${k} = ?`).join(', ');
    await db.query(
      `UPDATE clearance_requests SET ${fields}, updated_at = NOW() WHERE id = ?`,
      [...Object.values(data), id]
    );
  }

  static async delete(id) {
    await db.query('DELETE FROM clearance_requests WHERE id = ?', [id]);
  }

  static async getGroupedByResident({ statusFilter = '', typeFilter = '', search = '', page = 1, perPage = 10 } = {}) {
    let having = [];
    let where  = [];
    let params = [];

    if (search) {
      where.push("u.full_name LIKE ?");
      params.push(`%${search}%`);
    }
    if (typeFilter) {
      where.push("cr.document_type = ?");
      params.push(typeFilter);
    }
    const whereSQL = where.length ? 'WHERE ' + where.join(' AND ') : '';

    if (statusFilter) {
      having.push(`SUM(cr.status = ?) > 0`);
      params.push(statusFilter);
    }
    const havingSQL = having.length ? 'HAVING ' + having.join(' AND ') : '';

    const countSQL = `SELECT COUNT(*) AS total FROM (
      SELECT cr.user_id FROM clearance_requests cr
      LEFT JOIN users u ON u.id = cr.user_id
      ${whereSQL} GROUP BY cr.user_id ${havingSQL}
    ) AS sub`;
    const [[{ total }]] = await db.query(countSQL, [...params, ...(statusFilter ? [statusFilter] : [])]);

    const offset = (page - 1) * perPage;
    const dataParams = [...params, ...(statusFilter ? [statusFilter] : []), perPage, offset];
    const [rows] = await db.query(
      `SELECT cr.user_id, u.full_name AS resident_name, u.username,
        h.zone, h.address, h.contact_number,
        COUNT(cr.id) AS total_requests,
        SUM(cr.status = 'pending') AS pending_count,
        SUM(cr.status = 'approved') AS approved_count,
        SUM(cr.status = 'rejected') AS rejected_count,
        MAX(cr.created_at) AS latest_filed
       FROM clearance_requests cr
       LEFT JOIN users u ON u.id = cr.user_id
       LEFT JOIN households h ON h.household_no = u.household_no
       ${whereSQL} GROUP BY cr.user_id ${havingSQL}
       ORDER BY latest_filed DESC LIMIT ? OFFSET ?`,
      dataParams
    );
    return { rows, total: parseInt(total) };
  }

  static async getStats() {
    const [[stats]] = await db.query(
      `SELECT
        COUNT(*) AS total,
        SUM(status = 'pending') AS pending,
        SUM(status = 'approved') AS approved,
        SUM(status = 'rejected') AS rejected
       FROM clearance_requests`
    );
    return stats;
  }

  static async getByResidentUser(userId) {
    const [rows] = await db.query(
      'SELECT * FROM clearance_requests WHERE user_id = ? ORDER BY created_at DESC',
      [userId]
    );
    return rows;
  }
}

module.exports = ClearanceRequest;
