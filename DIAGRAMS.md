# Diagram Sistem — Sekretariat Jemaat GPdI

---

## 1. Skema Database (ERD)

```mermaid
erDiagram
    members {
        bigint id PK
        string id_jemaat UK "DDMMYYYY, auto-generate"
        string nama_lengkap
        enum jenis_kelamin "Laki-laki | Perempuan"
        date tanggal_lahir
        string tempat_lahir
        string alamat
        string no_telepon
        enum status_aktif "Aktif | Tidak Aktif | Dipindahkan"
        string password "bcrypt"
        string role "admin | member"
        timestamp deleted_at "soft delete"
        timestamp created_at
        timestamp updated_at
    }

    letters {
        bigint id PK
        bigint member_id FK "CASCADE"
        bigint member_pria_id FK "nullable, RESTRICT"
        bigint member_wanita_id FK "nullable, RESTRICT"
        string tipe_surat "nama tampilan"
        string letter_type "slug"
        string nomor_surat UK "NNN/GPdI/SA/XX/YYYY"
        date tanggal_surat
        text keterangan "nullable"
        string pdf_path "nullable"
        date tgl_mulai_tugas "nullable — TP"
        date tgl_akhir_tugas "nullable — TP"
        text tujuan_tugas "nullable — TP"
        int tahun_bergabung "nullable — KJA"
        string asal_sekolah "nullable — NS"
        string kelas "nullable — NS"
        string semester "nullable — NS"
        int nilai "nullable — NS"
        string nama_ayah "nullable — PA"
        string nama_ibu "nullable — PA"
        string nama_anak "nullable — PA"
        string tempat_lahir_anak "nullable — PA"
        date tanggal_lahir_anak "nullable — PA"
        date tanggal_pernikahan "nullable — PP"
        timestamp created_at
        timestamp updated_at
    }

    letter_number_counters {
        bigint id PK
        string letter_type UK "slug tipe surat"
        int year "tahun counter"
        int next_number "default 1"
        string abbreviation "TP | SP | KJA | NS | PB | PA | PP"
        timestamp created_at
        timestamp updated_at
    }

    personal_access_tokens {
        bigint id PK
        string tokenable_type "polymorphic"
        bigint tokenable_id "polymorphic → members.id"
        text name
        string token UK "64 char"
        text abilities "nullable"
        timestamp last_used_at "nullable"
        timestamp expires_at "nullable"
        timestamp created_at
        timestamp updated_at
    }

    members ||--o{ letters : "member_id (surat utama)"
    members ||--o{ letters : "member_pria_id (pernikahan)"
    members ||--o{ letters : "member_wanita_id (pernikahan)"
    members ||--o{ personal_access_tokens : "tokenable"
```

---

## 2. Arsitektur Sistem

