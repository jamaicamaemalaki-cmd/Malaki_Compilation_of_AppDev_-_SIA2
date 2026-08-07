const { pool } = require('../config/db');

async function create(data) {
  const [result] = await pool.execute(
    'INSERT INTO donation_requests (donor_id, facility_category, facility_name, blood_type, component, units, donor_note) VALUES (?, ?, ?, ?, ?, ?, ?)',
    [data.donor_id, data.facility_category, data.facility_name, data.blood_type, data.component, data.units, data.donor_note || null]
  );
  return result.insertId;
}

async function byDonor(donorId) {
  const [rows] = await pool.execute('SELECT * FROM donation_requests WHERE donor_id = ? ORDER BY created_at DESC', [donorId]);
  return rows;
}

async function byFacilityName(facilityName) {
  const [rows] = await pool.execute('SELECT dr.*, u.name AS donor_name, u.email AS donor_email, u.phone AS donor_phone FROM donation_requests dr JOIN donors d ON d.id = dr.donor_id JOIN users u ON u.id = d.user_id WHERE dr.facility_name = ? ORDER BY dr.created_at DESC', [facilityName]);
  return rows;
}

async function countPendingByFacilityName(facilityName) {
  const [rows] = await pool.execute(
    "SELECT COUNT(*) AS total FROM donation_requests WHERE facility_name = ? AND status = 'pending'",
    [facilityName]
  );
  return rows[0].total;
}

async function findById(id) {
  const [rows] = await pool.execute('SELECT * FROM donation_requests WHERE id = ? LIMIT 1', [id]);
  return rows[0] || null;
}

async function updateSchedule(id, data) {
  await pool.execute(
    'UPDATE donation_requests SET status = ?, scheduled_date = ?, start_time = ?, end_time = ?, facility_note = ? WHERE id = ?',
    [data.status, data.scheduled_date || null, data.start_time || null, data.end_time || null, data.facility_note || null, id]
  );
}

async function latest(limit = 8) {
  const safeLimit = Math.max(1, Math.min(100, Number.parseInt(limit, 10) || 8));
  const [rows] = await pool.query('SELECT * FROM donation_requests ORDER BY created_at DESC LIMIT ' + safeLimit);
  return rows;
}

module.exports = { create, byDonor, byFacilityName, countPendingByFacilityName, findById, updateSchedule, latest };
