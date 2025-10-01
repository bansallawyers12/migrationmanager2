# Migration Manager - Immigration CRM System

A comprehensive Laravel-based Customer Relationship Management (CRM) system specifically designed for immigration consultancies and migration agencies to manage clients, applications, appointments, invoices, and all aspects of the immigration process.

## Purpose

- Streamline immigration case management from lead to visa approval
- Centralize client information, documents, and communication in one platform
- Automate appointment scheduling and reminders
- Track visa applications, progress, and important deadlines
- Manage invoices, payments, and financial transactions
- Provide secure client portal access for document submission and status tracking
- Offer comprehensive reporting for business insights and compliance

## Features

- **Client Management**: Complete client profiles with personal information, visa history, and documents
- **Application Tracking**: Monitor visa applications with workflow stages and status updates
- **Appointment System**: Schedule consultations with calendar integration and automated reminders
- **Invoice & Payment Management**: Generate invoices, track payments, and manage receipts
- **Document Management**: Secure storage and organization of client documents and checklists
- **Lead Management**: Track potential clients from inquiry to conversion
- **Office Visit Tracking**: Manage walk-in clients and office visit queues
- **Email Integration**: Built-in email management with client correspondence tracking
- **Quotation System**: Create and send professional service quotations
- **Matter Management**: Organize cases by matter type and service category
- **Team & Staff Management**: Role-based access control for team members
- **Reporting & Analytics**: Comprehensive reports on clients, applications, and revenue
- **Client Portal**: Secure portal for clients to view status and submit documents
- **Multi-Currency Support**: Handle international payments and multiple currencies
- **Task Management**: Assign and track tasks related to cases and clients
- **Windows Friendly**: Optimized for XAMPP on Windows environments

## Technology Stack

- **Backend**: Laravel 10.x (PHP 8.1+)
- **Frontend**: Bootstrap 4, jQuery, DataTables, Select2
- **Database**: MySQL (Primary), SQLite (Development)
- **PDF Generation**: DomPDF for invoices and reports
- **Document Processing**: Python scripts for DOCX to PDF conversion
- **Email System**: Laravel Mail with SMTP/IMAP integration
- **Payment Integration**: Stripe, PayU payment gateways
- **File Storage**: Local storage with S3 support for attachments
- **Authentication**: Multi-role authentication (Admin, Staff, Agent, Client)
- **Development Environment**: XAMPP on Windows

## Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js and npm
- Python 3.x (for document conversion)
- MySQL 5.7+ or MariaDB 10.3+
- XAMPP (recommended for Windows)

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/viplucmca/migrationmanager.git
   cd migrationmanager
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   - Create a MySQL database
   - Update `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=migration_manager
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   - Run migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```

6. **Storage setup**
   ```bash
   php artisan storage:link
   ```
   - Create necessary directories:
   ```bash
   mkdir -p storage/app/public/agreements
   mkdir -p storage/app/public/checklists
   mkdir -p storage/app/public/attachments
   ```

7. **Configure mail settings**
   Update `.env` with your mail server details:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your_email@gmail.com
   MAIL_PASSWORD=your_app_password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=your_email@gmail.com
   MAIL_FROM_NAME="Migration Manager"
   ```

8. **Build frontend assets**
   ```bash
   npm run build
   # Or for development:
   npm run dev
   ```

9. **Install Python dependencies** (for document conversion)
   ```bash
   cd python
   pip install -r requirements.txt
   # For LibreOffice converter:
   pip install -r requirements_libreoffice.txt
   ```

10. **Configure payment gateways** (Optional)
    Add to `.env`:
    ```env
    STRIPE_KEY=your_stripe_publishable_key
    STRIPE_SECRET=your_stripe_secret_key
    
    PAYU_MERCHANT_KEY=your_payu_merchant_key
    PAYU_SALT=your_payu_salt
    ```

11. **Start the application**
    - If using XAMPP:
      - Point your virtual host to the `public` directory
      - Or access via `http://localhost/migrationmanager/public`
    
    - Using PHP's built-in server:
      ```bash
      php artisan serve
      ```
    
    - For queue workers (run in separate terminal):
      ```bash
      php artisan queue:work
      ```

## Development

### Running the application

For development with XAMPP:
1. Start Apache and MySQL from XAMPP Control Panel
2. Access the application at `http://localhost/migrationmanager/public`

For development with PHP built-in server:
```bash
php artisan serve
```

Access at: `http://localhost:8000`

### Background Jobs

Start the queue worker for processing background jobs:
```bash
php artisan queue:work
```

