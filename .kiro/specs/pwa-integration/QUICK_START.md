# Quick Start Guide - PWA Integration

## 🚀 Start the Application

```bash
# 1. Start all containers
docker-compose up -d

# 2. Start Vite dev server (for development)
docker-compose --profile dev up -d simulados-vite

# 3. Open in browser
open http://localhost:8090
```

## 🧪 Run Tests

```bash
# Run automated end-to-end tests
./test-e2e.sh
```

## 🔑 Test Credentials

| User Type | Email | Password | Access |
|-----------|-------|----------|--------|
| Admin | `admin@simulados.com` | `admin123` | Full access |
| Premium | `premium@test.com` | `senha123` | All exams |
| Free | `free@test.com` | `senha123` | Limited |
| Trial | `trial@test.com` | `senha123` | Trial period |

## 📊 Check Status

```bash
# Check containers
docker-compose ps

# Check Vite
docker logs simulados-vite

# Check Laravel logs
docker exec simulados-app tail -f storage/logs/laravel.log

# Test API
curl -X POST http://localhost:8090/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@simulados.com","password":"admin123"}'
```

## 🔧 Troubleshooting

### Application not loading?
```bash
# Restart containers
docker-compose restart

# Check if Vite is running
docker-compose --profile dev up -d simulados-vite
```

### Database issues?
```bash
# Re-run migrations and seeders
docker exec simulados-app php artisan migrate:fresh --seed
```

### Clear cache?
```bash
# Clear Laravel cache
docker exec simulados-app php artisan cache:clear
docker exec simulados-app php artisan config:clear
docker exec simulados-app php artisan route:clear
```

## 📚 Documentation

- **Full Test Report:** `END_TO_END_TEST_REPORT.md`
- **Manual Testing:** `MANUAL_TESTING_GUIDE.md`
- **Task Summary:** `TASK_23_SUMMARY.md`
- **Main README:** `../laravel/README.md`

## ✅ Current Status

- **Task 23:** ✅ COMPLETE (26/26 tests passed)
- **Application:** ✅ Running and functional
- **APIs:** ✅ All working correctly
- **PWA:** ✅ Infrastructure complete

## ⏭️ Next Steps

1. **Task 24.2:** Run Lighthouse audit
2. **Task 24.5:** Test PWA on real devices
3. **Manual Testing:** Follow `MANUAL_TESTING_GUIDE.md`

---

**Need help?** Check the troubleshooting section above or review the detailed documentation files.
