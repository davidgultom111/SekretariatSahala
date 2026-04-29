# 🚀 QUICKSTART - REST API Sekretariat Gereja

## 5 Menit Setup & Testing

### 1️⃣ Seed Test Data

```bash
cd d:\Skripsi\sekretariat

# Seed test members
php artisan db:seed --class=MemberSeeder
```

**Test Accounts yang akan dibuat:**

- Regular Member: `15051980` / password `12345`
- Admin: `01011980` / password `12345`

### 2️⃣ Test Login di Terminal

```bash
# Terminal 1: Start Laravel server (jika belum)
php artisan serve

# Terminal 2: Login request
curl -X POST http://localhost:8000/api/jemaat/login \
  -H "Content-Type: application/json" \
  -d '{
    "id_jemaat": "15051980",
    "password": "12345"
  }'
```

**Response:**

```json
{
  "status": "success",
  "message": "Login berhasil",
  "data": {
    "member": { ... },
    "token": "1|XXXXX..."
  }
}
```

**Simpan token untuk test berikutnya:**

```bash
TOKEN="1|XXXXX..."
```

### 3️⃣ Test Get Biodata

```bash
curl -X GET http://localhost:8000/api/jemaat/biodata \
  -H "Authorization: Bearer $TOKEN"
```

### 4️⃣ Test Update Biodata

```bash
curl -X PUT http://localhost:8000/api/jemaat/biodata \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "nama_lengkap": "Budi Updated",
    "alamat": "Jl. Baru No. 999"
  }'
```

### 5️⃣ Test Get Surat

```bash
curl -X GET http://localhost:8000/api/jemaat/surat \
  -H "Authorization: Bearer $TOKEN"
```

---

## 🔗 API Endpoints

| Method | Endpoint                          | Auth | Description                |
| ------ | --------------------------------- | ---- | -------------------------- |
| POST   | `/api/jemaat/login`               | ❌   | Login                      |
| POST   | `/api/jemaat/logout`              | ✅   | Logout                     |
| GET    | `/api/jemaat/biodata`             | ✅   | Get biodata                |
| PUT    | `/api/jemaat/biodata`             | ✅   | Update biodata             |
| GET    | `/api/jemaat/surat`               | ✅   | Get surat (with ?keyword=) |
| GET    | `/api/jemaat/surat/{id}/download` | ✅   | Download PDF               |
| DELETE | `/api/admin/jemaat/{id}`          | ✅🔐 | Hapus jemaat               |
| DELETE | `/api/admin/surat/{id}`           | ✅🔐 | Hapus surat                |

✅ = Requires token | 🔐 = Requires admin role

---

## 📝 Test Scenarios

### Scenario 1: Login Member

```bash
curl -X POST http://localhost:8000/api/jemaat/login \
  -H "Content-Type: application/json" \
  -d '{"id_jemaat":"15051980","password":"12345"}'
```

✅ Expected: Token returned

---

### Scenario 2: Login dengan Password Salah

```bash
curl -X POST http://localhost:8000/api/jemaat/login \
  -H "Content-Type: application/json" \
  -d '{"id_jemaat":"15051980","password":"salah"}'
```

❌ Expected: Error 401 - "ID Jemaat atau password salah"

---

### Scenario 3: Akses Protected Endpoint tanpa Token

```bash
curl -X GET http://localhost:8000/api/jemaat/biodata
```

❌ Expected: Error 401 - "Unauthorized"

---

### Scenario 4: Admin Delete Member

```bash
# 1. Login sebagai admin
curl -X POST http://localhost:8000/api/jemaat/login \
  -H "Content-Type: application/json" \
  -d '{"id_jemaat":"01011980","password":"12345"}'

# Copy token admin

# 2. Delete member
ADMIN_TOKEN="admin_token_here"
curl -X DELETE http://localhost:8000/api/admin/jemaat/5 \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

✅ Expected: Member berhasil dihapus

---

### Scenario 5: Non-Admin Akses Admin Endpoint

```bash
curl -X DELETE http://localhost:8000/api/admin/jemaat/5 \
  -H "Authorization: Bearer $REGULAR_TOKEN"
