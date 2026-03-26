# Morphling 3D Documentation Task & Guidelines

## Context

Anda adalah seorang _Technical Writer_ sekaligus _Software Architect_. Tugas utama Anda adalah membangun dokumentasi yang komprehensif pada folder `/docs` untuk Laravel package **Morphling 3D**—sebuah DDD (Domain-Driven Design) Scaffolding Generator.

---

## Why this matters

Morphling 3D documentation is only useful if it stays aligned with the actual generator workflow, naming conventions, 4-layer boundaries, and the request lifecycle at runtime.

## Connection to Morphling 3D layers

Each documentation page should explicitly describe how it maps to the execution path:

Delivery (HTTP/UI) -> Application (UseCases, DTOs) -> Domain (business rules, Entities) -> Infrastructure (repositories, integrations).

## Langkah-langkah Eksekusi

### 1. Analisis Kode Proyek
Baca dan pahami seluruh file di:
- `src/Commands/`
- `src/Support/`
- `stubs/`
Tujuannya: untuk mendalami _workflow_ generator serta pemetaan arsitektur layer (_Domain_, _Application_, _Infrastructure_, _Delivery_).

---

### 2. Rancang Struktur Folder Dokumentasi

Buatlah struktur `/docs` seperti berikut:

```
/docs
  ├─ README.md                 # Home & Unique Selling Points
  ├─ installation.md           # Panduan instalasi & composer
  ├─ architecture/
  │    ├─ overview.md
  │    ├─ domain.md
  │    ├─ application.md
  │    ├─ infrastructure.md
  │    └─ delivery.md
  ├─ commands/
  │    ├─ generators.md
  │    └─ management.md
  ├─ features/
  │    ├─ auto-discovery.md
  │    └─ deep-linking.md
  ├─ .nojekyll                 # Agar bisa di-host tanpa build di GitHub Pages
  └─ index.html                # Docsify setup (lihat bawah)
```

---

### 3. Spesifikasi Isi File

#### 3.1. `architecture/*.md`
- Uraikan **tanggung jawab** setiap layer DDD:
  - `Domain`: Business Logic & Entities
  - `Application`: Use Case Orchestration
  - `Infrastructure`: Integrasi eksternal & implementasi teknis
  - `Delivery`: Routes & Controllers
- Kaitkan dengan perintah artisan generator yang relevan di masing-masing layer.

#### 3.2. `commands/generators.md`
- Buat TABEL referensi otomatis dari kelas artisan generator di `src/Commands` (sertakan argumen `{name}`, `{module}`).
- Format contoh:

  | Command Class         | Artisan Command                  | Args              |
  |---------------------- |----------------------------------|-------------------|
  | `ControllerMakeCommand` | `module:make-controller`       | `{name} {module}` |

#### 3.3. `features/deep-linking.md`
- Jelaskan fitur pembukaan editor (Cursor, VSCode, PHPStorm) _direct from browser_ via script di `view.stub`.
- Sertakan kutipan kode aslinya dari _view.stub_ dan contoh penggunaannya.

#### 3.4. `README.md`
- Section utama: **Why Morphling 3D?**
  - Highlight keunggulan dibanding MVC _standard_ Laravel.
  - Tampilkan USP (Unique Selling Points): arsitektur scalable, zero-config, DDD native scaffolding.

---

## Format, Style, & Navigasi

- Gunakan **GitHub-flavored Markdown**.
- Selalu sertakan _code blocks_ (kutipan langsung) dari file di `stubs/` bila relevan.
- Tuliskan dalam bahasa profesional, elegan, dan nada teknis untuk _enterprise/architect audience_.
- Pastikan seluruh _link_ antar `.md` berfungsi dengan benar (relatif antar file) untuk _GitHub navigation/Pages_.

---

## Docsify & Styling Panduan

- Buat `docs/index.html` dengan _starter template_ [Docsify](https://docsify.js.org/) agar dokumentasi dapat langsung diakses tanpa build process.
- Tambahkan file kosong `docs/.nojekyll` untuk mencegah GitHub Pages melakukan build Jekyll.
- Untuk desain visual, gunakan contoh style dari kode berikut (cuplikan dari `stubs/view.stub`):

```html
<body class="min-h-screen bg-[#050505] font-sans text-[#888888] antialiased selection:bg-emerald-500/30">
  <!-- ... -->
  <footer class="mx-auto flex max-w-5xl items-center justify-between border-t border-neutral-900 px-6 py-12">
      <div class="flex items-center gap-4 text-[10px] uppercase tracking-[0.3em] text-neutral-700">
          <span>Morphling Coding 3D Engine</span>
          <span class="h-1 w-1 rounded-full bg-neutral-800"></span>
          <span>2026</span>
      </div>
      <span class="text-[10px] font-bold uppercase tracking-widest text-neutral-500">v{{ $threed_version }}</span>
  </footer>
</body>
```

- **Panduan styling:**  
  - Utamakan tampilan _dark mode_, _sans-serif font_, tone warna _emerald_ dan _neutral_, serta layout yang lebar (max-width: 5xl).
  - Gunakan _card/grid system_ untuk visualisasi highlights keunggulan atau arsitektur.

---

_Seluruh instruksi ditulis dalam Markdown, siap jalankan di proyek real._

---