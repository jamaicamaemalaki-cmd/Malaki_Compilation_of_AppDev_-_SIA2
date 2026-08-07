require('dotenv').config();
const { pool } = require('../config/db');

async function main() {
  const [rows] = await pool.query('SELECT DATABASE() AS database_name, NOW() AS server_time');
  console.log(rows[0]);
  await pool.end();
}

main().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
