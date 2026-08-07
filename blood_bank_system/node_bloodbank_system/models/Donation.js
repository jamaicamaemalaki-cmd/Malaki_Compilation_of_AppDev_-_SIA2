const { pool } = require('../config/db');

async function create(data) {
  const [result] = await pool.execute(
    'INSERT INTO donations (donor_id, blood_type, component, units, donation_date, facility_name, notes) VALUES (?, ?, ?, ?, ?, ?, ?)',
    [data.donor_id, data.blood_type, data.component, data.units, data.donation_date, data.facility_name || null, data.notes || null]
  );
  return result.insertId;
}

function requestNoteMarker(requestId) {
  return 'Donation request #' + requestId;
}

async function saveFromDonationRequest(request, data) {
  const marker = requestNoteMarker(request.id);
  const notes = data.facility_note
    ? marker + ': ' + data.facility_note
    : marker + ': Auto-saved when schedule was sent.';
  const [existing] = await pool.execute(
    'SELECT id FROM donations WHERE donor_id = ? AND notes LIKE ? ORDER BY id DESC LIMIT 1',
    [request.donor_id, '%' + marker + '%']
  );

  if (existing.length) {
    await pool.execute(
      'UPDATE donations SET blood_type = ?, component = ?, units = ?, donation_date = ?, facility_name = ?, notes = ? WHERE id = ?',
      [request.blood_type, request.component, request.units, data.scheduled_date, request.facility_name || null, notes, existing[0].id]
    );
    return existing[0].id;
  }

  return create({
    donor_id: request.donor_id,
    blood_type: request.blood_type,
    component: request.component,
    units: request.units,
    donation_date: data.scheduled_date,
    facility_name: request.facility_name,
    notes
  });
}

async function deleteForDonationRequest(request) {
  const marker = requestNoteMarker(request.id);
  await pool.execute(
    'DELETE FROM donations WHERE donor_id = ? AND notes LIKE ?',
    [request.donor_id, '%' + marker + '%']
  );
}

async function latest(limit = 8) {
  const safeLimit = Math.max(1, Math.min(100, Number.parseInt(limit, 10) || 8));
  const [rows] = await pool.query('SELECT * FROM donations ORDER BY donation_date DESC, created_at DESC LIMIT ' + safeLimit);
  return rows;
}

async function allWithDonors() {
  const [rows] = await pool.execute('SELECT dn.*, u.name AS donor_name FROM donations dn JOIN donors d ON d.id = dn.donor_id JOIN users u ON u.id = d.user_id ORDER BY dn.donation_date DESC, dn.created_at DESC');
  return rows;
}

module.exports = { create, saveFromDonationRequest, deleteForDonationRequest, latest, allWithDonors };
