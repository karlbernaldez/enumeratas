const db = require('../config/db');

class BlotterReport {
  static async getAll() {
    const [rows] = await db.query(
      `SELECT b.*, u.full_name AS complainant_full_name
       FROM blotter_reports b
       LEFT JOIN users u ON u.id = b.complainant_user_id
       ORDER BY b.created_at DESC`
    );
    return rows;
  }

  static async findById(id) {
    const [rows] = await db.query(
      `SELECT b.*, u.full_name AS complainant_full_name, u.email AS complainant_email_addr
       FROM blotter_reports b
       LEFT JOIN users u ON u.id = b.complainant_user_id
       WHERE b.id = ?`,
      [id]
    );
    return rows[0] || null;
  }

  static async getByUser(userId) {
    const [rows] = await db.query(
      'SELECT * FROM blotter_reports WHERE complainant_user_id = ? ORDER BY created_at DESC',
      [userId]
    );
    return rows;
  }

  static async create(data) {
    const cols = Object.keys(data).join(', ');
    const placeholders = Object.keys(data).map(() => '?').join(', ');
    const [result] = await db.query(
      `INSERT INTO blotter_reports (${cols}, created_at, updated_at) VALUES (${placeholders}, NOW(), NOW())`,
      Object.values(data)
    );
    return result.insertId;
  }

  static async update(id, data) {
    const fields = Object.keys(data).map(k => `${k} = ?`).join(', ');
    await db.query(
      `UPDATE blotter_reports SET ${fields}, updated_at = NOW() WHERE id = ?`,
      [...Object.values(data), id]
    );
  }

  static async countByUser(userId) {
    const [[{ c }]] = await db.query(
      'SELECT COUNT(*) AS c FROM blotter_reports WHERE complainant_user_id = ?',
      [userId]
    );
    return parseInt(c);
  }
}

module.exports = BlotterReport;