### Default Login Credentials

After running migrations with seed, use these credentials:

**Admin:**
- Email: admin@admin.com
- Password: (check `database/seeders/AdminUserSeeder.php`)

### Virtual Host Setup (XAMPP)

Add to `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:
```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/migrationmanager/public"
    ServerName migrationmanager.local
    <Directory "C:/xampp/htdocs/migrationmanager/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Add to `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 migrationmanager.local
```

## Usage Guide

### 1) Manage Leads
- Navigate to `Leads` to view all potential clients
- Create new leads with inquiry details, source, and interested services
- Track lead status: New, Follow-up, Converted, Lost
- Convert leads to clients when ready to proceed
- View lead history and notes

### 2) Client Management
- Go to `Clients` to view all active clients
- Create detailed client profiles with personal information
- Upload client documents and visa history
- Track client relationships (spouse, children, dependents)
- View client summary with all applications, invoices, and documents
- Access client portal credentials

### 3) Application Tracking
- Navigate to `Applications` to manage visa applications
- Create new applications linked to clients
- Select visa type and upload required documents
- Track application workflow stages
- Set important dates (submission, interview, decision)
- Add notes and updates for each application

### 4) Appointment Scheduling
- Go to `Appointments` to manage client consultations
- Use calendar view to see all scheduled appointments
- Create appointments with date, time, and service type
- Send automated appointment reminders to clients
- Track appointment status: Scheduled, Completed, Cancelled
- Handle walk-in appointments

### 5) Invoice Management
- Navigate to `Invoices` to create and manage invoices
- Generate professional invoices for services
- Track payment status: Paid, Unpaid, Partially Paid
- Send invoices via email to clients
- Create payment schedules for installments
- View invoice history and reports

### 6) Document Management
- Go to `Documents` to manage client documents
- Organize documents by categories and checklists
- Upload and download client documents securely
- Track document expiry dates
- Generate document requests for clients
- Sign documents electronically

### 7) Quotations
- Navigate to `Quotations` to create service quotes
- Use templates for standard services
- Customize quotations with line items and pricing
- Send quotations to potential clients
- Track quotation status: Draft, Sent, Accepted, Rejected
- Convert accepted quotations to invoices

### 8) Reports & Analytics
- Access `Reports` for business insights
- View client reports by country, visa type, and status
- Generate revenue reports and forecasts
- Track application success rates
- Monitor staff performance
- Export reports to PDF or Excel

## Business Workflows

- **Lead to Client Process**: Capture lead inquiry → Follow up and qualify → Send quotation → Convert to client → Create client profile
- **Client Onboarding**: Create client account → Collect personal information → Upload documents → Assign case manager → Set up client portal access
- **Application Process**: Receive client documents → Review checklist → Prepare application → Submit to immigration → Track progress → Receive decision
- **Invoice & Payment**: Generate invoice → Send to client → Process payment → Issue receipt → Update payment records
- **Appointment Management**: Client requests appointment → Schedule consultation → Send reminders → Conduct meeting → Update client notes
- **Document Processing**: Client uploads document → Staff reviews → Convert DOCX to PDF → Store securely → Track expiry dates
- **Reporting**: Generate reports → Filter by criteria → Export data → Analyze trends → Make business decisions

## Key Modules

### Admin Module
- Dashboard with key metrics
- Complete client management
- Application tracking
- Invoice and payment management
- Staff and team management
- System settings and configuration

### Client Portal
- View application status
- Upload documents
- Download receipts and invoices
- Book appointments
- Track visa expiry dates
- Communication with case manager

### Agent Module
- Manage assigned clients
- Track applications
- Create invoices
- Schedule appointments
- Commission tracking

## Project Structure

### Key Components

- **Models**: 
  - `User` - Multi-role user authentication (Admin, Staff, Agent, Client)
  - `Client` - Client profiles with personal information and relationships
  - `Application` - Visa application tracking with workflow stages
  - `Invoice` - Invoice generation and payment tracking
  - `Receipt` - Payment receipts and transaction records
  - `Appointment` - Consultation scheduling and management
  - `Lead` - Lead tracking and conversion management
  - `Document` - Document storage and electronic signatures
  - `Matter` - Case/matter management with categories
  - `Quotation` - Service quotation generation
  
- **Controllers**: 
  - `ClientsController` - Client CRUD operations and relationship management
  - `ApplicationsController` - Application tracking and workflow management
  - `InvoiceController` - Invoice generation, payment processing, and schedules
  - `AppointmentsController` - Appointment scheduling and calendar management
  - `DocumentController` - Document upload, download, and signature handling
  - `OfficeVisitController` - Walk-in client management
  - `AdminController` - Admin dashboard and system management
  
- **Services**:
  - `PythonConverterService` - DOCX to PDF document conversion
  - `EmailService` - Email sending and SMTP integration
  - `PaymentService` - Payment gateway integration
  
- **Python Scripts**:
  - `libreoffice_converter.py` - Convert DOCX to PDF using LibreOffice
  - `python_converter.py` - Alternative document conversion utility
  - `test_libreoffice_converter.py` - Test document conversion functionality
  
- **Database Migrations**: 
  - User roles and permissions
  - Client management tables
  - Application tracking
  - Financial transactions
  - Document storage
  - Appointment scheduling
  
- **Policies**: 
  - Role-based access control for Admin, Staff, Agent, and Client
  - Client data privacy and access restrictions

### Storage Structure

The application organizes files in the following structure:

```
storage/app/public/
├── agreements/           # Client service agreements
├── checklists/          # Document checklists
├── attachments/         # Email and document attachments
└── documents/           # Client uploaded documents

