const fs = require('fs');
const path = require('path');
const mysql = require('mysql2/promise');

const caPath = process.env.DB_SSL_CA ? path.resolve(__dirname, '..', process.env.DB_SSL_CA) : null;
const ssl = caPath && fs.existsSync(caPath) ? { ca: fs.readFileSync(caPath) } : undefined;

const baseOptions = {
  host: process.env.DB_HOST,
  port: Number(process.env.DB_PORT || 3306),
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_NAME,
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
  connectTimeout: 10000,
  enableKeepAlive: true,
  keepAliveInitialDelay: 0,
  ssl
};

const rawPool = mysql.createPool(baseOptions);

const transientErrorCodes = new Set([
  'ECONNRESET',
  'ECONNREFUSED',
  'ETIMEDOUT',
  'EPIPE',
  'PROTOCOL_CONNECTION_LOST'
]);

function isTransientDatabaseError(error) {
  return error && transientErrorCodes.has(error.code);
}

function wait(milliseconds) {
  return new Promise((resolve) => setTimeout(resolve, milliseconds));
}

async function retryTransientDatabaseError(operation) {
  let lastError;
  for (let attempt = 0; attempt < 3; attempt += 1) {
    try {
      return await operation();
    } catch (error) {
      lastError = error;
      if (!isTransientDatabaseError(error) || attempt === 2) throw error;
      await wait(100 * (attempt + 1));
    }
  }
  throw lastError;
}

const pool = {
  execute(sql, params) {
    return retryTransientDatabaseError(() => rawPool.execute(sql, params));
  },
  query(sql, params) {
    return retryTransientDatabaseError(() => rawPool.query(sql, params));
  },
  getConnection() {
    return rawPool.getConnection();
  },
  end() {
    return rawPool.end();
  }
};

module.exports = {
  pool,
  sessionStoreOptions: {
    ...baseOptions,
    createDatabaseTable: true,
    schema: {
      tableName: 'sessions',
      columnNames: {
        session_id: 'session_id',
        expires: 'expires',
        data: 'data'
      }
    }
  }
};
