@extends('layouts.partial.app')

@section('title', 'Dinas PUPR Kabupaten Jember - Portal Resmi')
@section('title_header', 'Selamat Datang di Dinas PUPR Jember')
@section('subtitle_header', 'Portal Resmi Informasi Infrastruktur Bina Marga, Sumber Daya Air, dan Cipta Karya Jember')

@push('styles')
<style>
    /* Hero Banner Section */
    .landing-hero {
        background: linear-gradient(135deg, #001737 0%, #002855 60%, #003E75 100%);
        border-radius: var(--radius);
        padding: 50px 40px;
        color: #ffffff;
        margin-bottom: 30px;
        box-shadow: 0 12px 35px rgba(0, 40, 85, 0.2);
        position: relative;
        overflow: hidden;
    }

    .landing-hero::after {
        content: '';
        position: absolute;
        right: -50px;
        bottom: -50px;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(255, 184, 0, 0.18) 0%, rgba(255, 184, 0, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .landing-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 16px;
        border-radius: 20px;
        background: rgba(255, 184, 0, 0.15);
        color: #FFB800;
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 18px;
        border: 1px solid rgba(255, 184, 0, 0.3);
    }

    .landing-hero h1 {
        font-size: 34px;
        font-weight: 900;
        line-height: 1.2;
        margin-bottom: 16px;
        color: #ffffff;
        letter-spacing: -0.5px;
    }

    .landing-hero p {
        font-size: 15px;
        line-height: 1.7;
        color: rgba(255, 255, 255, 0.88);
        margin-bottom: 30px;
        max-width: 780px;
    }

    .landing-cta-group {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
    }

    .btn-gold {
        background: #FFB800;
        color: #001737;
        padding: 12px 24px;
        border-radius: var(--radius-sm);
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 14px rgba(255, 184, 0, 0.35);
        transition: var(--transition);
    }
    .btn-gold:hover {
        background: #ffc933;
        transform: translateY(-2px);
    }

    .btn-glass {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
        padding: 12px 24px;
        border-radius: var(--radius-sm);
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: var(--transition);
    }
    .btn-glass:hover {
        background: rgba(255, 255, 255, 0.22);
        transform: translateY(-2px);
    }

    /* Section Headers */
    .section-title {
        margin-bottom: 24px;
    }
    .section-title h2 {
        font-size: 22px;
        font-weight: 800;
        color: #002855;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-title p {
        font-size: 13px;
        color: #64748b;
        margin-top: 4px;
    }

    /* Stats Grid */
    .stats-public-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 36px;
    }

    .stat-public-card {
        background: #ffffff;
        border-radius: var(--radius);
        padding: 22px 20px;
        box-shadow: 0 4px 16px rgba(0, 40, 85, 0.05);
        border: 1px solid rgba(0, 40, 85, 0.08);
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-public-card .icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .stat-public-card .icon.blue { background: rgba(0, 40, 85, 0.1); color: #002855; }
    .stat-public-card .icon.green { background: rgba(39, 174, 96, 0.12); color: #27ae60; }
    .stat-public-card .icon.orange { background: rgba(255, 184, 0, 0.15); color: #d69e00; }
    .stat-public-card .icon.purple { background: rgba(142, 68, 173, 0.12); color: #8e44ad; }

    .stat-public-card .info .number {
        font-size: 24px;
        font-weight: 900;
        color: #002855;
        line-height: 1.1;
    }
    .stat-public-card .info .label {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
        margin-top: 2px;
    }

    /* Visi Misi Section */
    .visi-misi-card {
        background: #ffffff;
        border-radius: var(--radius);
        padding: 32px;
        border: 1px solid rgba(0, 40, 85, 0.08);
        box-shadow: 0 4px 20px rgba(0, 40, 85, 0.05);
        margin-bottom: 36px;
    }

    .visi-box {
        background: rgba(0, 40, 85, 0.04);
        border-left: 4px solid #002855;
        padding: 18px 24px;
        border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
        margin-bottom: 24px;
    }
    .visi-box h4 {
        font-size: 14px;
        font-weight: 800;
        color: #002855;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }
    .visi-box p {
        font-size: 15px;
        font-weight: 600;
        color: #0A192F;
        font-style: italic;
    }

    .misi-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .misi-item {
        background: #f8fafc;
        border-radius: var(--radius-sm);
        padding: 18px;
        border: 1px solid rgba(0, 40, 85, 0.06);
    }
    .misi-item .num {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #002855;
        color: #ffffff;
        font-size: 12px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }
    .misi-item h5 {
        font-size: 14px;
        font-weight: 700;
        color: #002855;
        margin-bottom: 6px;
    }
    .misi-item p {
        font-size: 12.5px;
        color: #475569;
        line-height: 1.5;
    }

    /* Service Bidang Grid */
    .services-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 36px;
    }

    .service-card {
        background: #ffffff;
        border-radius: var(--radius);
        padding: 26px;
        border: 1px solid rgba(0, 40, 85, 0.08);
        box-shadow: 0 4px 18px rgba(0, 40, 85, 0.05);
        transition: var(--transition);
    }
    .service-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 30px rgba(0, 40, 85, 0.12);
        border-color: #002855;
    }

    .service-card .icon-head {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: rgba(0, 40, 85, 0.08);
        color: #002855;
        font-size: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
    }

    .service-card h3 {
        font-size: 16px;
        font-weight: 800;
        color: #002855;
        margin-bottom: 8px;
    }
    .service-card p {
        font-size: 13px;
        color: #475569;
        line-height: 1.6;
    }

    /* Contact Footer Card */
    .contact-card {
        background: #ffffff;
        border-radius: var(--radius);
        padding: 28px 32px;
        border: 1px solid rgba(0, 40, 85, 0.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        box-shadow: 0 4px 18px rgba(0, 40, 85, 0.05);
    }

    .contact-info-list {
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
    }
    .contact-info-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: #334155;
        font-weight: 600;
    }
    .contact-info-item i {
        color: #002855;
        font-size: 16px;
    }

    @media (max-width: 1024px) {
        .stats-public-grid { grid-template-columns: repeat(2, 1fr); }
        .misi-grid { grid-template-columns: 1fr; }
        .services-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
        .dashboard-content-public {
            padding: 16px 14px 40px !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        .landing-hero {
            padding: 24px 18px !important;
            border-radius: 14px !important;
            width: 100% !important;
            box-sizing: border-box !important;
            margin-bottom: 20px !important;
        }
        .landing-hero h1 { font-size: 22px; line-height: 1.3; }
        .landing-hero p { font-size: 13.5px; line-height: 1.6; }
        .landing-cta-group { flex-direction: column; width: 100%; }
        .landing-cta-group .btn-gold,
        .landing-cta-group .btn-glass { width: 100%; justify-content: center; text-align: center; box-sizing: border-box; }
        .stats-public-grid {
            gap: 14px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .stat-public-card {
            width: 100% !important;
            box-sizing: border-box !important;
        }
    }

    @media (max-width: 480px) {
        .dashboard-content-public {
            padding: 12px 12px 30px !important;
        }
        .stats-public-grid { grid-template-columns: 1fr; }
        .contact-card { flex-direction: column; text-align: center; }
    }
</style>
@endpush

@section('content')
    <!-- Dedicated Public Navbar Component -->
    @include('layouts.navbar_public')

    <main class="dashboard-content dashboard-content-public">
        <!-- Hero Section -->
        <div class="landing-hero">
            <h1>Dinas Pekerjaan Umum dan Penataan Ruang</h1>
            <p>
                Mewujudkan infrastruktur jalan, jembatan, jaringan irigasi pertanian, serta penataan ruang bangunan 
                yang berkualitas, tangguh, dan berkelanjutan di seluruh wilayah Kabupaten Jember.
            </p>
            <div class="landing-cta-group">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-gold">
                        <i class="fas fa-th-large"></i> Akses Dashboard
                    </a>
                @else
                    <a href="{{ url('/login') }}" class="btn-gold">
                        <i class="fas fa-sign-in-alt"></i> Akses Login
                    </a>
                @endauth
                <a href="#profil-dinas" class="btn-glass">
                    <i class="fas fa-info-circle"></i> Profil &amp; Tugas Dinas
                </a>
            </div>
        </div>

        <!-- 4 Stat Counters Grid -->
        <div class="stats-public-grid">
            <div class="stat-public-card">
                <div class="icon blue"><i class="fas fa-road"></i></div>
                <div class="info">
                    <div class="number">1.240+ KM</div>
                    <div class="label">Panjang Jalan Kabupaten</div>
                </div>
            </div>
            <div class="stat-public-card">
                <div class="icon green"><i class="fas fa-water"></i></div>
                <div class="info">
                    <div class="number">86+</div>
                    <div class="label">Daerah Irigasi Pertanian</div>
                </div>
            </div>
            <div class="stat-public-card">
                <div class="icon orange"><i class="fas fa-bridge"></i></div>
                <div class="info">
                    <div class="number">340+</div>
                    <div class="label">Jembatan Terawat</div>
                </div>
            </div>
            <div class="stat-public-card">
                <div class="icon purple"><i class="fas fa-map-location-dot"></i></div>
                <div class="info">
                    <div class="number">31</div>
                    <div class="label">Wilayah Kecamatan Jember</div>
                </div>
            </div>
        </div>

        <!-- Profil & Visi Misi -->
        <div class="visi-misi-card" id="profil-dinas">
            <div class="section-title">
                <h2><i class="fas fa-bullseye"></i> Visi &amp; Misi Dinas PUPR Jember</h2>
                <p>Pedoman utama penyelenggaraan pembangunan fisik dan pengelolaan jaringan irigasi di Kabupaten Jember.</p>
            </div>

            <div class="visi-box">
                <h4>Visi Utama</h4>
                <p>&ldquo;Mewujudkan infrastruktur Jember yang andal, merata, berdaya saing, dan berkelanjutan menuju masyarakat Jember yang sejahtera.&rdquo;</p>
            </div>

            <div class="misi-grid">
                <div class="misi-item">
                    <div class="num">1</div>
                    <h5>Infrastruktur Jalan &amp; Jembatan</h5>
                    <p>Meningkatkan persentase kondisi jalan mantap dan jembatan berkualitas untuk memperlancar konektivitas ekonomi warga.</p>
                </div>
                <div class="misi-item">
                    <div class="num">2</div>
                    <h5>Irigasi &amp; Ketahanan Pangan</h5>
                    <p>Mengoptimalkan rehabilitasi saluran irigasi pertanian dan penanggulangan banjir pendukung ketahanan pangan daerah.</p>
                </div>
                <div class="misi-item">
                    <div class="num">3</div>
                    <h5>Tata Ruang &amp; Keselamatan Building</h5>
                    <p>Pengawasan kesesuaian tata ruang, penerbitan Sertifikat Laik Fungsi (SLF), dan pengelolaan drainase perkotaan.</p>
                </div>
            </div>
        </div>

        <!-- Bidang Tugas & Layanan Utama -->
        <div class="section-title">
            <h2><i class="fas fa-shapes"></i> Bidang Pelayanan &amp; Tugas Utama</h2>
            <p>Pengelompokan struktur kerja teknis pelayanan publik Dinas Pekerjaan Umum Kabupaten Jember.</p>
        </div>

        <div class="services-grid">
            <div class="service-card">
                <div class="service-card .icon-head" style="width:48px;height:48px;border-radius:12px;background:rgba(0,40,85,0.08);color:#002855;font-size:20px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                    <i class="fas fa-road"></i>
                </div>
                <h3>Bidang Bina Marga</h3>
                <p>Melaksanakan pemeliharaan rutin, perbaikan berkala, serta rekonstruksi ruas jalan dan jembatan kabupaten agar aman dilalui pengguna jalan.</p>
            </div>

            <div class="service-card">
                <div class="service-card .icon-head" style="width:48px;height:48px;border-radius:12px;background:rgba(39,174,96,0.12);color:#27ae60;font-size:20px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                    <i class="fas fa-droplet"></i>
                </div>
                <h3>Bidang Sumber Daya Air (SDA)</h3>
                <p>Mengelola pintu air bendung, pengerukan sedimen irigasi, pembangunan tanggul sungai, dan perlindungan DAS di Kabupaten Jember.</p>
            </div>

            <div class="service-card">
                <div class="service-card .icon-head" style="width:48px;height:48px;border-radius:12px;background:rgba(255,184,0,0.15);color:#d69e00;font-size:20px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                    <i class="fas fa-city"></i>
                </div>
                <h3>Bidang Cipta Karya &amp; Tata Ruang</h3>
                <p>Verifikasi teknis kelayakan bangunan gedung (SLF), penataan drainase perkotaan bebas genangan, serta pengawasan tata ruang.</p>
            </div>
        </div>

        <!-- Contact & Location Box -->
        <div class="contact-card">
            <div>
                <h3 style="font-size:18px;font-weight:800;color:#002855;margin-bottom:8px;">Kantor Dinas PUPR Kabupaten Jember</h3>
                <div class="contact-info-list">
                    <div class="contact-info-item">
                        <i class="fas fa-location-dot"></i> Jl. Ahmad Yani No. 80, Kabupaten Jember, Jawa Timur
                    </div>
                    <div class="contact-info-item">
                        <i class="fas fa-clock"></i> Senin - Jumat (07.30 - 16.00 WIB)
                    </div>
                    <div class="contact-info-item">
                        <i class="fas fa-envelope"></i> pupr@jemberkab.go.id
                    </div>
                </div>
            </div>
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-gold" style="white-space:nowrap;">
                    <i class="fas fa-th-large"></i> Akses Dashboard
                </a>
            @else
                <a href="{{ url('/login') }}" class="btn-gold" style="white-space:nowrap;">
                    <i class="fas fa-sign-in-alt"></i> Akses Login
                </a>
            @endauth
        </div>
    </main>
@endsection

@push('scripts')
<script>
    // Lock theme to PUPR official mode for Landing Beranda (Untouched by dynamic theme)
    document.documentElement.setAttribute('data-theme', 'pupr');
</script>
@endpush
