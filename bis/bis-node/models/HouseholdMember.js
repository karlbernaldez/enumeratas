const db = require('../config/db');

class HouseholdMember {
  static async getByHousehold(householdNo) {
    const [rows] = await db.query(
      'SELECT * FROM household_members WHERE household_no = ? ORDER BY relationship ASC',
      [householdNo]
    );
    return rows;
  }

  static async replaceMembers(householdNo, members) {
    await db.query('DELETE FROM household_members WHERE household_no = ?', [householdNo]);
    if (!members.length) return;
    for (const m of members) {
      m.household_no = householdNo;
      const cols = Object.keys(m).join(', ');
      const placeholders = Object.keys(m).map(() => '?').join(', ');
      await db.query(
        `INSERT INTO household_members (${cols}, created_at, updated_at) VALUES (${placeholders}, NOW(), NOW())`,
        Object.values(m)
      );
    }
  }

  static async add(data) {
    const cols = Object.keys(data).join(', ');
    const placeholders = Object.keys(data).map(() => '?').join(', ');
    const [result] = await db.query(
      `INSERT INTO household_members (${cols}, created_at, updated_at) VALUES (${placeholders}, NOW(), NOW())`,
      Object.values(data)
    );
    return result.insertId;
  }

  static async update(id, data) {
    const fields = Object.keys(data).map(k => `${k} = ?`).join(', ');
    await db.query(
      `UPDATE household_members SET ${fields}, updated_at = NOW() WHERE id = ?`,
      [...Object.values(data), id]
    );
  }

  static async delete(id) {
    await db.query('DELETE FROM household_members WHERE id = ?', [id]);
  }
}

module.exports = HouseholdMember;
