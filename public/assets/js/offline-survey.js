/**
 * BSPS Verval - Offline Survey & Auto-Compression Engine
 * Menggunakan IndexedDB untuk menyimpan data & foto di memori HP saat offline,
 * serta melakukan kompresi otomatis dan auto-sync di latar belakang.
 */

const DB_NAME = 'BSPS_OFFLINE_DB';
const DB_VERSION = 2;
const STORE_NAME = 'offline_surveys';
const STORE_USULAN = 'offline_usulan';

// 1. Inisialisasi Database Lokal IndexedDB di HP
function openOfflineDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);

        request.onupgradeneeded = function (e) {
            const db = e.target.result;
            if (!db.objectStoreNames.contains(STORE_NAME)) {
                db.createObjectStore(STORE_NAME, { keyPath: 'id' });
            }
            if (!db.objectStoreNames.contains(STORE_USULAN)) {
                db.createObjectStore(STORE_USULAN, { keyPath: 'id' });
            }
        };

        request.onsuccess = function (e) {
            resolve(e.target.result);
        };

        request.onerror = function (e) {
            console.error('[IndexedDB] Error membuka database:', e);
            reject(e);
        };
    });
}

// 2. Kompresi Foto Ekstrem di HP (Canvas API -> 100-200 KB)
function compressPhoto(file, maxDimension = 1280, quality = 0.72) {
    return new Promise((resolve, reject) => {
        if (!file || !file.type.startsWith('image/')) {
            resolve(null);
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            const img = new Image();
            img.onload = function () {
                let width = img.width;
                let height = img.height;

                if (width > maxDimension || height > maxDimension) {
                    if (width > height) {
                        height = Math.round((height * maxDimension) / width);
                        width = maxDimension;
                    } else {
                        width = Math.round((width * maxDimension) / height);
                        height = maxDimension;
                    }
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                const base64 = canvas.toDataURL('image/jpeg', quality);
                canvas.toBlob(
                    function (blob) {
                        resolve({
                            blob: blob,
                            base64: base64,
                            origSize: file.size,
                            compSize: blob ? blob.size : 0
                        });
                    },
                    'image/jpeg',
                    quality
                );
            };
            img.onerror = reject;
            img.src = e.target.result;
        };
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}

// 3. Simpan Form Survei ke IndexedDB (Saat Mode Offline)
async function saveSurveyToIndexedDB(surveyData) {
    try {
        const db = await openOfflineDB();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_NAME, 'readwrite');
            const store = tx.objectStore(STORE_NAME);
            surveyData.saved_at = new Date().toISOString();
            surveyData.sync_status = 'pending';

            store.put(surveyData);

            tx.oncomplete = function () {
                console.log('[IndexedDB] Sukses menyimpan survei ID:', surveyData.id);
                updatePendingBadgeUI();
                resolve(true);
            };

            tx.onerror = function (e) {
                console.error('[IndexedDB] Gagal menyimpan survei:', e);
                reject(e);
            };
        });
    } catch (err) {
        console.error('[IndexedDB] Exception save:', err);
        return false;
    }
}

// 4. Ambil Semua Data Survei Tertunda di IndexedDB
async function getAllPendingSurveys() {
    try {
        const db = await openOfflineDB();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_NAME, 'readonly');
            const store = tx.objectStore(STORE_NAME);
            const request = store.getAll();

            request.onsuccess = function () {
                resolve(request.result || []);
            };
            request.onerror = reject;
        });
    } catch (err) {
        return [];
    }
}

// 5. Hapus Data Survei dari IndexedDB setelah Sukses Diunggah
async function removeSurveyFromIndexedDB(id) {
    try {
        const db = await openOfflineDB();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_NAME, 'readwrite');
            const store = tx.objectStore(STORE_NAME);
            store.delete(id);

            tx.oncomplete = function () {
                updatePendingBadgeUI();
                resolve(true);
            };
            tx.onerror = reject;
        });
    } catch (err) {
        return false;
    }
}

