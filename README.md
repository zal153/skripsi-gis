# Posyandu Locator - Sistem Informasi Geografis (SIG) Pencarian Posyandu Terdekat

Aplikasi Sistem Informasi Geografis (SIG) berbasis web untuk pencarian lokasi Posyandu terdekat di wilayah Kecamatan Arjasa, Kabupaten Jember. Aplikasi ini merupakan bagian dari penelitian tugas akhir (skripsi) untuk mengimplementasikan kombinasi metode **Haversine** dan **Dijkstra**.

---

## 🚀 Fitur Utama

- **Peta Interaktif:** Visualisasi persebaran Posyandu di 6 desa (Candijati, Darsono, Biting, Kemuning Lor, Kamal, Arjasa) menggunakan Leaflet.js dan OpenStreetMap.
- **Penyaringan Jarak (Haversine):** Menghitung jarak garis lurus dari koordinat GPS pengguna ke seluruh Posyandu secara instan untuk mencari yang terdekat.
- **Pencarian Rute Terpendek (Dijkstra & Yen's Algorithm):** Menghitung rute navigasi jalan ril dan menyediakan hingga 3 rute alternatif (*K-Shortest Paths*).
- **Estimasi Waktu (ETA):** Menghitung durasi perjalanan secara dinamis berdasarkan 3 moda transportasi: Jalan Kaki, Mobil, dan Motor.
- **Panel Dashboard Admin:** Pengelolaan data desa, lokasi koordinat Posyandu, data titik jalan (simpul graf), dan ruas jalan (sisi graf) secara dinamis menggunakan SimpleDataTable.

---

## 🛠️ Tech Stack

- **Framework:** Laravel 13 (PHP 8.4)
- **Frontend CSS/JS:** TailwindCSS v4, AlpineJS v3, AdminLTE v4
- **Mapping Engine:** Leaflet.js, OpenStreetMap (OSM)
- **Database:** MariaDB / MySQL
- **Testing Tools:** Pest PHP Framework (Automated Testing)

---

## ⚙️ Petunjuk Pemasangan

### 1. Cloning Proyek & Install Dependensi
```bash
# Clone proyek ini
git clone https://github.com/username/skripsi-gis.git
cd skripsi-gis

# Install package PHP & JavaScript
composer install
npm install
```

### 2. Konfigurasi Environment
Salin file `.env.example` menjadi `.env` dan sesuaikan koneksi database Anda:
```env
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=skripsi_gis
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Migrasi & Impor Data Jalan (OpenStreetMap)
Jalankan migrasi database dan importir data jaringan jalan OSM yang telah disiapkan:
```bash
# Generate key aplikasi
php artisan key:generate

# Migrasi tabel database
php artisan migrate --seed

# Impor data simpul dan ruas jalan OSM
php artisan osm:import-roads
```

### 4. Jalankan Aplikasi
Jalankan development server Laravel dan build asset Vite secara bersamaan:
```bash
# Jalankan server PHP (Default: http://127.0.0.1:8000)
php artisan serve

# Jalankan Vite server untuk frontend
npm run dev
```

---

## 🧪 Pengujian Otomatis
Anda dapat memverifikasi seluruh fungsi logika program (Haversine, Dijkstra Service, CRUD Admin, Route API) menggunakan Pest:
```bash
php artisan test
```