```

❌ Expected: Error 403 - "Forbidden - Insufficient permissions"

---

## 🛠️ Debugging Tips

### 1. Enable Query Logging

```php
// Di routes/api.php atau anywhere
use Illuminate\Support\Facades\DB;
DB::listen(function($query) {
    \Log::info($query->sql);
});
```

### 2. Check Token

```bash
php artisan tinker
> $user = auth('sanctum')->user();
> $user->tokens; // List semua tokens
> $user->currentAccessToken(); // Current token
```

### 3. Database Check

```bash
php artisan tinker
> Member::all();
> Member::where('role', 'admin')->first();
> Hash::check('12345', $member->password); // Check password
```

### 4. API Test File (test.http)

Buat file `test.http` di root project:

```
### Login Member
POST http://localhost:8000/api/jemaat/login
Content-Type: application/json

{
  "id_jemaat": "15051980",
  "password": "12345"
}

###
@token = <copy token dari response di atas>

### Get Biodata
GET http://localhost:8000/api/jemaat/biodata
Authorization: Bearer @token

### Get Surat
GET http://localhost:8000/api/jemaat/surat
Authorization: Bearer @token

### Search Surat
GET http://localhost:8000/api/jemaat/surat?keyword=nikah
Authorization: Bearer @token

### Update Biodata
PUT http://localhost:8000/api/jemaat/biodata
Authorization: Bearer @token
Content-Type: application/json

{
  "nama_lengkap": "Updated Name",
  "alamat": "New Address"
}

### Logout
POST http://localhost:8000/api/jemaat/logout
Authorization: Bearer @token
```

Buka di VS Code dengan REST Client extension dan run!

---

## 🌐 Integrasi Nuxt.js

### Install Dependencies

```bash
npm install axios
```

### Setup Plugin (plugins/api.ts)

```typescript
export default defineNuxtPlugin((nuxtApp) => {
    const api = axios.create({
        baseURL: "http://localhost:8000",
    });

    api.interceptors.request.use((config) => {
        const token = useCookie("api_token").value;
        if (token) config.headers.Authorization = `Bearer ${token}`;
        return config;
    });

    return { provide: { api } };
});
```

### Use in Component

```vue
<script setup>
const { $api } = useNuxtApp();
const token = useCookie("api_token");

// Login
const login = async () => {
    const res = await $api.post("/api/jemaat/login", {
        id_jemaat: "15051980",
        password: "12345",
    });
    token.value = res.data.data.token;
};

// Get Biodata
const biodata = await $api.get("/api/jemaat/biodata");
</script>
```

---

## 📚 Documentation Files

| File                        | Purpose                        |
| --------------------------- | ------------------------------ |
| `API_DOCUMENTATION.md`      | 📖 Dokumentasi lengkap         |
| `API_SETUP_SUMMARY.md`      | 📋 Ringkasan setup & checklist |
| `NUXT_INTEGRATION_GUIDE.md` | 🔗 Panduan integrasi Nuxt.js   |
| `QUICKSTART.md`             | ⚡ File ini - Quick reference  |

---

## ✅ Common Issues & Solutions

| Issue                   | Solution                                   |
| ----------------------- | ------------------------------------------ |
| 419 CSRF Token Mismatch | API tidak perlu CSRF (stateless)           |
| 404 Not Found           | Cek endpoint URL & method                  |
| 401 Unauthorized        | Cek token di header Authorization          |
| 403 Forbidden           | Cek role (untuk admin endpoints)           |
| 500 Server Error        | Cek logs: `storage/logs/laravel.log`       |
| CORS Error              | Cek config/cors.php & FRONTEND_URL di .env |

---

## 🎯 Next Steps

1. ✅ Test API dengan cURL/Postman
2. ✅ Setup Nuxt.js frontend
3. ✅ Create login page
4. ✅ Create dashboard page
5. ✅ Test semua endpoints
6. ✅ Deploy ke production

---

## 💡 Tips & Tricks

```bash
# Install REST Client di VS Code
# Gunakan test.http file untuk testing

# atau install Postman
# Import collection untuk batch testing

# Atau gunakan Insomnia
# Alternative ke Postman
```

---

**Siap? Mari mulai testing! 🚀**

Hubungi support jika ada pertanyaan. Good luck! 🙏