// 6. Toast Notification Helper (PUPR Style)
function showPuprToast(message, type = 'success') {
    let container = document.getElementById('puprToastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'puprToastContainer';
        container.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:99999;display:flex;flex-direction:column;gap:10px;pointer-events:none;';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    const bg = type === 'success' ? '#15803d' : (type === 'warning' ? '#b45309' : '#b91c1c');
    const icon = type === 'success' ? 'fa-circle-check' : (type === 'warning' ? 'fa-cloud-arrow-up' : 'fa-triangle-exclamation');

    toast.style.cssText = `background:${bg};color:#ffffff;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:700;display:flex;align-items:center;gap:10px;box-shadow:0 8px 24px rgba(0,0,0,0.25);transform:translateY(20px);opacity:0;transition:all 0.3s cubic-bezier(0.16, 1, 0.3, 1);pointer-events:auto;`;
    toast.innerHTML = `<i class="fas ${icon}" style="font-size:16px;"></i> <span>${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';
    }, 20);

    setTimeout(() => {
        toast.style.transform = 'translateY(20px)';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 350);
    }, 4500);
}

// 7. Update Badge Indikator Jumlah Survei Tertunda di Layar (Dinonaktifkan agar layar bersih)
async function updatePendingBadgeUI() {
    const badge = document.getElementById('pendingSyncBadge');
    if (badge) badge.remove();
}

// 8. Auto-Sync di Layar Belakang (100% Silent Background Sync - Tanpa Loading)
let isSyncing = false;
async function syncPendingSurveys() {
    if (!navigator.onLine || isSyncing) return;

    const pendingList = await getAllPendingSurveys();
    if (pendingList.length === 0) return;

    isSyncing = true;
    
    let successCount = 0;

    for (const item of pendingList) {
        try {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');

            // Masukkan data field teks
            for (const [key, val] of Object.entries(item.fields || {})) {
                if (val !== null && val !== undefined) {
                    formData.append(key, val);
                }
            }

            // Masukkan foto / dokumen Base64 (DataURL)
            for (const [photoKey, photoVal] of Object.entries(item.photos || {})) {
                const dataStr = typeof photoVal === 'string' ? photoVal : (photoVal && photoVal.dataUrl ? photoVal.dataUrl : '');
                
                if (dataStr && dataStr.startsWith('data:')) {
                    const res = await fetch(dataStr);
                    const blob = await res.blob();
                    
                    let ext = 'jpg';
                    if (dataStr.startsWith('data:application/pdf')) ext = 'pdf';
                    else if (dataStr.startsWith('data:image/png')) ext = 'png';
                    
                    formData.append(photoKey, blob, `${photoKey}_${item.id}.${ext}`);
                }
            }

            const response = await fetch(`/survey/${item.id}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                await removeSurveyFromIndexedDB(item.id);
                successCount++;

                // Perbarui status badge di baris tabel secara realtime
                const statusBadge = document.getElementById(`syncStatus_${item.id}`);
                if (statusBadge) {
                    statusBadge.style.background = 'rgba(39, 174, 96, 0.15)';
                    statusBadge.style.color = '#15803d';
                    statusBadge.style.boxShadow = 'none';
                    statusBadge.innerHTML = `<i class="fas fa-check-circle" style="color:#16a34a;"></i> Selesai (Tersinkron)`;
                }
            }
        } catch (err) {
            console.error('[Sync] Gagal mengunggah item ID:', item.id, err);
        }
    }

    isSyncing = false;

    if (successCount > 0) {
        showPuprToast(`🎉 ${successCount} data survei offline berhasil disinkronkan ke server!`, 'success');
    }
}

// 9. Simpan Data Usulan Baru ke IndexedDB (Saat Mode Offline)
async function saveUsulanToIndexedDB(usulanData) {
    try {
        const db = await openOfflineDB();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_USULAN, 'readwrite');
            const store = tx.objectStore(STORE_USULAN);
            if (!usulanData.id) {
                usulanData.id = 'usulan_off_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5);
            }
            usulanData.saved_at = usulanData.saved_at || new Date().toISOString();
            usulanData.sync_status = 'pending';
            delete usulanData.sync_error;

            store.put(usulanData);

            tx.oncomplete = function () {
                console.log('[IndexedDB] Sukses menyimpan usulan baru offline NIK:', usulanData.no_ktp);
                window.dispatchEvent(new CustomEvent('offlineUsulanUpdated', { detail: { action: 'save', item: usulanData } }));
                resolve(true);
            };

            tx.onerror = function (e) {
                console.error('[IndexedDB] Gagal menyimpan usulan baru:', e);
                reject(e);
            };
        });
    } catch (err) {
        console.error('[IndexedDB] Exception saveUsulan:', err);
        return false;
    }
}

// 10. Ambil Semua Usulan Baru Tertunda di IndexedDB
async function getAllPendingUsulan() {
    try {
        const db = await openOfflineDB();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_USULAN, 'readonly');
            const store = tx.objectStore(STORE_USULAN);
            const request = store.getAll();

            request.onsuccess = function () {
                resolve(request.result || []);
            };
            request.onerror = reject;
        });
    } catch (err) {
        return [];
    }
}

// 11. Perbarui Data Usulan di IndexedDB (misal update status error)
async function updateUsulanInIndexedDB(usulanData) {
    try {
        const db = await openOfflineDB();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_USULAN, 'readwrite');
            const store = tx.objectStore(STORE_USULAN);
            store.put(usulanData);

            tx.oncomplete = function () {
                window.dispatchEvent(new CustomEvent('offlineUsulanUpdated', { detail: { action: 'update', item: usulanData } }));
                resolve(true);
            };
            tx.onerror = reject;
        });
    } catch (err) {
        return false;
    }
}

