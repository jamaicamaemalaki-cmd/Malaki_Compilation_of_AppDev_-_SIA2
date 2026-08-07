const { pool } = require('../config/db');

async function available() {
  const [rows] = await pool.execute('SELECT * FROM blood_inventories WHERE units_available > 0 ORDER BY facility_name, blood_type, component');
  return rows;
}

async function allLowStock(threshold) {
  const [rows] = await pool.execute('SELECT * FROM blood_inventories WHERE units_available <= ? ORDER BY units_available ASC, facility_name ASC', [threshold]);
  return rows;
}

async function byFacility(facilityId) {
  const [rows] = await pool.execute('SELECT * FROM blood_inventories WHERE medical_facility_id = ? ORDER BY blood_type, component', [facilityId]);
  return rows;
}

async function sumUnits() {
  const [rows] = await pool.execute('SELECT COALESCE(SUM(units_available), 0) AS total FROM blood_inventories');
  return rows[0].total;
}

async function latest(limit = 8) {
  const safeLimit = Math.max(1, Math.min(100, Number.parseInt(limit, 10) || 8));
  const [rows] = await pool.query('SELECT * FROM blood_inventories ORDER BY updated_at DESC LIMIT ' + safeLimit);
  return rows;
}

async function firstOrCreateAndIncrement(data, unitsToAdd) {
  await pool.execute(
    'INSERT INTO blood_inventories (medical_facility_id, facility_name, blood_type, component, units_available) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE units_available = units_available + VALUES(units_available), facility_name = VALUES(facility_name)',
    [data.medical_facility_id, data.facility_name, data.blood_type, data.component, unitsToAdd]
  );
}

module.exports = { available, allLowStock, byFacility, sumUnits, latest, firstOrCreateAndIncrement };
