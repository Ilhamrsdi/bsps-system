const xlsx = require('xlsx');
const fs = require('fs');
const path = require('path');

console.log("Reading filtered data...");
const jsonPath = path.join(__dirname, '../storage/app/12_ribu.json');
const rawData = JSON.parse(fs.readFileSync(jsonPath, 'utf8'));

// 1. Group by Kecamatan & Desa
const desaMap = {};
const kecMap = {};

rawData.forEach(item => {
    const kec = item.kecamatan;
    const desa = item.desa_kelurahan;
    const key = `${kec}|||${desa}`;

    if (!desaMap[key]) {
        desaMap[key] = { kecamatan: kec, desa: desa, total: 0 };
    }
    desaMap[key].total += 1;

    if (!kecMap[kec]) {
        kecMap[kec] = { desaSet: new Set(), total: 0 };
    }
    kecMap[kec].desaSet.add(desa);
    kecMap[kec].total += 1;
});

// Sheet 1: Rekap Per Kecamatan
const kecListSorted = Object.keys(kecMap).sort().map((kec, idx) => ({
    'NO': idx + 1,
    'KECAMATAN': kec,
    'JUMLAH DESA': kecMap[kec].desaSet.size,
    'TOTAL PENERIMA': kecMap[kec].total
}));

// Sheet 2: Detail Per Desa
const desaListSorted = Object.values(desaMap).sort((a, b) => {
    if (a.kecamatan === b.kecamatan) return a.desa.localeCompare(b.desa);
    return a.kecamatan.localeCompare(b.kecamatan);
}).map((item, idx) => ({
    'NO': idx + 1,
    'KECAMATAN': item.kecamatan,
    'NAMA DESA / KELURAHAN': item.desa,
    'JUMLAH PENERIMA': item.total
}));

console.log("Creating Excel workbook with 2 sheets...");
const workbook = xlsx.utils.book_new();

const sheetKec = xlsx.utils.json_to_sheet(kecListSorted);
xlsx.utils.book_append_sheet(workbook, sheetKec, "Rekap Per Kecamatan");

const sheetDesa = xlsx.utils.json_to_sheet(desaListSorted);
xlsx.utils.book_append_sheet(workbook, sheetDesa, "Detail Nama Desa");

const outputPath = 'C:/Users/ilham/Downloads/rekap_desa_per_kecamatan.xlsx';
xlsx.writeFile(workbook, outputPath);
console.log(`Excel file saved successfully to ${outputPath}`);