// 12. Hapus Usulan dari IndexedDB setelah Sukses Diunggah atau Dihapus Manual
async function removeUsulanFromIndexedDB(id) {
    try {
        const db = await openOfflineDB();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_USULAN, 'readwrite');
            const store = tx.objectStore(STORE_USULAN);
            store.delete(id);

            tx.oncomplete = function () {
                window.dispatchEvent(new CustomEvent('offlineUsulanUpdated', { detail: { action: 'delete', id: id } }));
                resolve(true);
            };
            tx.onerror = reject;
        });
    } catch (err) {
        return false;
    }
}

// 13. Sync Usulan Baru ke Server saat Terhubung Kembali
let isUsulanSyncing = false;
async function syncPendingUsulan(isManual = false) {
    if (!navigator.onLine) {
        if (isManual) {
            showPuprToast('⚠️ Perangkat Anda masih dalam keadaan offline (tidak ada koneksi internet).', 'warning');
        }
        return;
    }

    if (isUsulanSyncing) return;

    const pendingList = await getAllPendingUsulan();
    if (pendingList.length === 0) {
        if (isManual) {
            showPuprToast('ℹ️ Tidak ada antrian usulan baru offline yang perlu disinkronkan.', 'info');
        }
        return;
    }

    isUsulanSyncing = true;
    let successCount = 0;
    const syncErrors = [];

    for (const item of pendingList) {
        try {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
            formData.append('nama', item.nama || '');
            formData.append('no_ktp', item.no_ktp || '');
            formData.append('no_kk', item.no_kk || '');
            formData.append('jenis_kelamin', item.jenis_kelamin || 'L');
            formData.append('pengelompokan_desil', item.pengelompokan_desil || 'Usulan Baru Lapangan');
            formData.append('dusun', item.dusun || '');
            formData.append('rt', item.rt || '');
            formData.append('rw', item.rw || '');
            formData.append('alamat', item.alamat || '');

            const response = await fetch('/petugas/usulkan-penerima', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const resData = await response.json().catch(() => ({ success: true }));
                if (resData.success !== false) {
                    await removeUsulanFromIndexedDB(item.id);
                    successCount++;
                }
            } else {
                const errData = await response.json().catch(() => ({}));
                let errorMsg = errData.message;
                if (!errorMsg && errData.errors) {
                    errorMsg = Object.values(errData.errors).flat().join(', ');
                }
                if (!errorMsg) {
                    if (response.status === 419) {
                        errorMsg = 'Sesi / Token CSRF telah kadaluarsa. Silakan refresh halaman web.';
                    } else {
                        errorMsg = `Data ditolak oleh server (Kode Status HTTP: ${response.status}). Periksa format NIK / KK.`;
                    }
                }

                console.warn('[Sync Usulan] Validasi Gagal untuk NIK:', item.no_ktp, errorMsg);
                
                // Simpan pesan error di item IndexedDB agar user tahu di dashboard
                item.sync_status = 'error';
                item.sync_error = errorMsg;
                item.error_at = new Date().toISOString();
                await updateUsulanInIndexedDB(item);

                syncErrors.push({
                    id: item.id,
                    nama: item.nama || 'Tanpa Nama',
                    no_ktp: item.no_ktp || '-',
                    error: errorMsg
                });
            }
        } catch (err) {
            console.error('[Sync Usulan] Network exception NIK:', item.no_ktp, err);
        }
    }

    isUsulanSyncing = false;

    // Trigger event notifikasi list
    window.dispatchEvent(new CustomEvent('offlineUsulanUpdated', { 
        detail: { action: 'sync_completed', successCount, syncErrors } 
    }));

    if (successCount > 0) {
        showPuprToast(`🎉 ${successCount} usulan baru offline berhasil disimpan ke server!`, 'success');
    }

    if (syncErrors.length > 0) {
        window.dispatchEvent(new CustomEvent('offlineUsulanSyncError', { detail: syncErrors }));
        showPuprToast(`⚠️ Terdapat ${syncErrors.length} usulan offline yang gagal validasi server (periksa NIK/No.KK).`, 'error');
    }

    if (successCount > 0 && syncErrors.length === 0 && !isManual) {
        setTimeout(() => {
            if (window.location.pathname.includes('/petugas/')) {
                window.location.reload();
            }
        }, 1800);
    }
}

// Event Listeners Auto-Sync
window.addEventListener('online', function () {
    console.log('[PWA] Terhubung ke internet, memulai auto-sync di latar belakang...');
    setTimeout(() => {
        syncPendingSurveys();
        syncPendingUsulan();
    }, 1200);
});

document.addEventListener('DOMContentLoaded', function () {
    updatePendingBadgeUI();
    if (navigator.onLine) {
        setTimeout(() => {
            syncPendingSurveys();
            syncPendingUsulan();
        }, 2500);
    }
});

// Export Global
window.BspsOffline = {
    compressPhoto,
    saveSurveyToIndexedDB,
    getAllPendingSurveys,
    removeSurveyFromIndexedDB,
    syncPendingSurveys,
    saveUsulanToIndexedDB,
    getAllPendingUsulan,
    updateUsulanInIndexedDB,
    removeUsulanFromIndexedDB,
    syncPendingUsulan,
    updatePendingBadgeUI,
    showPuprToast
};
