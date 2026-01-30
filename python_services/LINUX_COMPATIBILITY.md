# Linux Compatibility - Summary

## Overview

The Python services have been made fully compatible with Linux production servers. The service was already cross-platform at the code level (Python, FastAPI), but the startup scripts and deployment tools were Windows-only.

---

## What Was Added

### 1. Linux Startup Script
**File:** `start_services.sh`
- Bash equivalent of `start_services.bat`
- Checks for Python 3
- Validates dependencies
- Starts the service on port 5000
- Provides clear console output

**Usage:**
```bash
chmod +x start_services.sh
./start_services.sh
```

### 2. Linux Service Installer
**File:** `install_service_linux.sh`
- Automatically installs the Python service as a systemd service
- Configures service to start on boot
- Sets up proper permissions
- Provides service management commands

**Usage:**
```bash
chmod +x install_service_linux.sh
sudo ./install_service_linux.sh
```

### 3. Comprehensive Linux Deployment Guide
**File:** `LINUX_DEPLOYMENT.md`
- Complete production deployment instructions
- Step-by-step installation guide
- Nginx reverse proxy configuration
- SSL/TLS setup with Let's Encrypt
- Security hardening
- Monitoring and logging
- Troubleshooting guide
- Performance tuning
- Backup and recovery

### 4. Docker Support
**Files:** `Dockerfile`, `docker-compose.yml`, `.dockerignore`
- Optional Docker deployment
- Multi-stage build for optimization
- Health checks
- Non-root user for security
- Volume mounting for logs and temp files

### 5. Quick Reference Guide
**File:** `QUICK_REFERENCE.md`
- Quick commands for Windows, Linux, and Docker
- Common tasks and operations
- Troubleshooting commands
- Service management
- Configuration reference

### 6. Updated Documentation
**Files:** `README.md`, `PYTHON_SERVICES_START_HERE.md`
- Added Linux-specific sections
- Separated Windows and Linux instructions
- Cross-referenced new documentation
- Updated deployment sections

---

## Platform Compatibility

### ✅ What Works on Both Windows and Linux

1. **Python Code** - All services are cross-platform
   - FastAPI application
   - PDF processing
   - Email parsing
   - Email analysis
   - Email rendering

2. **Configuration** - `config.py` uses environment variables
   - Works on both platforms
   - Customizable via `.env` file

3. **Dependencies** - All Python packages are cross-platform
   - Listed in `requirements.txt`
   - Install with pip/pip3

4. **API Endpoints** - All endpoints work identically
   - `/health`
   - `/pdf/*`
   - `/email/*`

### 🔧 Platform-Specific Components

#### Windows
- `start_services.bat` - Batch file
- NSSM for Windows Service
- PowerShell commands

#### Linux
- `start_services.sh` - Bash script
- `install_service_linux.sh` - Systemd installer
- systemd service management
- Nginx reverse proxy (recommended)
- journalctl for logs

#### Cross-Platform
- `start_services.py` - Python startup script (works everywhere)
- Docker - Works on any platform with Docker

---

## Deployment Options

### Development

| Platform | Command |
|----------|---------|
| Windows | `start_services.bat` or `python main.py` |
| Linux | `./start_services.sh` or `python3 main.py` |
| Docker | `docker-compose up` |

### Production

| Platform | Method |
|----------|--------|
| Windows | NSSM Windows Service |
| Linux | systemd service (automated with `install_service_linux.sh`) |
| Docker | Docker Compose with restart policy |

---

## File Summary

### New Files Created

```
python_services/
├── start_services.sh              # Linux startup script
├── install_service_linux.sh       # Linux service installer
├── LINUX_DEPLOYMENT.md            # Complete Linux deployment guide
├── Dockerfile                     # Docker image definition
├── docker-compose.yml             # Docker Compose configuration
├── .dockerignore                  # Docker build exclusions
└── QUICK_REFERENCE.md             # Quick command reference
```

### Updated Files

```
python_services/
├── README.md                      # Added Linux sections
└── PYTHON_SERVICES_START_HERE.md  # Added Linux quick start
```

---

## Migration from Windows to Linux

If you're currently running on Windows and want to migrate to Linux:

### Step 1: Transfer Files
```bash
# Using SCP
scp -r python_services/ user@linux-server:/var/www/migrationmanager/

# Or using rsync
rsync -avz python_services/ user@linux-server:/var/www/migrationmanager/python_services/
```

### Step 2: Install on Linux
```bash
# SSH to Linux server
ssh user@linux-server

# Navigate to directory
cd /var/www/migrationmanager/python_services

# Make scripts executable
chmod +x start_services.sh install_service_linux.sh

# Install as service
sudo ./install_service_linux.sh
```

### Step 3: Update Laravel Configuration
```env
# Update .env on Linux server
PYTHON_SERVICE_URL=http://localhost:5000
```

### Step 4: Test
```bash
# Check service status
sudo systemctl status migration-python-services

# Test health endpoint
curl http://localhost:5000/health

# View logs
sudo journalctl -u migration-python-services -f
```

