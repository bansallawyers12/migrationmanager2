# Python Services - START HERE

> **Welcome!** This is your entry point to the unified Python services documentation.

---

## 🎯 What is This?

The **Unified Python Services** is a single FastAPI microservice that handles:

- ✅ PDF Processing (convert to images, merge files)
- ✅ Email Parsing (.msg files)
- ✅ Email Analysis (categorization, priority, sentiment)
- ✅ Email Rendering (enhanced HTML)

**One service. One port. Simple.**

---

## 🚀 Quick Start (2 Minutes)

```bash
# 1. Navigate to service
cd C:\xampp\htdocs\migrationmanager\python_services

# 2. Start service
py main.py

# 3. Test it
curl http://localhost:5000/health
```

**Done!** Service is running on `http://localhost:5000`

---

## 📚 Documentation

### ⭐ Main Guide
**[PYTHON_SERVICES_MASTER_GUIDE.md](PYTHON_SERVICES_MASTER_GUIDE.md)** - Everything you need in one place

### 📖 Other Guides
- **[Documentation Index](PYTHON_SERVICES_DOCUMENTATION_INDEX.md)** - All documentation organized
- **[Decision Guide](PYTHON_SERVICES_DECISION_GUIDE.md)** - Why unified service?
- **[Integration Guide](PYTHON_SERVICE_INTEGRATION_GUIDE.md)** - Laravel examples

---

## 💻 From Laravel

```php
use App\Services\PythonService;

// Get service instance
$pythonService = app(PythonService::class);

// Process email (parse + analyze + render)
$result = $pythonService->processEmail($request->file('email'));

// Use the results
Email::create([
    'subject' => $result['subject'],
    'category' => $result['analysis']['category'],
    'priority' => $result['analysis']['priority']
]);
```

---

## 📊 Status

| Component | Status |
|-----------|--------|
| Service | ✅ Ready |
| Documentation | ✅ Complete |
| Laravel Integration | ✅ Complete |
| Migration from Old Services | ✅ Complete |
| Tests | ✅ Passing |
| Production Deployment | ✅ Documented |

### Migration Complete ✅

The migration from separate Python services (`python_pdf_service/`, `python/`, `python_outlook_web/`) to the unified `python_services/` has been completed:

- ✅ All PDF methods migrated to `PythonService.php`
- ✅ Controllers updated (`PublicDocumentController`, `DocumentController`)
- ✅ Old services archived in `python_services_archive/`
- ✅ Single service on port 5000

---

## 🎓 Next Steps

1. **Read**: [MASTER GUIDE](PYTHON_SERVICES_MASTER_GUIDE.md)
2. **Install**: Follow installation steps
3. **Test**: Run `py test_service.py`
4. **Integrate**: Use Laravel examples
5. **Deploy**: Follow deployment guide

---

## 📞 Need Help?

1. Check [MASTER GUIDE - Troubleshooting](PYTHON_SERVICES_MASTER_GUIDE.md#troubleshooting)
2. Review logs in `python_services/logs/`
3. Test with `py python_services/test_service.py`

---

**Go to**: [PYTHON_SERVICES_MASTER_GUIDE.md](PYTHON_SERVICES_MASTER_GUIDE.md) 🚀