```mermaid
graph TB
    subgraph Klien["Klien"]
        FE["Laravel Blade Frontend\n(port 8001)"]
        MOB["Mobile App"]
    end

    subgraph API["Laravel REST API (port 8000)"]
        direction TB
        MW_AUTH["Middleware: auth:sanctum"]
        MW_ROLE["Middleware: role:admin\n→ CheckRole"]

        subgraph PUB["Public"]
            EP_LOGIN["POST /api/auth/login"]
            EP_HEALTH["GET /api/health"]
        end

        subgraph MEMBER["Member (auth:sanctum)"]
            EP_ME["GET|PUT /api/me"]
            EP_ME_LETTERS["GET /api/me/letters"]
            EP_ME_DL["GET /api/me/letters/{id}/download"]
        end

        subgraph ADMIN["Admin (auth:sanctum + role:admin)"]
            EP_ADM_MEM["GET|POST /api/admin/members"]
            EP_ADM_MEM_ID["GET|PUT|DELETE /api/admin/members/{id}"]
            EP_ADM_LET["GET|POST /api/admin/letters"]
            EP_ADM_LET_ID["GET|DELETE /api/admin/letters/{id}"]
            EP_ADM_PDF["GET /api/admin/letters/{id}/pdf"]
        end
    end

    subgraph SERVICES["Services & Logic"]
        OBS["MemberObserver\n(auto-generate id_jemaat)"]
        SVC["LetterTemplateService\n(generate nomor surat)"]
        DOMPDF["DomPDF\n(render PDF)"]
        BLADE["letter/print.blade.php"]
    end

    subgraph DB["Database (MySQL)"]
        TBL_MEM[("members")]
        TBL_LET[("letters")]
        TBL_CTR[("letter_number_counters")]
        TBL_TOK[("personal_access_tokens")]
    end

    subgraph STORAGE["Storage"]
        FILES["storage/app/letters/\n*.pdf"]
    end

    FE --> API
    MOB --> API

    EP_LOGIN --> MW_AUTH
    EP_ME --> MW_AUTH
    EP_ME_LETTERS --> MW_AUTH
    EP_ME_DL --> MW_AUTH

    MW_AUTH --> MW_ROLE
    MW_ROLE --> EP_ADM_MEM
    MW_ROLE --> EP_ADM_MEM_ID
    MW_ROLE --> EP_ADM_LET
    MW_ROLE --> EP_ADM_LET_ID
    MW_ROLE --> EP_ADM_PDF

    EP_ADM_MEM --> OBS
    EP_ADM_MEM_ID --> OBS
    OBS --> TBL_MEM

    EP_ADM_LET --> SVC
    SVC --> TBL_CTR
    SVC --> TBL_LET

    EP_ADM_PDF --> DOMPDF
    DOMPDF --> BLADE
    DOMPDF --> FILES

    EP_ME_DL --> FILES

    TBL_MEM --> TBL_TOK
```

---

## 3. Alur Autentikasi

```mermaid
flowchart TD
    A([Mulai]) --> B["Kirim POST /api/auth/login\n{id_jemaat, password}"]
    B --> C{id_jemaat\nditemukan?}
    C -- Tidak --> ERR1["401 — ID atau password salah"]
    C -- Ya --> D{Password\ncocok?}
    D -- Tidak --> ERR1
    D -- Ya --> E{status_aktif\n= 'Aktif'?}
    E -- Tidak --> ERR2["403 — Akun tidak aktif"]
    E -- Ya --> F["Buat Sanctum Token\n(personal_access_tokens)"]
    F --> G["200 — { member, token }"]
    G --> H["Simpan token di header\nAuthorization: Bearer {token}"]
    H --> I{Role?}
    I -- admin --> J["Akses endpoint /api/me/*\n+ /api/admin/*"]
    I -- member --> K["Akses endpoint /api/me/* saja"]

    ERR1 --> Z([Selesai])
    ERR2 --> Z
    J --> Z
    K --> Z
```

---

## 4. Alur Manajemen Jemaat (Admin)

```mermaid
flowchart TD
    A([Admin Login]) --> B{Pilih Aksi}

    B --> C["Lihat Daftar Jemaat\nGET /api/admin/members\n?search=&status=&per_page="]
    C --> C1["Tampil paginated list\n+ filter aktif/tidak aktif"]

    B --> D["Tambah Jemaat\nPOST /api/admin/members"]
    D --> D1{Validasi\nRequest}
    D1 -- Gagal --> D2["422 — errors validasi"]
    D1 -- Lulus --> D3["Member::create(data)\n+ password = bcrypt('12345')"]
    D3 --> D4["MemberObserver::creating()\nGenerate id_jemaat = DDMMYYYY"]
    D4 --> D5{id_jemaat\nduplikat?}
    D5 -- Ya --> D6["Tambah counter\n17081995 → 170819951"]
    D5 -- Tidak --> D7["Simpan ke DB"]
    D6 --> D7 
    D7 --> D8["201 — member object"]

    B --> E["Update Jemaat\nPUT /api/admin/members/{id}"]
    E --> E1{Validasi\nRequest}
    E1 -- Gagal --> E2["422 — errors validasi"]
    E1 -- Lulus --> E3{tanggal_lahir\nberubah?}
    E3 -- Ya --> E4["MemberObserver::updating()\nRegenerasi id_jemaat"]
    E3 -- Tidak --> E5["Update field lain"]
    E4 --> E5
    E5 --> E6["200 — member object terbaru"]

    B --> F["Hapus Jemaat\nDELETE /api/admin/members/{id}"]
    F --> F1["forceDelete() member\n+ CASCADE delete semua letters"]
    F1 --> F2["204 No Content"]
```

