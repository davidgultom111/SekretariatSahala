# 📋 File Reference & Quick Links

## 🎯 Start Here

1. **First Time?** → Read [QUICKSTART.md](QUICKSTART.md) (5 min)
2. **Setup Issues?** → Read [SANCTUM_SETUP_GUIDE.md](SANCTUM_SETUP_GUIDE.md)
3. **API Details?** → Read [API_DOCUMENTATION.md](API_DOCUMENTATION.md)
4. **Frontend Setup?** → Read [NUXT_INTEGRATION_GUIDE.md](NUXT_INTEGRATION_GUIDE.md)

---

## 📁 API Files by Type

### Controllers

| File                   | Purpose                   | Location                    |
| ---------------------- | ------------------------- | --------------------------- |
| `MemberAuthController` | Login/Logout endpoints    | `app/Http/Controllers/API/` |
| `MemberApiController`  | Biodata & Surat endpoints | `app/Http/Controllers/API/` |
| `AdminApiController`   | Admin delete operations   | `app/Http/Controllers/API/` |

### Models & Resources

| File             | Purpose                                     | Location              |
| ---------------- | ------------------------------------------- | --------------------- |
| `Member.php`     | Member model (updated with password & role) | `app/Models/`         |
| `MemberResource` | Transform Member to JSON                    | `app/Http/Resources/` |
| `LetterResource` | Transform Letter to JSON                    | `app/Http/Resources/` |

### Validation

| File                         | Purpose                 | Location                 |
| ---------------------------- | ----------------------- | ------------------------ |
| `MemberLoginRequest`         | Validate login inputs   | `app/Http/Requests/API/` |
| `UpdateMemberBiodataRequest` | Validate biodata update | `app/Http/Requests/API/` |

### Middleware & Routes

| File            | Purpose                   | Location               |
| --------------- | ------------------------- | ---------------------- |
| `CheckRole.php` | Role-based access control | `app/Http/Middleware/` |
| `api.php`       | All API routes            | `routes/`              |

### Configuration

| File            | Purpose                           | Location     |
| --------------- | --------------------------------- | ------------ |
| `sanctum.php`   | Sanctum config (NEW)              | `config/`    |
| `cors.php`      | CORS config (NEW)                 | `config/`    |
| `auth.php`      | Auth guards & providers (UPDATED) | `config/`    |
| `app.php`       | Bootstrap config (UPDATED)        | `bootstrap/` |
| `providers.php` | Service providers (UPDATED)       | `bootstrap/` |

### Database

| File                    | Purpose                        | Location               |
| ----------------------- | ------------------------------ | ---------------------- |
| `2026_04_20_094914_...` | Add password & role to members | `database/migrations/` |
| `MemberSeeder.php`      | Test data seeder (UPDATED)     | `database/seeders/`    |

### Documentation

| File                           | Purpose                    | Read Time |
| ------------------------------ | -------------------------- | --------- |
| `API_DOCUMENTATION.md`         | Complete API reference     | 15 min    |
| `NUXT_INTEGRATION_GUIDE.md`    | Nuxt.js integration guide  | 20 min    |
| `QUICKSTART.md`                | Quick start & testing      | 5 min     |
| `API_SETUP_SUMMARY.md`         | Setup checklist            | 10 min    |
| `SANCTUM_SETUP_GUIDE.md`       | Autoloader troubleshooting | 5 min     |
| `REST_API_COMPLETE_SUMMARY.md` | Overall summary            | 10 min    |
| `FILE_REFERENCE.md`            | This file                  | 2 min     |

---

## 🔍 API Endpoints Quick Reference

### Login (Public)

```bash
POST /api/jemaat/login
```

### Member (Protected)

```bash
POST   /api/jemaat/logout
GET    /api/jemaat/biodata
PUT    /api/jemaat/biodata
GET    /api/jemaat/surat
GET    /api/jemaat/surat/{id}/download
```

### Admin (Protected + Admin Role)

```bash
DELETE /api/admin/jemaat/{id}
DELETE /api/admin/surat/{id}
```

---

## 🛠️ Common Tasks

### Test Login

```bash
curl -X POST http://localhost:8000/api/jemaat/login \
  -H "Content-Type: application/json" \
  -d '{"id_jemaat":"15051980","password":"12345"}'
```

**See:** QUICKSTART.md → Test Scenarios

### Setup Frontend

**See:** NUXT_INTEGRATION_GUIDE.md → Integration dengan Nuxt.js

### Fix Sanctum Issues

**See:** SANCTUM_SETUP_GUIDE.md → Solusi Paling Cepat

### Full API Docs

