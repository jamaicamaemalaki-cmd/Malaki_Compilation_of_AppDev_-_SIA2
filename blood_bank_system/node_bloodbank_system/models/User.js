const { pool } = require('../config/db');

async function create(data) {
  const [result] = await pool.execute(
    'INSERT INTO users (name, email, password, role, phone, address) VALUES (?, ?, ?, ?, ?, ?)',
    [data.name, data.email, data.password, data.role, data.phone || null, data.address || null]
  );
  return findById(result.insertId);
}

async function findByEmail(email) {
  const [rows] = await pool.execute('SELECT * FROM users WHERE email = ? LIMIT 1', [email]);
  return rows[0] || null;
}

async function findById(id) {
  const [rows] = await pool.execute('SELECT * FROM users WHERE id = ? LIMIT 1', [id]);
  return rows[0] || null;
}

async function firstOrCreateByEmail(email, data) {
  const existing = await findByEmail(email);
  if (existing) return existing;
  return create({ ...data, email });
}

async function upsertByEmail(email, data) {
  await pool.execute(
    'INSERT INTO users (name, email, password, role, phone, address) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name), password = VALUES(password), role = VALUES(role), phone = VALUES(phone), address = VALUES(address)',
    [data.name, email, data.password, data.role, data.phone || null, data.address || null]
  );
  return findByEmail(email);
}

async function countAll() {
  const [rows] = await pool.execute('SELECT COUNT(*) AS total FROM users');
  return rows[0].total;
}

async function monthlyCounts(year) {
  const [rows] = await pool.execute(
    'SELECT MONTH(created_at) AS month, COUNT(*) AS total FROM users WHERE YEAR(created_at) = ? GROUP BY MONTH(created_at) ORDER BY month',
    [year]
  );
  return rows;
}

module.exports = { create, findByEmail, findById, firstOrCreateByEmail, upsertByEmail, countAll, monthlyCounts };