---

## 5. Alur Pembuatan Surat (Admin)

```mermaid
flowchart TD
    A([Admin]) --> B["POST /api/admin/letters\n{letter_type, tanggal_surat, ...}"]
    B --> C{Validasi\nStoreLetterRequest}
    C -- Gagal --> ERR["422 — errors validasi"]
    C -- Lulus --> D{letter_type?}

    D --> D1["surat_tugas_pelayanan\n+ member_id, tgl_mulai, tgl_akhir, tujuan"]
    D --> D2["surat_pengantar\n+ member_id, keterangan"]
    D --> D3["surat_keterangan_jemaat_aktif\n+ member_id, tahun_bergabung"]
    D --> D4["surat_nilai_sekolah\n+ member_id, sekolah, kelas, semester, nilai"]
    D --> D5["surat_pengajuan_baptisan\n+ member_id"]
    D --> D6["surat_pengajuan_penyerahan_anak\n+ member_id, data anak & orang tua"]
    D --> D7["surat_pengajuan_pernikahan\n+ member_pria_id, member_wanita_id, tgl_nikah"]

    D1 & D2 & D3 & D4 & D5 & D6 & D7 --> E

    E["LetterTemplateService::generateLetterNumber()\n→ cek letter_number_counters"]
    E --> F{Record\nada?}
    F -- Tidak --> G["firstOrCreate() — buat counter baru\nnext_number = 1"]
    F -- Ya --> H["Ambil next_number"]
    G & H --> I["Format: NNN/GPdI/SA/XX/YYYY\nIncrement next_number di counter"]
    I --> J["Letter::create(data)\npdf_path = null"]
    J --> K["201 — letter object"]
```

---

## 6. Alur Download PDF

```mermaid
flowchart TD
    A([Request Download PDF]) --> B{Dari siapa?}

    B -- "Admin\nGET /api/admin/letters/{id}/pdf" --> C{pdf_path\ntersimpan & file ada?}
    C -- Ya --> D["Langsung download\nfile tersimpan"]
    C -- Tidak --> E["Generate PDF baru\n→ DomPDF::loadView('letter.print')"]
    E --> F["Render Blade template\ndengan data letter + relasi member"]
    F --> G["Simpan ke\nstorage/app/letters/{filename}.pdf"]
    G --> H["Update letters.pdf_path\ndi database"]
    H --> I["Download PDF\n(Content-Type: application/pdf)"]
    D --> I

    B -- "Member\nGET /api/me/letters/{id}/download" --> J{Surat milik\nuser ini?}
    J -- Tidak --> ERR1["404 — Surat tidak ditemukan"]
    J -- Ya --> K{pdf_path ada\n& file exist?}
    K -- Tidak --> ERR2["404 — File PDF belum tersedia"]
    K -- Ya --> L["Download PDF tersimpan"]

    I --> Z([File PDF terunduh])
    L --> Z
    ERR1 --> ZE([Error])
    ERR2 --> ZE
```

---

## 7. Alur Lengkap Sistem (End-to-End)