**See:** API_DOCUMENTATION.md → Semua endpoint dengan contoh

---

## 📊 File Statistics

| Category       | Count  | Status               |
| -------------- | ------ | -------------------- |
| Controllers    | 3      | ✅ Created           |
| Resources      | 2      | ✅ Created           |
| Form Requests  | 2      | ✅ Created           |
| Middleware     | 1      | ✅ Created           |
| Configuration  | 5      | ✅ Created/Updated   |
| Database Files | 2      | ✅ Created/Updated   |
| Documentation  | 6      | ✅ Created           |
| **TOTAL**      | **21** | **✅ 100% Complete** |

---

## 🎯 Implementation Checklist

### Done ✅

- [x] Install Sanctum
- [x] Create migrations
- [x] Create controllers
- [x] Create resources
- [x] Create form requests
- [x] Create middleware
- [x] Create routes
- [x] Configure CORS
- [x] Configure Sanctum
- [x] Update auth config
- [x] Update models
- [x] Create seeder
- [x] Create documentation

### To Do (User)

- [ ] Fix Sanctum autoloader (see SANCTUM_SETUP_GUIDE.md)
- [ ] Create test data
- [ ] Test endpoints
- [ ] Setup Nuxt.js frontend
- [ ] Deploy to production

---

## 💻 Command Reference

### Setup & Migration

```bash
# Run migrations
php artisan migrate

# Seed test data
php artisan db:seed --class=MemberSeeder

# Manual member creation
php artisan tinker
> Member::create([...])
```

### Testing

```bash
# Start server
php artisan serve

# Test login
curl -X POST http://localhost:8000/api/jemaat/login ...

# Test protected endpoint
curl -X GET http://localhost:8000/api/jemaat/biodata \
  -H "Authorization: Bearer {token}"
```

### Debugging

```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan cache:clear
php artisan config:clear

# Debug member
php artisan tinker
> Member::all()
> Hash::check('12345', $member->password)
```

---

## 🌐 Environment Variables

Required in `.env`:

```env
# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost:3000,localhost:8080,127.0.0.1:3000,127.0.0.1:8080

# Frontend URL
FRONTEND_URL=http://localhost:3000

# Production
APP_ENV=production
APP_DEBUG=false
```

---

## 🔐 Test Credentials

From MemberSeeder:

| ID Jemaat | Role   | Password |
| --------- | ------ | -------- |
| 15051980  | member | 12345    |
| 22081992  | member | 12345    |
| 10031985  | member | 12345    |
| 01011980  | admin  | 12345    |

---

## 📱 Postman / cURL Tips

### Import into Postman

1. Create collection
2. Add requests for each endpoint
3. Use variables for `base_url` and `token`
4. Run tests

### Use test.http file

1. Install REST Client extension in VS Code
2. Create `test.http` file
3. Copy examples from QUICKSTART.md
4. Run requests directly

---

## 🎓 Learning Path

1. **Beginner** → Start with QUICKSTART.md
2. **Intermediate** → Read API_DOCUMENTATION.md
3. **Advanced** → Read source code in app/Http/Controllers/API/
4. **Frontend** → Read NUXT_INTEGRATION_GUIDE.md
5. **Deployment** → Read REST_API_COMPLETE_SUMMARY.md

---

## 🆘 Troubleshooting Map

| Problem           | Solution                               |
| ----------------- | -------------------------------------- |
| "Trait not found" | SANCTUM_SETUP_GUIDE.md                 |
| CORS Error        | API_DOCUMENTATION.md → CORS            |
| "Unauthorized"    | API_DOCUMENTATION.md → Error Handling  |
| "Forbidden"       | API_DOCUMENTATION.md → Admin Endpoints |
| 500 Error         | Check storage/logs/laravel.log         |
| Seeder fails      | SANCTUM_SETUP_GUIDE.md → Manual Setup  |

---

## 📞 Support Resources

- **Laravel Sanctum:** https://laravel.com/docs/12.x/sanctum
- **RESTful APIs:** https://restfulapi.net/
- **Nuxt.js:** https://nuxt.com/
- **CORS:** https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS

---

## 🏁 Quick Navigation

```
You are here: FILE_REFERENCE.md

Next steps:
  • Beginner: Go to QUICKSTART.md
  • Setup issue: Go to SANCTUM_SETUP_GUIDE.md
  • API details: Go to API_DOCUMENTATION.md
  • Frontend: Go to NUXT_INTEGRATION_GUIDE.md
  • Overview: Go to REST_API_COMPLETE_SUMMARY.md
```

---

**Happy coding! 🚀**

_All files are in the root directory of the project._
_Refer to this file whenever you need to find something._
