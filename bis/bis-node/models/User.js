const db = require('../config/db');
const bcrypt = require('bcryptjs');

class User {
  static async findById(id) {
    const [rows] = await db.query('SELECT * FROM users WHERE id = ?', [id]);
    return rows[0] || null;
  }

  static async findByEmail(email) {
    const [rows] = await db.query('SELECT * FROM users WHERE email = ?', [email]);
    return rows[0] || null;
  }

  static async findByUsername(username) {
    const [rows] = await db.query('SELECT * FROM users WHERE username = ?', [username]);
    return rows[0] || null;
  }

  static async findByVerifyToken(token) {
    const [rows] = await db.query(
      'SELECT * FROM users WHERE verify_token = ? AND email_verified = 0', [token]
    );
    return rows[0] || null;
  }

  static async findByCredentials(username, password) {
    const [rows] = await db.query(
      'SELECT id, full_name, username, email, role, status, email_verified, avatar, password FROM users WHERE username = ?',
      [username]
    );
    const user = rows[0];
    if (!user) return null;
    const match = await bcrypt.compare(password, user.password);
    if (!match) return null;
    delete user.password;
    return user;
  }

  static async create(data) {
    const hash = await bcrypt.hash(data.password, 10);
    const [result] = await db.query(
      `INSERT INTO users (full_name, email, username, password, role, status, email_verified,
        verify_token, verify_token_expires, household_no, created_at, updated_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())`,
      [
        data.full_name, data.email, data.username, hash,
        data.role, data.status || 'unverified', data.email_verified || 0,
        data.verify_token || null, data.verify_token_expires || null,
        data.household_no || null,
      ]
    );
    return result.insertId;
  }

  static async update(id, data) {
    const fields = Object.keys(data).map(k => `${k} = ?`).join(', ');
    const values = [...Object.values(data), id];
    await db.query(`UPDATE users SET ${fields}, updated_at = NOW() WHERE id = ?`, values);
  }

  static async markEmailVerified(id) {
    await db.query(
      `UPDATE users SET email_verified = 1, verify_token = NULL, verify_token_expires = NULL,
       status = 'pending', updated_at = NOW() WHERE id = ?`,
      [id]
    );
  }

  static async getPending() {
    const [rows] = await db.query(
      `SELECT * FROM users WHERE status = 'pending' AND role IN ('sk','resident') ORDER BY created_at ASC`
    );
    return rows;
  }

  static async approve(id) {
    await db.query(`UPDATE users SET status = 'active', updated_at = NOW() WHERE id = ?`, [id]);
  }

  static async reject(id) {
    await db.query(`UPDATE users SET status = 'rejected', updated_at = NOW() WHERE id = ?`, [id]);
  }

  static async getActiveByRole(role) {
    const [rows] = await db.query(
      `SELECT id, full_name, username, email, status FROM users WHERE role = ? AND status = 'active' LIMIT 1`,
      [role]
    );
    return rows[0] || null;
  }

  static async getAll() {
    const [rows] = await db.query(
      `SELECT id, full_name, username, role FROM users WHERE status = 'active' ORDER BY full_name ASC`
    );
    return rows;
  }
}

module.exports = User;
