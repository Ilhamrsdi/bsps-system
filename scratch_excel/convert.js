const xlsx = require('xlsx');
const fs = require('fs');

console.log("Reading file...");
const workbook = xlsx.readFile('C:/Users/ilham/Downloads/12 ribu.xlsx');
const sheetName = workbook.SheetNames[0];
const sheet = workbook.Sheets[sheetName];
const data = xlsx.utils.sheet_to_json(sheet, {header: 1});

console.log("Parsing data (Filtering for Eligible & Desil 1-4)...");
const records = [];
for (let i = 2; i < data.length; i++) {
    const row = data[i];
    if (!row || row.length === 0) continue;
    if (!row[3] && !row[5]) continue;

    const status = row[15] ? row[15].toString().trim() : '';
    const desil = row[17] ? row[17].toString().trim() : '';

    // Filter: STATUS must be 'Eligible' AND Desil must contain 'Desil 1-4'
    if (status === 'Eligible' && desil.includes('Desil 1-4')) {
        records.push({
            nama: row[3] ? row[3].toString().trim() : null,
            jenis_kelamin: row[4] ? row[4].toString().trim() : null,
            no_ktp: row[5] ? row[5].toString().trim() : null,
            no_kk: row[6] ? row[6].toString().trim() : null,
            alamat: row[7] ? row[7].toString().trim() : null,
            desa_kelurahan: row[8] ? row[8].toString().trim() : null,
            kecamatan: row[9] ? row[9].toString().trim() : null,
            kabupaten_kota: row[10] ? row[10].toString().trim() : null,
            provinsi: row[11] ? row[11].toString().trim() : null,
            pengelompokan_desil: desil,
            created_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
            updated_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
        });
    }
}

console.log(`Writing ${records.length} filtered records to JSON...`);
fs.writeFileSync('../storage/app/12_ribu.json', JSON.stringify(records));
console.log("Done.");
