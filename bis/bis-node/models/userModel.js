const pool = require('../config/db');
const bcrypt = require('bcryptjs');

const UserModel = {
  async findByCredentials(username, password) {
    const [rows] = await pool.query(
      'SELECT id, full_name, username, email, role, status, email_verified, avatar, password FROM users WHERE username = ? LIMIT 1',
      [username]
    );
    if (!rows.length) return null;
    const user = rows[0];
    const match = await bcrypt.compare(password, user.password);
    if (!match) return null;
    delete user.password;
    return user;
  },

  async findById(id) {
    const [rows] = await pool.query(
      'SELECT id, full_name, username, email, role, status, email_verified, avatar, household_no, contact_number, verify_token, verify_token_expires FROM users WHERE id = ? LIMIT 1',
      [id]
    );
    return rows[0] || null;
  },

  async findByEmail(email) {
    const [rows] = await pool.query(
      'SELECT id, full_name, username, email, role, status, email_verified, avatar, household_no, verify_token, verify_token_expires FROM users WHERE email = ? LIMIT 1',
      [email]
    );
    return rows[0] || null;
  },

  async findByEmailWithPassword(email) {
    const [rows] = await pool.query(
      'SELECT id, full_name, username, email, role, status, email_verified, avatar, household_no, verify_token, verify_token_expires, password FROM users WHERE email = ? LIMIT 1',
      [email]
    );
    return rows[0] || null;
  },

  async findByIdWithPassword(id) {
    const [rows] = await pool.query(
      'SELECT id, full_name, username, email, role, status, email_verified, avatar, household_no, verify_token, verify_token_expires, password FROM users WHERE id = ? LIMIT 1',
      [id]
    );
    return rows[0] || null;
  },

  async create(data) {
    const hash = data.password ? await bcrypt.hash(data.password, 10) : data.password_hash;
    const [result] = await pool.query(
      `INSERT INTO users (full_name, email, username, password, role, status, email_verified, verify_token, verify_token_expires, household_no)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [
        data.full_name, data.email, data.username, hash,
        data.role, data.status || 'unverified',
        data.email_verified || 0,
        data.verify_token || null,
        data.verify_token_expires || null,
        data.household_no || null,
      ]
    );
    return result.insertId;
  },

  async update(id, data) {
    const fields = [];
    const values = [];
    const allowed = ['full_name','email','username','password','role','status','email_verified',
                     'verify_token','verify_token_expires','household_no','avatar','contact_number'];
    for (const key of allowed) {
      if (key in data) {
        fields.push(`${key} = ?`);
        values.push(data[key]);
      }
    }
    if (!fields.length) return;
    values.push(id);
    await pool.query(`UPDATE users SET ${fields.join(', ')} WHERE id = ?`, values);
  },

  async updatePassword(id, plainPassword) {
    const hash = await bcrypt.hash(plainPassword, 10);
    await pool.query('UPDATE users SET password = ?, verify_token = NULL, verify_token_expires = NULL WHERE id = ?', [hash, id]);
  },

  async markEmailVerified(id) {
    await pool.query(
      "UPDATE users SET email_verified = 1, verify_token = NULL, verify_token_expires = NULL, status = 'pending' WHERE id = ?",
      [id]
    );
  },

  async getPendingAccounts() {
    const [rows] = await pool.query(
      "SELECT id, full_name, username, email, role, created_at FROM users WHERE status = 'pending' AND role IN ('sk','resident') ORDER BY created_at ASC"
    );
    return rows;
  },

  async approveUser(id) {
    await pool.query("UPDATE users SET status = 'active' WHERE id = ?", [id]);
  },

  async rejectUser(id) {
    await pool.query("UPDATE users SET status = 'rejected' WHERE id = ?", [id]);
  },

  async getActiveByRole(role) {
    const [rows] = await pool.query(
      "SELECT id, full_name, username, email, status FROM users WHERE role = ? AND status = 'active' LIMIT 1",
      [role]
    );
    return rows[0] || null;
  },

  async isEmailTaken(email, excludeId = null) {
    let sql = 'SELECT id FROM users WHERE email = ?';
    const params = [email];
    if (excludeId) { sql += ' AND id != ?'; params.push(excludeId); }
    const [rows] = await pool.query(sql, params);
    return rows.length > 0;
  },

  async isUsernameTaken(username, excludeId = null) {
    let sql = 'SELECT id FROM users WHERE username = ?';
    const params = [username];
    if (excludeId) { sql += ' AND id != ?'; params.push(excludeId); }
    const [rows] = await pool.query(sql, params);
    return rows.length > 0;
  },

  async getAllActive() {
    const [rows] = await pool.query(
      "SELECT id, full_name, username, role FROM users WHERE status = 'active' ORDER BY full_name ASC"
    );
    return rows;
  },
};

module.exports = UserModel;
