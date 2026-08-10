@extends('layouts.partial.app')

@section('title', 'Isi Data Verval')
@section('title_header', 'Isi Data Dokumen Verval')
@section('subtitle_header', 'Lengkapi data administrasi dan foto untuk calon penerima BSPS')

@push('styles')
<style>
    .form-section {
        background: var(--bg-card); 
        border-radius: var(--radius); 
        padding: 24px; 
        box-shadow: var(--shadow-sm); 
        border: 1px solid rgba(0, 40, 85, 0.06); 
        margin-bottom: 24px;
    }
    .form-section h4 {
        margin-top: 0; 
        color: var(--text-secondary);
        font-weight: 700;
        margin-bottom: 20px;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 13.5px;
        color: var(--text-primary);
    }
    .form-control, .form-select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid rgba(0, 40, 85, 0.15);
        border-radius: var(--radius-sm);
        background: var(--bg-body);
        color: var(--text-primary);
        font-size: 14px;
    }
    .form-control:disabled {
        background: #f4f6f8;
        color: #6b7280;
        cursor: not-allowed;
    }
    .photo-box {
        border: 1.5px dashed rgba(0, 40, 85, 0.2); 
        padding: 20px; 
        border-radius: 8px; 
        text-align: center;
        background: #fcfcfc;
    }
    .photo-box label {
        font-weight: 700;
        margin-bottom: 12px;
        color: var(--text-secondary);
    }
</style>
@endpush

