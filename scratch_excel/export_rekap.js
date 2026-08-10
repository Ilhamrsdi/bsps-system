const xlsx = require('xlsx');
const fs = require('fs');
const path = require('path');

console.log("Reading filtered data...");
const jsonPath = path.join(__dirname, '../storage/app/12_ribu.json');
const rawData = JSON.parse(fs.readFileSync(jsonPath, 'utf8'));

// Group by kecamatan & desa_kelurahan
const rekapMap = {};
rawData.forEach(item => {
    const key = `${item.kecamatan}|||${item.desa_kelurahan}`;
    if (!rekapMap[key]) {
        rekapMap[key] = {
            kecamatan: item.kecamatan,
            desa_kelurahan: item.desa_kelurahan,
            total: 0
        };
    }
    rekapMap[key].total += 1;
});

const sortedRekap = Object.values(rekapMap).sort((a, b) => {
    if (a.kecamatan === b.kecamatan) {
        return a.desa_kelurahan.localeCompare(b.desa_kelurahan);
    }
    return a.kecamatan.localeCompare(b.kecamatan);
});

const formattedRekap = sortedRekap.map((item, index) => ({
    'NO': index + 1,
    'KECAMATAN': item.kecamatan,
    'DESA / KELURAHAN': item.desa_kelurahan,
    'JUMLAH PENERIMA': item.total
}));

console.log("Generating Rekap Excel...");
const worksheet = xlsx.utils.json_to_sheet(formattedRekap);
const workbook = xlsx.utils.book_new();
xlsx.utils.book_append_sheet(workbook, worksheet, "Rekap Per Desa");

const outputPath = 'C:/Users/ilham/Downloads/rekap_penerima_per_desa.xlsx';
xlsx.writeFile(workbook, outputPath);
console.log(`Rekap saved to ${outputPath}`);