public/
├── assets/              # UI assets and images
├── css/                 # Custom stylesheets
├── js/                  # JavaScript files
└── img/                 # Public images
```

### Background Jobs & Scheduling

- Use Laravel's scheduler for automated tasks:
  - Appointment reminders
  - Visa expiry notifications
  - Follow-up reminders
  - Invoice payment reminders
- Queue workers handle:
  - Email sending
  - Document processing
  - PDF generation
  - Report generation

### Document Conversion

The system includes Python-based document conversion:

#### `libreoffice_converter.py`
- **Purpose**: Convert DOCX documents to PDF format
- **Usage**: Automatically called when documents are uploaded
- **Features**: 
  - Uses LibreOffice for high-quality conversion
  - Maintains document formatting
  - Handles multiple file formats
  - Error logging and recovery

#### Integration with Laravel
- Documents uploaded as DOCX are automatically converted to PDF
- Conversion happens in background queue
- Original and converted files are both stored
- Fallback to alternative conversion methods if needed

## Main Routes

### Public Routes
- `GET /` - Welcome/Landing page
- `GET /login` - Login page
- `POST /login` - Authenticate user
- `GET /register` - Registration page (if enabled)

### Admin Routes (Protected)
- `GET /admin/dashboard` - Admin dashboard with key metrics
- **Clients:**
  - `GET /admin/clients` - List all clients
  - `GET /admin/clients/create` - Create new client
  - `GET /admin/clients/{id}` - View client details
  - `GET /admin/clients/{id}/edit` - Edit client
  - `DELETE /admin/clients/{id}` - Delete client
  
- **Applications:**
  - `GET /admin/applications` - List all applications
  - `GET /admin/applications/create` - Create application
  - `GET /admin/applications/{id}` - View application details
  - `PUT /admin/applications/{id}` - Update application status
  
- **Invoices:**
  - `GET /admin/invoices` - List invoices
  - `GET /admin/invoices/create` - Create invoice
  - `GET /admin/invoices/{id}` - View invoice
  - `POST /admin/invoices/{id}/send` - Email invoice
  - `POST /admin/invoices/{id}/payment` - Record payment
  
- **Appointments:**
  - `GET /admin/appointments` - Calendar view
  - `POST /admin/appointments` - Create appointment
  - `PUT /admin/appointments/{id}` - Update appointment
  - `DELETE /admin/appointments/{id}` - Cancel appointment
  
- **Leads:**
  - `GET /admin/leads` - List leads
  - `POST /admin/leads` - Create lead
  - `PUT /admin/leads/{id}/convert` - Convert to client
  
- **Reports:**
  - `GET /admin/reports/clients` - Client reports
  - `GET /admin/reports/applications` - Application reports
  - `GET /admin/reports/revenue` - Financial reports
  - `GET /admin/reports/export` - Export data

### Client Portal Routes (Protected)
- `GET /portal/dashboard` - Client dashboard
- `GET /portal/applications` - View my applications
- `GET /portal/documents` - View and upload documents
- `GET /portal/invoices` - View invoices and payments
- `GET /portal/appointments` - Book appointments
- `POST /portal/documents/upload` - Upload document

### Agent Routes (Protected)
- `GET /agent/dashboard` - Agent dashboard
- `GET /agent/clients` - Assigned clients
- `GET /agent/commissions` - Commission tracking

## Configuration

### Environment Variables

Key environment variables in `.env`:

```env
APP_NAME="Migration Manager"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=migration_manager
DB_USERNAME=root
DB_PASSWORD=

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="Migration Manager"