@section('content')
<div class="content-body" style="padding: 24px;">

    <!-- Display Validation Errors if any -->
    @if ($errors->any())
        <div style="background: #fee2e2; border-left: 4px solid #ef4444; color: #b91c1c; padding: 16px; margin-bottom: 24px; border-radius: 4px;">
            <strong>Terdapat kesalahan:</strong>
            <ul style="margin-top: 8px; margin-bottom: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div class="form-section">
        <h4 style="color: var(--primary);"><i class="fas fa-info-circle"></i> Data Penerima (Dari Database)</h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" class="form-control" value="{{ $vervalData->nama }}" disabled>
            </div>
            <div class="form-group">
                <label>Jenis Kelamin</label>
                <input type="text" class="form-control" value="{{ $vervalData->jenis_kelamin }}" disabled>
            </div>
            <div class="form-group">
                <label>No. KTP</label>
                <input type="text" class="form-control" value="{{ $vervalData->no_ktp }}" disabled>
            </div>
            <div class="form-group">
                <label>No. KK</label>
                <input type="text" class="form-control" value="{{ $vervalData->no_kk }}" disabled>
            </div>
            <div class="form-group">
                <label>Alamat Lengkap</label>
                <input type="text" class="form-control" value="{{ $vervalData->alamat }}" disabled>
            </div>
            <div class="form-group">
                <label>Desa/Kelurahan</label>
                <input type="text" class="form-control" value="{{ $vervalData->desa_kelurahan }}" disabled>
            </div>
            <div class="form-group">
                <label>Kecamatan</label>
                <input type="text" class="form-control" value="{{ $vervalData->kecamatan }}" disabled>
            </div>
            <div class="form-group">
                <label>Kabupaten/Kota</label>
                <input type="text" class="form-control" value="{{ $vervalData->kabupaten_kota }}" disabled>
            </div>
            <div class="form-group">
                <label>Provinsi</label>
                <input type="text" class="form-control" value="{{ $vervalData->provinsi }}" disabled>
            </div>
        </div>
    </div>

    <form action="{{ route('data-verval.update', $vervalData->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-section">
            <h4><i class="fas fa-file-contract"></i> Dokumen Administrasi Utama</h4>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                <div class="form-group">
                    <label>KTP (PDF/Gambar)</label>
                    <input type="file" name="ktp" class="form-control" accept="image/*">
                    @if($vervalData->ktp)
                        <small style="color: green; margin-top: 4px; display: block;"><i class="fas fa-check"></i> Sudah diunggah</small>
                    @endif
                </div>
                <div class="form-group">
                    <label>Kartu Keluarga (KK)</label>
                    <input type="file" name="kk" class="form-control" accept="image/*">
                    @if($vervalData->kk)
                        <small style="color: green; margin-top: 4px; display: block;"><i class="fas fa-check"></i> Sudah diunggah</small>
                    @endif
                </div>
                <div class="form-group">
                    <label>Jenis Kepemilikan Lahan</label>
                    <select name="jenis_kepemilikan_lahan" class="form-select">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="SHM" {{ $vervalData->jenis_kepemilikan_lahan == 'SHM' ? 'selected' : '' }}>SHM (Sertifikat Hak Milik)</option>
                        <option value="SHGB" {{ $vervalData->jenis_kepemilikan_lahan == 'SHGB' ? 'selected' : '' }}>SHGB (Sertifikat Hak Guna Bangunan)</option>
                        <option value="Girik/Letter C" {{ $vervalData->jenis_kepemilikan_lahan == 'Girik/Letter C' ? 'selected' : '' }}>Girik / Letter C</option>
                        <option value="SKT" {{ $vervalData->jenis_kepemilikan_lahan == 'SKT' ? 'selected' : '' }}>SKT (Surat Keterangan Tanah)</option>
                        <option value="AJB" {{ $vervalData->jenis_kepemilikan_lahan == 'AJB' ? 'selected' : '' }}>AJB (Akta Jual Beli)</option>
                        <option value="Surat Perjanjian/Izin Tinggal" {{ $vervalData->jenis_kepemilikan_lahan == 'Surat Perjanjian/Izin Tinggal' ? 'selected' : '' }}>Surat Perjanjian / Izin Tinggal</option>
                        <option value="Lainnya" {{ $vervalData->jenis_kepemilikan_lahan == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Sertipikat/Surat Tanah</label>
                    <input type="file" name="sertifikat_tanah" class="form-control" accept="image/*">
                    @if($vervalData->sertifikat_tanah)
                        <small style="color: green; margin-top: 4px; display: block;"><i class="fas fa-check"></i> Sudah diunggah</small>
                    @endif
                </div>
            </div>
        </div>

        <div class="form-section">
            <h4><i class="fas fa-camera"></i> Foto Kondisi Rumah Eksisting</h4>
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
                <div class="photo-box">
                    <label>Sudut Depan</label>
                    <input type="file" name="foto_sudut_depan" class="form-control" accept="image/*">
                    @if($vervalData->foto_sudut_depan)
                        <small style="color: green; margin-top: 8px; display: block;"><i class="fas fa-check"></i> Gambar tersimpan</small>
                    @endif
                </div>
                <div class="photo-box">
                    <label>Sudut Belakang</label>
                    <input type="file" name="foto_sudut_belakang" class="form-control" accept="image/*">
                    @if($vervalData->foto_sudut_belakang)
                        <small style="color: green; margin-top: 8px; display: block;"><i class="fas fa-check"></i> Gambar tersimpan</small>
                    @endif
                </div>
                <div class="photo-box">
                    <label>Bagian Dalam</label>
                    <input type="file" name="foto_bagian_dalam" class="form-control" accept="image/*">
                    @if($vervalData->foto_bagian_dalam)
                        <small style="color: green; margin-top: 8px; display: block;"><i class="fas fa-check"></i> Gambar tersimpan</small>
                    @endif
                </div>
                <div style="grid-column: span 3; display: grid; grid-template-columns: 1fr 1fr; gap: 24px; max-width: 800px; margin: 0 auto; width: 100%;">
                    <div class="photo-box">
                        <label>Sudut Kiri</label>
                        <input type="file" name="foto_sudut_kiri" class="form-control" accept="image/*">
                        @if($vervalData->foto_sudut_kiri)
                            <small style="color: green; margin-top: 8px; display: block;"><i class="fas fa-check"></i> Gambar tersimpan</small>
                        @endif
                    </div>
                    <div class="photo-box">
                        <label>Sudut Kanan</label>
                        <input type="file" name="foto_sudut_kanan" class="form-control" accept="image/*">
                        @if($vervalData->foto_sudut_kanan)
                            <small style="color: green; margin-top: 8px; display: block;"><i class="fas fa-check"></i> Gambar tersimpan</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h4><i class="fas fa-map-marker-alt"></i> Titik Koordinat Lokasi</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                <div class="form-group">
                    <label>Latitude</label>
                    <input type="text" id="disp_latitude" class="form-control" value="{{ $vervalData->latitude }}" disabled placeholder="Menunggu GPS...">
                    <input type="hidden" name="latitude" id="latitude" value="{{ $vervalData->latitude }}">
                </div>
                <div class="form-group">
                    <label>Longitude</label>
                    <input type="text" id="disp_longitude" class="form-control" value="{{ $vervalData->longitude }}" disabled placeholder="Menunggu GPS...">
                    <input type="hidden" name="longitude" id="longitude" value="{{ $vervalData->longitude }}">
                </div>
            </div>
            <small style="color: var(--text-muted);"><i class="fas fa-info-circle"></i> Koordinat lokasi akan terisi otomatis menggunakan GPS perangkat/browser Anda.</small>
        </div>

        <div style="text-align: right; padding-bottom: 40px;">
            <a href="{{ route('data-verval') }}" class="btn btn-outline" style="margin-right: 12px; padding: 12px 24px; border-radius: 8px;">Batal</a>
            <button type="submit" class="btn btn-primary" style="padding: 12px 24px; font-weight: 700; border-radius: 8px;">
                <i class="fas fa-save"></i> Simpan Data
            </button>
        </div>
    </form>
</div>

<!-- Modal GPS (Custom System Modal) -->
<div class="modal-overlay" id="gpsModal">
    <div class="modal-box" style="max-width: 440px;">
        <div class="modal-header" style="background: #fff3cd; border-bottom-color: #ffeeba;">
            <h3 style="color: #856404; display: flex; align-items: center; gap: 10px; font-size: 16px;">
                <i class="fas fa-exclamation-triangle"></i> GPS/Lokasi Dibutuhkan
            </h3>
        </div>

        <div class="modal-body" style="padding: 24px; text-align: center;">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(220, 53, 69, 0.1); color: #dc3545; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px;">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <p style="font-size: 14px; margin-bottom: 0;">Anda harus mengaktifkan dan mengizinkan akses GPS (Lokasi) pada perangkat/browser Anda untuk dapat mengisi data verval ini.</p>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 12px;">Silakan izinkan akses lokasi saat diminta oleh browser.</p>
        </div>

        <div class="modal-footer" style="padding: 16px 20px; background: var(--bg-body); border-top: 1px solid rgba(0, 40, 85, 0.06); display: flex; justify-content: center;">
            <button type="button" class="btn btn-primary" onclick="requestLocation()">
                <i class="fas fa-sync-alt"></i> Coba Lagi
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function requestLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                // Success
                document.getElementById('disp_latitude').value = position.coords.latitude;
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('disp_longitude').value = position.coords.longitude;
                document.getElementById('longitude').value = position.coords.longitude;
                
                if (window.PuprModal) {
                    window.PuprModal.close('gpsModal');
                }
            }, function(error) {
                // Error (denied or unavailable)
                if (window.PuprModal) {
                    window.PuprModal.open('gpsModal');
                }
            }, {
                enableHighAccuracy: true
            });
        } else {
            alert("Browser Anda tidak mendukung Geolocation.");
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        requestLocation();
    });
</script>
@endpush
