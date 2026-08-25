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

async function main() {
  const pool = await sql.connect(config);
  console.log('=== CONECTADO A ' + config.server + ' / ' + config.database + ' ===\n');

  // Últimos 4 pedidos con monto > 0
  const cab = await pool.request().query(`
    SELECT TOP 4 CFNUMPED, CFFECDOC, CFIMPORTE, CFPORDESCL, CFPORDESES, CFIGV, CFDESCTO, CFDESIMP, CFDESVAL, CFCODMON, CFTIPCAM
    FROM PEDCAB
    WHERE CFIMPORTE > 0
    ORDER BY CFFECDOC DESC
  `);

  for (const c of cab.recordset) {
    console.log('\n========================================');
    console.log('PEDIDO ' + c.CFNUMPED + ' | fecha=' + (c.CFFECDOC ? c.CFFECDOC.toISOString() : '?') + ' | moneda=' + c.CFCODMON + ' | tc=' + c.CFTIPCAM);
    console.log('  CFIMPORTE=' + c.CFIMPORTE + '  CFIGV=' + c.CFIGV + '  CFDESVAL=' + c.CFDESVAL +
      '  CFPORDESCL=' + c.CFPORDESCL + '  CFPORDESES=' + c.CFPORDESES +
      '  CFDESCTO=' + c.CFDESCTO + '  CFDESIMP=' + c.CFDESIMP);
    console.log('  base_sin_igv=' + (c.CFIMPORTE - c.CFIGV).toFixed(6));

    const det = await pool.request()
      .input('ped', sql.VarChar, c.CFNUMPED)
      .query(`
        SELECT DFNUMPED, DFSECUEN, DFCODIGO, DFDESCRI, DFCANTID,
               DFPREC_VEN, DFPREC_ORI, DFDESCTO, DFIGV, DFDESCLI, DFDESESP,
               DFIGVPOR, DFPORDES, DFIMPUS, DFIMPMN, DFARTIGV, DFUNIDAD, DFPORDES
        FROM PEDDET
        WHERE DFNUMPED = @ped
        ORDER BY CAST(DFSECUEN AS INT)
      `);

    console.log('  FILAS DETALLE: ' + det.recordset.length);
    let sumCantXPrecio = 0;      // cant * precio_ven
    let sumImpUS = 0;            // suma DFIMPUS
    let sumImpMN = 0;            // suma DFIMPMN
    let sumDescto = 0;           // suma DFDESCTO
    let sumDesCli = 0;           // suma DFDESCLI
    let sumDesEsp = 0;           // suma DFDESESP
    let sumIgv = 0;              // suma DFIGV

    for (const d of det.recordset) {
      const cantXPrecio = (d.DFCANTID || 0) * (d.DFPREC_VEN || 0);
      sumCantXPrecio += cantXPrecio;
      sumImpUS += d.DFIMPUS || 0;
      sumImpMN += d.DFIMPMN || 0;
      sumDescto += d.DFDESCTO || 0;
      sumDesCli += d.DFDESCLI || 0;
      sumDesEsp += d.DFDESESP || 0;
      sumIgv += d.DFIGV || 0;
      console.log(
        '    ' + d.DFSECUEN +
        ' | cant=' + (d.DFCANTID || 0) +
        ' | pven=' + (d.DFPREC_VEN || 0) +
        ' | pori=' + (d.DFPREC_ORI || 0) +
        ' | descto=' + (d.DFDESCTO || 0) +
        ' | descli=' + (d.DFDESCLI || 0) +
        ' | desesp=' + (d.DFDESESP || 0) +
        ' | igvp%=' + (d.DFIGVPOR || 0) +
        ' | pordes=' + (d.DFPORDES || 0) +
        ' | igv=$' + (d.DFIGV || 0) +
        ' | impus=' + (d.DFIMPUS || 0) +
        ' | impmn=' + (d.DFIMPMN || 0) +
        ' | artigv=' + d.DFARTIGV +
        ' | ' + (d.DFDESCRI || '').substring(0, 40)
      );
    }
    console.log('  SUMA cant*precio=' + sumCantXPrecio.toFixed(6));
    console.log('  SUMA DFIMPUS(S/)= ' + sumImpUS.toFixed(6));
    console.log('  SUMA DFIMPMN= ' + sumImpMN.toFixed(6));
    console.log('  SUMA DFDESCTO=' + sumDescto.toFixed(6) + ' DFDESCLI=' + sumDesCli.toFixed(6) + ' DFDESESP=' + sumDesEsp.toFixed(6));
    console.log('  SUMA DFIGV=' + sumIgv.toFixed(6));
  }

  await pool.close();
}

main().catch(err => {
  console.error('ERROR:', err.message);
  if (err.originalError && err.originalError.message) console.error('ORIGINAL:', err.originalError.message);
  process.exit(1);
});