---

## Key Differences Between Windows and Linux Deployments

### Command Syntax
| Task | Windows | Linux |
|------|---------|-------|
| Python | `python` | `python3` |
| Pip | `pip` | `pip3` |
| Path separator | `\` | `/` |
| Env vars | `set VAR=value` | `export VAR=value` |

### Service Management
| Task | Windows | Linux |
|------|---------|-------|
| Start | `nssm start ServiceName` | `sudo systemctl start service-name` |
| Stop | `nssm stop ServiceName` | `sudo systemctl stop service-name` |
| Status | `nssm status ServiceName` | `sudo systemctl status service-name` |
| Logs | Windows Event Viewer | `sudo journalctl -u service-name` |

### File Permissions
| Task | Windows | Linux |
|------|---------|-------|
| Make executable | Not needed | `chmod +x file.sh` |
| Change owner | Right-click → Properties | `chown user:group file` |
| Permissions | Security tab | `chmod 755 file` |

---

## Security Considerations for Linux

### 1. Run as Non-Root User
```bash
# Service runs as dedicated user
sudo useradd -r migration-services
```

### 2. Firewall Configuration
```bash
# Only expose necessary ports
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

### 3. Use Reverse Proxy
- Don't expose Python service directly
- Use Nginx as reverse proxy
- Handle SSL/TLS at Nginx level

### 4. File Permissions
```bash
# Restrict access to service files
sudo chmod 640 config.py
sudo chmod 750 logs/
```

### 5. SELinux (CentOS/RHEL)
```bash
# Configure SELinux contexts
sudo semanage fcontext -a -t httpd_sys_content_t "/var/www/migrationmanager/python_services(/.*)?"
sudo restorecon -Rv /var/www/migrationmanager/python_services
```

---

## Performance Recommendations for Linux Production

### 1. Use Gunicorn
```bash
pip3 install gunicorn
gunicorn main:app -w 4 -k uvicorn.workers.UvicornWorker -b 127.0.0.1:5000
```

### 2. Calculate Workers
```
Workers = (2 × CPU cores) + 1
Example: 4 CPU cores = 9 workers
```

### 3. Configure Nginx
- Enable gzip compression
- Set up caching
- Configure timeouts for large uploads

### 4. Monitor Resources
```bash
# Check memory
free -h

# Check CPU
top

# Check disk
df -h
```

---

## Monitoring on Linux

### System Logs
```bash
# Real-time logs
sudo journalctl -u migration-python-services -f

# Last 100 lines
sudo journalctl -u migration-python-services -n 100

# Today's logs
sudo journalctl -u migration-python-services --since today

# Errors only
sudo journalctl -u migration-python-services -p err
```

### Application Logs
```bash
# Service log
tail -f logs/service.log

# Error log
tail -f logs/error.log

# All logs
tail -f logs/*.log
```

### Health Monitoring
```bash
# Set up cron job for health checks
crontab -e

# Add this line
*/5 * * * * curl -f http://127.0.0.1:5000/health || systemctl restart migration-python-services
```

---

## Troubleshooting on Linux

### Common Issues

#### 1. Service Won't Start
```bash
# Check status
sudo systemctl status migration-python-services

# Check logs
sudo journalctl -u migration-python-services -n 50

# Check Python
python3 --version
```

#### 2. Permission Denied
```bash
# Fix ownership
sudo chown -R www-data:www-data /var/www/migrationmanager/python_services

# Fix permissions
sudo chmod -R 755 /var/www/migrationmanager/python_services
```

#### 3. Port Already in Use
```bash
# Find process
sudo lsof -i :5000

# Kill process
sudo kill -9 <PID>
```

#### 4. Module Not Found
```bash
# Reinstall dependencies
cd /var/www/migrationmanager/python_services
pip3 install --upgrade -r requirements.txt
```

---

## Next Steps

1. **Read**: [LINUX_DEPLOYMENT.md](LINUX_DEPLOYMENT.md) for complete deployment guide
2. **Review**: [QUICK_REFERENCE.md](QUICK_REFERENCE.md) for common commands
3. **Test**: Use test script to verify installation
4. **Monitor**: Set up logging and health checks
5. **Secure**: Follow security hardening guide

---

## Documentation Links

- **[README.md](README.md)** - Main documentation
- **[LINUX_DEPLOYMENT.md](LINUX_DEPLOYMENT.md)** - Linux production guide
- **[QUICK_REFERENCE.md](QUICK_REFERENCE.md)** - Quick commands
- **[PYTHON_SERVICES_START_HERE.md](../PYTHON_SERVICES_START_HERE.md)** - Getting started
- **[PYTHON_SERVICES_MASTER_GUIDE.md](../PYTHON_SERVICES_MASTER_GUIDE.md)** - Complete guide

---

**Status:** ✅ Complete - Python services are now fully compatible with Linux production servers

**Last Updated:** October 2025

