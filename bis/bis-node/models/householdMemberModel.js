const pool = require('../config/db');

const HouseholdMemberModel = {
  async getByHousehold(householdNo) {
    const [rows] = await pool.query(
      'SELECT * FROM household_members WHERE household_no = ? ORDER BY relationship ASC',
      [householdNo]
    );
    return rows;
  },

  async insert(data) {
    const fields = Object.keys(data);
    const placeholders = fields.map(() => '?').join(', ');
    const values = fields.map(f => data[f]);
    const [result] = await pool.query(
      `INSERT INTO household_members (${fields.join(', ')}) VALUES (${placeholders})`,
      values
    );
    return result.insertId;
  },

  async update(id, data) {
    const fields = Object.keys(data).map(k => `${k} = ?`).join(', ');
    const values = [...Object.values(data), id];
    await pool.query(`UPDATE household_members SET ${fields} WHERE id = ?`, values);
  },

  async delete(id) {
    await pool.query('DELETE FROM household_members WHERE id = ?', [id]);
  },

  async replaceMembers(householdNo, members) {
    await pool.query('DELETE FROM household_members WHERE household_no = ?', [householdNo]);
    if (!members.length) return;
    for (const m of members) {
      m.household_no = householdNo;
      await this.insert(m);
    }
  },
};

module.exports = HouseholdMemberModel;
