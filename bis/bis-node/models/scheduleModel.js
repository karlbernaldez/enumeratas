const pool = require('../config/db');

const ScheduleModel = {
  async getVisibleByMonth(year, month, userId, role) {
    const start = `${year}-${String(month).padStart(2,'0')}-01`;
    const end   = new Date(year, month, 0).toISOString().slice(0,10);

    let rows;
    if (role === 'captain') {
      [rows] = await pool.query(
        `SELECT * FROM schedules
         WHERE event_date >= ? AND event_date <= ?
           AND (created_by = ? OR shared_with = ?)
         ORDER BY event_date ASC, start_time ASC`,
        [start, end, userId, userId]
      );
    } else {
      [rows] = await pool.query(
        `SELECT * FROM schedules
         WHERE event_date >= ? AND event_date <= ? AND created_by = ?
         ORDER BY event_date ASC, start_time ASC`,
        [start, end, userId]
      );
    }

    const byDate = {};
    for (const row of rows) {
      if (!byDate[row.event_date]) byDate[row.event_date] = [];
      byDate[row.event_date].push(row);
    }
    return byDate;
  },

  async getUpcomingVisible(userId, role, limit = 8) {
    const today = new Date().toISOString().slice(0,10);
    let rows;
    if (role === 'captain') {
      [rows] = await pool.query(
        `SELECT * FROM schedules
         WHERE event_date >= ? AND (created_by = ? OR shared_with = ?)
         ORDER BY event_date ASC, start_time ASC LIMIT ?`,
        [today, userId, userId, limit]
      );
    } else {
      [rows] = await pool.query(
        `SELECT * FROM schedules
         WHERE event_date >= ? AND created_by = ?
         ORDER BY event_date ASC, start_time ASC LIMIT ?`,
        [today, userId, limit]
      );
    }
    return rows;
  },

  async getAllVisible(userId, role, search = '', type = '') {
    let where = [];
    let params = [];

    if (role === 'captain') {
      where.push('(created_by = ? OR shared_with = ?)');
      params.push(userId, userId);
    } else {
      where.push('created_by = ?');
      params.push(userId);
    }

    if (search) {
      where.push('(title LIKE ? OR description LIKE ? OR location LIKE ?)');
      params.push(`%${search}%`, `%${search}%`, `%${search}%`);
    }
    if (type) { where.push('event_type = ?'); params.push(type); }

    const whereStr = 'WHERE ' + where.join(' AND ');
    const [rows] = await pool.query(
      `SELECT * FROM schedules ${whereStr} ORDER BY event_date DESC, start_time ASC`,
      params
    );
    return rows;
  },

  async findById(id) {
    const [rows] = await pool.query('SELECT * FROM schedules WHERE id = ? LIMIT 1', [id]);
    return rows[0] || null;
  },

  async insert(data) {
    const fields = Object.keys(data);
    const placeholders = fields.map(() => '?').join(', ');
    const values = fields.map(f => data[f]);
    const [result] = await pool.query(
      `INSERT INTO schedules (${fields.join(', ')}) VALUES (${placeholders})`,
      values
    );
    return result.insertId;
  },

  async update(id, data) {
    const fields = Object.keys(data).map(k => `${k} = ?`).join(', ');
    const values = [...Object.values(data), id];
    await pool.query(`UPDATE schedules SET ${fields} WHERE id = ?`, values);
  },

  async delete(id) {
    await pool.query('DELETE FROM schedules WHERE id = ?', [id]);
  },
};

module.exports = ScheduleModel;