```mermaid
sequenceDiagram
    actor Admin
    actor Member
    participant FE as Frontend (port 8001)
    participant API as Laravel API (port 8000)
    participant DB as Database
    participant Storage as File Storage

    Note over Admin,Storage: === SKENARIO: Admin membuat surat, Member download ===

    Admin->>FE: Login (id_jemaat + password)
    FE->>API: POST /api/auth/login
    API->>DB: Cek Member + Hash password
    DB-->>API: Member ditemukan & aktif
    API->>DB: Buat personal_access_token
    API-->>FE: { member, token }
    FE-->>Admin: Masuk dashboard

    Admin->>FE: Tambah jemaat baru
    FE->>API: POST /api/admin/members
    API->>API: MemberObserver: generate id_jemaat
    API->>DB: INSERT members
    DB-->>API: Member tersimpan
    API-->>FE: 201 member object
    FE-->>Admin: Jemaat berhasil ditambahkan

    Admin->>FE: Buat surat untuk jemaat
    FE->>API: POST /api/admin/letters
    API->>DB: Cek letter_number_counters
    DB-->>API: Counter (next_number = 1)
    API->>DB: INSERT letters (pdf_path = null)
    API->>DB: UPDATE next_number++
    API-->>FE: 201 surat (has_pdf = false)
    FE-->>Admin: Surat berhasil dibuat

    Admin->>FE: Download PDF surat
    FE->>API: GET /api/admin/letters/{id}/pdf
    API->>API: DomPDF render letter.print.blade.php
    API->>Storage: Simpan PDF file
    API->>DB: UPDATE letters.pdf_path
    API-->>FE: Binary PDF file
    FE-->>Admin: File PDF terunduh

    Note over Member,Storage: === SKENARIO: Member melihat dan download suratnya ===

    Member->>FE: Login
    FE->>API: POST /api/auth/login
    API-->>FE: { member (role=member), token }

    Member->>FE: Lihat surat saya
    FE->>API: GET /api/me/letters?keyword=pengantar
    API->>DB: SELECT letters WHERE member_id = auth.id
    DB-->>API: Array surat
    API-->>FE: 200 [{ has_pdf: true, ... }]
    FE-->>Member: Daftar surat tampil

    Member->>FE: Download PDF
    FE->>API: GET /api/me/letters/{id}/download
    API->>DB: Verifikasi member_id = auth.id
    API->>Storage: Baca file PDF
    API-->>FE: Binary PDF file
    FE-->>Member: File PDF terunduh
```

---

## 8. Struktur Nomor Surat

```mermaid
graph LR
    A["001"] --> |"Nomor urut\n(3 digit, reset per tahun)"| SEP1["/"]
    SEP1 --> B["GPdI"] --> |"Organisasi"| SEP2["/"]
    SEP2 --> C["SA"] --> |"Jemaat Sahabat Allah"| SEP3["/"]
    SEP3 --> D["SP"] --> |"Kode tipe surat"| SEP4["/"]
    SEP4 --> E["2026"] --> |"Tahun"| RESULT

    RESULT["Contoh: 001/GPdI/SA/SP/2026"]

    style RESULT fill:#f0f4ff,stroke:#4a6cf7,stroke-width:2px
```

| Kode | Tipe Surat |
|------|-----------|
| TP | Surat Tugas Pelayanan |
| SP | Surat Pengantar |
| KJA | Surat Keterangan Jemaat Aktif |
| NS | Surat Nilai Sekolah |
| PB | Surat Pengajuan Baptisan |
| PA | Surat Pengajuan Penyerahan Anak |
| PP | Surat Pengajuan Pernikahan |

---

## 9. Relasi Tipe Surat → Field Database

```mermaid
graph TD
    LT["letters table"] --> ALL["Field Umum\nid, member_id, tipe_surat, letter_type\nnomor_surat, tanggal_surat, keterangan\npdf_path, created_at, updated_at"]

    LT --> TP["Surat Tugas Pelayanan\ntgl_mulai_tugas\ntgl_akhir_tugas\ntujuan_tugas"]
    LT --> KJA["Surat Keterangan Jemaat Aktif\ntahun_bergabung"]
    LT --> NS["Surat Nilai Sekolah\nasal_sekolah\nkelas\nsemester\nnilai"]
    LT --> PA["Surat Penyerahan Anak\nnama_ayah\nnama_ibu\nnama_anak\ntempat_lahir_anak\ntanggal_lahir_anak"]
    LT --> PP["Surat Pengajuan Pernikahan\nmember_pria_id → members\nmember_wanita_id → members\ntanggal_pernikahan"]

    SP["Surat Pengantar\n(hanya field keterangan)"] -.-> LT
    PB["Surat Pengajuan Baptisan\n(hanya field umum)"] -.-> LT

    style TP fill:#fff3e0
    style KJA fill:#e8f5e9
    style NS fill:#e3f2fd
    style PA fill:#fce4ec
    style PP fill:#f3e5f5
    style SP fill:#e0f7fa
    style PB fill:#f9fbe7
```
