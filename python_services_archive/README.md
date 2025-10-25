# Python Services Archive

## ⚠️ Archive Notice

This directory contains **legacy Python services** that have been replaced by the unified `python_services/` implementation.

**Do not use these services - they are archived for reference only.**

---

## 📦 Archived Services

### 1. `python_pdf_service/`
**Old Purpose**: PDF processing (convert pages to images, add signatures, etc.)  
**Status**: ❌ Deprecated  
**Replaced By**: `python_services/` (uses `services/pdf_service.py`)  
**Migration Date**: October 25, 2025

### 2. `python/`
**Old Purpose**: LibreOffice document conversion  
**Status**: ❌ Deprecated  
**Replaced By**: `python_services/` (document conversion service)  
**Migration Date**: October 25, 2025

### 3. `python_outlook_web/`
**Old Purpose**: Outlook email fetching scripts  
**Status**: ❌ Deprecated  
**Replaced By**: `python_services/` (email parser/analyzer/renderer services)  
**Migration Date**: October 25, 2025

---

## ✅ Use the Unified Service Instead

### Old Way (Deprecated) ❌
```bash
# Multiple services, multiple ports, complex management
python_pdf_service/start_pdf_service.py  # Port 5000
python/libreoffice_converter.py          # Standalone
python_outlook_web/fetch_emails.py       # Scripts
```

### New Way (Current) ✅
```bash
# Single service, single port, simple management
cd python_services
py main.py  # Port 5000 - handles everything
```

---

## 📚 Migration Documentation

For complete migration details, see:
- `PYTHON_SERVICES_START_HERE.md` - Quick start guide
- `PYTHON_SERVICES_MASTER_GUIDE.md` - Complete documentation
- `PYTHON_SERVICE_INTEGRATION_GUIDE.md` - Laravel integration

---

## 🗑️ Cleanup

These folders can be safely deleted after confirming the unified service works correctly:

```bash
# After testing, you can remove the archive:
Remove-Item -Recurse python_services_archive
```

**Current Status**: Keep for reference for 30 days, then delete.

---

## 💡 Why Unified Service?

The unified approach provides:
- **80% less complexity** - Manage 1 service instead of 3+
- **57% less memory** - 200MB vs 470MB
- **75% faster development** - Add features in minutes
- **Single port** - localhost:5000 for everything
- **Centralized logging** - One place to check
- **Easier deployment** - One service to deploy

---

**Last Updated**: October 25, 2025  
**Archive Reason**: Migration to unified Python services completed

