const xlsx = require('xlsx');
const fs = require('fs');
const path = require('path');

console.log("Reading filtered data from storage...");
const jsonPath = path.join(__dirname, '../storage/app/12_ribu.json');
const rawData = JSON.parse(fs.readFileSync(jsonPath, 'utf8'));

console.log(`Formatting ${rawData.length} records...`);
const formattedData = rawData.map((item, index) => ({
    'NO': index + 1,
    'NAMA': item.nama,
    'JENIS KELAMIN': item.jenis_kelamin,
    'NO KTP': item.no_ktp,
    'NO KK': item.no_kk,
    'ALAMAT': item.alamat,
    'DESA / KELURAHAN': item.desa_kelurahan,
    'KECAMATAN': item.kecamatan,
    'KABUPATEN / KOTA': item.kabupaten_kota,
    'PROVINSI': item.provinsi,
    'PENGELOMPOKAN DESIL': item.pengelompokan_desil
}));

console.log("Generating Excel worksheet...");
const worksheet = xlsx.utils.json_to_sheet(formattedData);
const workbook = xlsx.utils.book_new();
xlsx.utils.book_append_sheet(workbook, worksheet, "Data Penerima");

const outputPath = 'C:/Users/ilham/Downloads/data_penerima_12673_filtered.xlsx';
console.log(`Saving Excel to ${outputPath}...`);
xlsx.writeFile(workbook, outputPath);

console.log("Excel file generated successfully!");
