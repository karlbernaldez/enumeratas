const db = require('../config/db');

class Schedule {
  static async getAll({ role = null } = {}) {
    let where = [];
    let params = [];
    if (role === 'captain') {
      where.push("(created_by_role = 'captain' OR is_shared = 1)");
    } else if (role === 'secretary') {
      where.push("created_by_role = 'secretary'");
    }
    const whereSQL = where.length ? 'WHERE ' + where.join(' AND ') : '';
    const [rows] = await db.query(
      `SELECT * FROM schedules ${whereSQL} ORDER BY event_date ASC, event_time ASC`,
      params
    );
    return rows;
  }

  static async findById(id) {
    const [rows] = await db.query('SELECT * FROM schedules WHERE id = ?', [id]);
    return rows[0] || null;
  }

  static async create(data) {
    const cols = Object.keys(data).join(', ');
    const placeholders = Object.keys(data).map(() => '?').join(', ');
    const [result] = await db.query(
      `INSERT INTO schedules (${cols}, created_at, updated_at) VALUES (${placeholders}, NOW(), NOW())`,
      Object.values(data)
    );
    return result.insertId;
  }

  static async update(id, data) {
    const fields = Object.keys(data).map(k => `${k} = ?`).join(', ');
    await db.query(
      `UPDATE schedules SET ${fields}, updated_at = NOW() WHERE id = ?`,
      [...Object.values(data), id]
    );
  }

  static async delete(id) {
    await db.query('DELETE FROM schedules WHERE id = ?', [id]);
  }
}

module.exports = Schedule;