# Payment Gateways
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
PAYU_MERCHANT_KEY=your_payu_key
PAYU_SALT=your_payu_salt

# File Storage
FILESYSTEM_DISK=local

# Queue Configuration
QUEUE_CONNECTION=database
QUEUE_RETRY_AFTER=90

# Session & Cache
SESSION_DRIVER=file
CACHE_DRIVER=file

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=debug
```

### Database

The application uses MySQL as the primary database. For development, you can use SQLite by changing the `DB_CONNECTION` to `sqlite` in `.env`.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests to ensure everything works
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Important Notes

- The application is optimized to work with XAMPP on Windows
- Python scripts are used for document conversion (DOCX to PDF)
- MySQL database is recommended for production environments
- The application uses Laravel's built-in authentication with multi-role support
- Document storage is handled locally by default, with optional S3 integration
- Comprehensive logging is available in `storage/logs/laravel.log`
- Email integration supports both SMTP and IMAP protocols
- Payment gateways (Stripe, PayU) need to be configured for online payments
- Client portal provides secure access for clients to track their applications

## Troubleshooting

### Database Issues
- **Connection refused**: Ensure MySQL is running in XAMPP Control Panel
- **Access denied**: Verify database credentials in `.env` file
- **Table not found**: Run `php artisan migrate --seed` to create tables

### PDF Generation Issues
- **DOCX conversion fails**: Ensure Python is installed and accessible
- **LibreOffice not found**: Install LibreOffice or use alternative converter
- **Permission denied**: Check storage folder permissions (775 or 777)

### Email Issues
- **Emails not sending**: Verify MAIL_* configuration in `.env`
- **SMTP authentication failed**: Use app-specific passwords for Gmail
- **Emails going to spam**: Configure SPF and DKIM records for your domain

### Performance Issues
- **Slow page loads**: Run `php artisan optimize` and `npm run build`
- **Queue not processing**: Ensure `php artisan queue:work` is running
- **High memory usage**: Increase PHP memory_limit in php.ini

## Security Best Practices

- **Never commit `.env` file** - Contains sensitive credentials
- **Use strong passwords** - Enforce password policies for users
- **Enable HTTPS** - Use SSL certificates in production
- **Regular backups** - Automated daily database backups recommended
- **Update dependencies** - Run `composer update` and `npm update` regularly
- **Role-based access** - Limit admin access to trusted staff only
- **Two-factor authentication** - Consider implementing 2FA for admin accounts
- **Data encryption** - Sensitive client data should be encrypted at rest

## Backup & Data Management

### What to Backup
- **Database**: MySQL database dumps (daily recommended)
- **Storage folder**: `storage/app/public/` containing:
  - Client documents
  - Agreements
  - Attachments
  - Generated PDFs
- **Environment file**: `.env` (store securely, not in repository)
- **Uploaded files**: All content in `public/` except framework files

### Backup Commands
```bash
# Database backup
mysqldump -u root -p migration_manager > backup_$(date +%Y%m%d).sql

# Storage backup
tar -czf storage_backup_$(date +%Y%m%d).tar.gz storage/app/public/
```

### Data Retention
- Keep client records for minimum 7 years (immigration regulations)
- Archive old applications after case closure
- Regularly clean up old logs and temporary files

## FAQ

**Q: Nothing appears after login**
- Ensure migrations have been run: `php artisan migrate --seed`
- Check that Apache and MySQL are running in XAMPP
- Verify `.env` database configuration

**Q: PDF generation not working**
- Install Python 3.x and add to system PATH
- Install LibreOffice for document conversion
- Check `storage/logs/laravel.log` for conversion errors

**Q: Client portal not accessible**
- Ensure client has portal access enabled in their profile
- Client must use the email address registered in the system
- Check that routes are properly configured in `routes/web.php`

**Q: Payment gateway errors**
- Verify Stripe/PayU credentials in `.env`
- Ensure SSL is enabled for production payments
- Test with sandbox/test keys before going live

**Q: Can I customize invoice templates?**
- Yes, edit templates in `resources/views/Admin/invoice/`
- Customize email templates in `resources/views/emails/`

**Q: How to add new visa types?**
- Go to Admin → Settings → Visa Types
- Add new visa type with required documents checklist

**Q: How to export client data?**
- Use Reports section to generate and export data
- Available formats: PDF, Excel, CSV