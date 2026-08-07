const { pool } = require('../config/db');

async function create(data) {
  const [result] = await pool.execute(
    'INSERT INTO donors (user_id, blood_type, age, gender, weight, declaration_confirmed, declaration_confirmed_at, medical_notes) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)',
    [data.user_id, data.blood_type, data.age, data.gender, data.weight || null, data.declaration_confirmed ? 1 : 0, data.medical_notes || null]
  );
  return findById(result.insertId);
}

async function findById(id) {
  const [rows] = await pool.execute('SELECT * FROM donors WHERE id = ? LIMIT 1', [id]);
  return rows[0] || null;
}

async function findByUserId(userId) {
  const [rows] = await pool.execute('SELECT * FROM donors WHERE user_id = ? LIMIT 1', [userId]);
  return rows[0] || null;
}

async function countAll() {
  const [rows] = await pool.execute('SELECT COUNT(*) AS total FROM donors');
  return rows[0].total;
}

async function allWithUsers() {
  const [rows] = await pool.execute('SELECT d.*, u.name, u.email, u.phone, u.address FROM donors d JOIN users u ON u.id = d.user_id ORDER BY d.created_at DESC');
  return rows;
}

module.exports = { create, findById, findByUserId, countAll, allWithUsers };
