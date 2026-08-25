const sql = require('mssql');

const config = {
  server: '38.19.151.197',
  port: 1433,
  database: '003BDCOMUN',
  user: 'SOPORTE',
  password: 'SOPORTE',
  options: {
    encrypt: false,
    trustServerCertificate: true,
    connectTimeout: 15000,
    requestTimeout: 60000,
  },
  pool: { max: 5, min: 0, idleTimeoutMillis: 30000 },
};

const MODE = process.argv[2] || 'schema';

async function main() {
  const pool = await sql.connect(config);
  console.log('=== CONECTADO A ' + config.server + ' / ' + config.database + ' ===\n');

  if (MODE === 'schema') {
    const cabCols = await pool.request().query(`
      SELECT COLUMN_NAME, DATA_TYPE,
             ISNULL(CAST(CHARACTER_MAXIMUM_LENGTH AS VARCHAR(10)), '') AS max_len,
             ISNULL(CAST(NUMERIC_PRECISION AS VARCHAR(10)), '') AS prec,
             ISNULL(CAST(NUMERIC_SCALE AS VARCHAR(10)), '') AS scale,
             IS_NULLABLE
      FROM INFORMATION_SCHEMA.COLUMNS
      WHERE TABLE_NAME = 'PEDCAB'
      ORDER BY ORDINAL_POSITION
    `);
    console.log('--- ESTRUCTURA PEDCAB (' + cabCols.recordset.length + ' cols) ---');
    cabCols.recordset.forEach(c => {
      const tipo = c.DATA_TYPE + (c.max_len ? '(' + c.max_len + ')' : (c.prec ? '(' + c.prec + ',' + c.scale + ')' : ''));
      console.log(c.COLUMN_NAME + ' | ' + tipo + ' | null=' + c.IS_NULLABLE);
    });

    const detCols = await pool.request().query(`
      SELECT COLUMN_NAME, DATA_TYPE,
             ISNULL(CAST(CHARACTER_MAXIMUM_LENGTH AS VARCHAR(10)), '') AS max_len,
             ISNULL(CAST(NUMERIC_PRECISION AS VARCHAR(10)), '') AS prec,
             ISNULL(CAST(NUMERIC_SCALE AS VARCHAR(10)), '') AS scale,
             IS_NULLABLE
      FROM INFORMATION_SCHEMA.COLUMNS
      WHERE TABLE_NAME = 'PEDDET'
      ORDER BY ORDINAL_POSITION
    `);
    console.log('\n--- ESTRUCTURA PEDDET (' + detCols.recordset.length + ' cols) ---');
    detCols.recordset.forEach(c => {
      const tipo = c.DATA_TYPE + (c.max_len ? '(' + c.max_len + ')' : (c.prec ? '(' + c.prec + ',' + c.scale + ')' : ''));
      console.log(c.COLUMN_NAME + ' | ' + tipo + ' | null=' + c.IS_NULLABLE);
    });
  }

  if (MODE === 'recent') {
    const cab = await pool.request().query('SELECT TOP 5 * FROM PEDCAB ORDER BY 1 DESC');
    console.log('--- ULTIMOS 5 PEDIDOS (PEDCAB) ---');
    cab.recordset.forEach((r, i) => {
      console.log('\n[' + (i + 1) + '] ' + JSON.stringify(r));
    });
  }

  await pool.close();
}

main().catch(err => {
  console.error('ERROR:', err.message);
  if (err.originalError && err.originalError.message) console.error('ORIGINAL:', err.originalError.message);
  process.exit(1);
});