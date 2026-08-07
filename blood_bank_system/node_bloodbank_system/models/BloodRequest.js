const { pool } = require('../config/db');

async function create(data) {
  const [result] = await pool.execute(
    'INSERT INTO blood_requests (requester_id, requester_role, facility_category, facility_name, blood_type, component, units, urgency, reason) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
    [data.requester_id, 'facility', data.facility_category, data.facility_name, data.blood_type, data.component, data.units, data.urgency, data.reason || null]
  );
  return result.insertId;
}

async function latest(limit = 8) {
  const safeLimit = Math.max(1, Math.min(100, Number.parseInt(limit, 10) || 8));
  const [rows] = await pool.query('SELECT br.*, u.name AS requester_name, mf.facility_name AS requester_facility FROM blood_requests br JOIN users u ON u.id = br.requester_id LEFT JOIN medical_facilities mf ON mf.user_id = br.requester_id ORDER BY br.created_at DESC LIMIT ' + safeLimit);
  return rows;
}

async function all() {
  const [rows] = await pool.execute('SELECT br.*, u.name AS requester_name, mf.facility_name AS requester_facility FROM blood_requests br JOIN users u ON u.id = br.requester_id LEFT JOIN medical_facilities mf ON mf.user_id = br.requester_id ORDER BY br.created_at DESC');
  return rows;
}

async function byRequester(userId) {
  const [rows] = await pool.execute('SELECT * FROM blood_requests WHERE requester_id = ? ORDER BY created_at DESC', [userId]);
  return rows;
}

async function incomingForFacility(facilityName, currentUserId) {
  const [rows] = await pool.execute("SELECT br.*, mf.facility_name AS requester_facility FROM blood_requests br LEFT JOIN medical_facilities mf ON mf.user_id = br.requester_id WHERE br.requester_role = 'facility' AND br.facility_name = ? AND br.requester_id <> ? ORDER BY br.created_at DESC", [facilityName, currentUserId]);
  return rows;
}

async function countIncomingForFacility(facilityName, currentUserId) {
  const [rows] = await pool.execute(
    "SELECT COUNT(*) AS total FROM blood_requests WHERE requester_role = 'facility' AND facility_name = ? AND requester_id <> ? AND status = 'pending'",
    [facilityName, currentUserId]
  );
  return rows[0].total;
}

async function findById(id) {
  const [rows] = await pool.execute('SELECT * FROM blood_requests WHERE id = ? LIMIT 1', [id]);
  return rows[0] || null;
}

async function updateStatus(id, data) {
  await pool.execute('UPDATE blood_requests SET status = ?, admin_note = ? WHERE id = ?', [data.status, data.admin_note || null, id]);
}

async function countPending() {
  const [rows] = await pool.execute("SELECT COUNT(*) AS total FROM blood_requests WHERE status = 'pending'");
  return rows[0].total;
}

async function monthlyCounts(year) {
  const [rows] = await pool.execute('SELECT MONTH(created_at) AS month, COUNT(*) AS total FROM blood_requests WHERE YEAR(created_at) = ? GROUP BY MONTH(created_at) ORDER BY month', [year]);
  return rows;
}

module.exports = { create, latest, all, byRequester, incomingForFacility, countIncomingForFacility, findById, updateStatus, countPending, monthlyCounts };
