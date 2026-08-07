const { pool } = require('../config/db');

async function create(data) {
  const [result] = await pool.execute(
    'INSERT INTO medical_facilities (user_id, facility_category, facility_name, license_number, contact_person) VALUES (?, ?, ?, ?, ?)',
    [data.user_id, data.facility_category, data.facility_name, data.license_number || null, data.contact_person || null]
  );
  return findById(result.insertId);
}

async function findById(id) {
  const [rows] = await pool.execute('SELECT * FROM medical_facilities WHERE id = ? LIMIT 1', [id]);
  return rows[0] || null;
}

async function findByUserId(userId) {
  const [rows] = await pool.execute('SELECT * FROM medical_facilities WHERE user_id = ? LIMIT 1', [userId]);
  return rows[0] || null;
}

async function firstOrCreateByUserId(userId, data) {
  const existing = await findByUserId(userId);
  if (existing) return existing;
  return create({ ...data, user_id: userId });
}

async function countAll() {
  const [rows] = await pool.execute('SELECT COUNT(*) AS total FROM medical_facilities');
  return rows[0].total;
}

async function allWithUsers() {
  const [rows] = await pool.execute('SELECT f.*, u.name, u.email, u.phone FROM medical_facilities f JOIN users u ON u.id = f.user_id ORDER BY f.facility_name');
  return rows;
}

module.exports = { create, findById, findByUserId, firstOrCreateByUserId, countAll, allWithUsers };
