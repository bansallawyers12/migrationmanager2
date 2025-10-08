# Bansal Immigration Client Portal - Flutter Application

## Overview

The Bansal Immigration Client Portal is a comprehensive Flutter application designed for immigration and legal services. This document provides complete technical specifications for integrating with a Laravel backend system.

## Application Information

- **App Name**: Bansal Immigration Client Portal
- **Version**: 1.0.0
- **Company**: Bansal Immigration
- **Description**: Immigration & Legal Services Client Portal
- **Platform**: Flutter (Web, Android, iOS, Windows, macOS, Linux)

## Architecture

### Technology Stack
- **Frontend**: Flutter 3.x
- **State Management**: Provider
- **Local Storage**: Hive
- **HTTP Client**: Dio + Retrofit
- **Authentication**: JWT with refresh tokens
- **File Handling**: image_picker, file_picker
- **Notifications**: Firebase Cloud Messaging
- **Charts**: fl_chart
- **UI Components**: Material Design 3

### Project Structure
```
lib/
├── config/                 # Configuration files
│   ├── api_config.dart     # API endpoints and settings
│   ├── app_config.dart     # App-wide configuration
│   └── theme_config.dart   # UI theme configuration
├── models/                 # Data models
│   ├── client.dart         # Client/User model
│   ├── appointment.dart    # Appointment model
│   ├── case.dart          # Legal case model
│   ├── document.dart      # Document model
│   ├── invoice.dart       # Invoice/Billing model
│   ├── task.dart          # Task model
│   ├── message.dart       # Message model
│   └── ...                # Other models
├── screens/               # UI screens
│   ├── auth/              # Authentication screens
│   ├── dashboard/         # Dashboard screens
│   ├── cases/             # Case management screens
│   ├── appointments/      # Appointment screens
│   ├── documents/         # Document screens
│   ├── tasks/             # Task screens
│   ├── billing/           # Billing screens
│   └── messages/          # Message screens
├── services/              # Business logic
│   ├── api_service.dart   # API communication
│   └── auth_service.dart  # Authentication logic
├── widgets/               # Reusable UI components
└── main.dart             # Application entry point
```

## API Integration

### Base Configuration
- **Base URL**: `http://localhost:8000/api` (configurable)
- **Timeout**: 30 seconds
- **Content-Type**: `application/json`
- **Authentication**: Bearer Token (Laravel Sanctum)

### Authentication Endpoints

#### 1. Login
- **Endpoint**: `POST /api/login`
- **Request Body**:
```json
{
  "email": "string",
  "password": "string",
  "device_name": "flutter-client-portal",
  "device_token": "string"
}
```
- **Response**:
```json
{
  "success": true,
  "data": {
    "token": "jwt_token",
    "refresh_token": "refresh_token",
    "user": {
      "id": 1,
      "name": "Client Name",
      "email": "client@example.com",
      "client_id": "CLI-001"
    }
  }
}
```



#### 3. Logout
- **Endpoint**: `POST /api/logout`
- **Headers**: `Authorization: Bearer {token}`

#### 3.1. Logout All Devices
- **Endpoint**: `POST /api/logout-all`
- **Headers**: `Authorization: Bearer {token}`

#### 4. Refresh Token
- **Endpoint**: `POST /api/refresh`
- **Request Body**:
```json
{
  "refresh_token": "string"
}
```

#### 5. Forgot Password
- **Endpoint**: `POST /api/forgot-password`
- **Request Body**:
```json
{
  "email": "string"
}
```

#### 6. Reset Password
- **Endpoint**: `POST /api/reset-password`
- **Request Body**:
```json
{
  "email": "string",
  "code": "string",
  "password": "string",
  "password_confirmation": "string"
}
```

### Client Portal Endpoints

#### Document Management APIs Summary

The Client Portal includes four new document management endpoints:

| Endpoint | Method | Matter Dependency | Description |
|----------|--------|-------------------|-------------|
| `/api/documents/personal/categories` | GET | Matter Independent | Get personal document categories from `personal_document_types` |
| `/api/documents/personal/checklist` | GET | Matter Independent | Get personal document checklist from `document_checklists` (doc_type=1) |
| `/api/documents/visa/categories` | GET | Optional Matter Filter | Get visa document categories from `visa_document_types` |
| `/api/documents/visa/checklist` | GET | Matter Independent | Get visa document checklist from `document_checklists` (doc_type=2) |

**Key Differences:**
- **Personal Documents**: Apply to all cases, filtered by client_id (global or client-specific)
- **Visa Documents**: Can be global, client-specific, or matter-specific based on client_id and client_matter_id
- **Database Tables**: Uses actual database tables with proper doc_type filtering
- **Flexible Filtering**: Supports global, client-specific, and matter-specific document types

#### 1. Client Profile
- **Get Profile**: `GET /api/profile`
- **Headers**: `Authorization: Bearer {token}`
- **Response**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "client_id": "CLI-001",
    "first_name": "John",
    "last_name": "Doe",
    "email": "client@example.com",
    "phone": "+1234567890",
    "address": "123 Main St",
    "city": "Los Angeles",
    "state": "CA",
    "zip": "90210",
    "country": "USA",
    "profile_img": "path/to/profile/image.jpg",
    "status": 1,
    "role": 7,
    "cp_status": 1,
    "cp_code_verify": 1,
    "email_verified_at": "2024-01-01T00:00:00Z",
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-01-01T00:00:00Z"
  }
}
```

#### 2. Dashboard
- **Get Dashboard**: `GET /api/dashboard`
- **Headers**: `Authorization: Bearer {token}`
- **Query Parameters**:
  - `sel_matter_id` (required): Filter dashboard data by specific matter ID from the matters API (must be a positive integer)
- **Description**: Returns dashboard data for the authenticated client filtered by the specified matter. All dashboard sections (documents, appointments, activities, etc.) are filtered to show only data related to the selected matter.
- **Response**:
```json
{
  "success": true,
  "data": {
    "active_cases": 2,
    "total_documents": 15,
    "total_appointments": 3,
    "case_summary": {
      "active_cases": 2,
      "completed_cases": 1,
      "total_cases": 3
    },
    "recent_cases": [
      {
        "id": 1,
        "title": "Immigration Visa Application (MAT-001)",
        "case_number": "Case #1",
        "status": "In Progress",
        "updated_at": "2024-01-01T00:00:00Z"
      }
    ],
    "document_status": {
      "summary": {
        "approved": 8,
        "pending": 5,
        "rejected": 2
      },
      "overall_progress": 53,
      "recent_documents": [
        {
          "id": 1,
          "name": "Passport Copy",
          "status": "Approved",
          "uploaded_at": "2024-01-01T00:00:00Z",
          "uploaded_days_ago": 5
        }
      ]
    },
    "upcoming_deadlines": {
      "summary": {
        "due_this_week_count": 2,
        "appointments_count": 3,
        "overdue_count": 1
      },
      "due_this_week_list": [
        {
          "id": 1,
          "title": "Submit Documents",
          "due_date": "2024-01-15",
          "due_datetime": "Jan 15, 2024",
          "status": "pending",
          "days_until": 4,
          "priority": "medium",
          "type": "deadline"
        }
      ]
    },
    "recent_activity": [
      {
        "id": 1,
        "type": "Document",
        "title": "Added personal document",
        "description": "Passport copy uploaded for Case #123. Document is now under review...",
        "created_at": "2024-01-01T00:00:00Z",
        "updated_at": "2024-01-01T00:00:00Z",
        "time_ago": "2 hours ago"
      }
    ],
    "selected_matter_id": 1
  }
}
```

#### 3. Dashboard - Recent Cases (View All)
- **Get All Recent Cases**: `GET /api/recent-cases`
- **Headers**: `Authorization: Bearer {token}`
- **Query Parameters**:
  - `sel_matter_id` (required): Filter by specific matter ID from the matters API (must be a positive integer)
  - `page` (optional): Page number for pagination (default: 1)
  - `per_page` (optional): Number of items per page (default: 10)
  - `search` (optional): Search term for case title, case number, or status
  - `status` (optional): Filter by case status
  - `priority` (optional): Filter by case priority (high, medium, low)
- **Description**: Returns detailed case information including progress, agent assignments, priority levels, and action capabilities. Progress percentage is calculated based on workflow_stage_id (1-14 stages): `round((workflow_stage_id / 14) * 100)`. Stage 14 = 100% (file closed). Agent information includes Migration Agent, Person Responsible, and Person Assisting names from the admins table.
- **Response**:
```json
{
  "success": true,
  "data": {
    "cases": [
      {
        "id": 1,
        "title": "102 - Adoption (AP_2)",
        "case_number": "Case #1",
        "matter_number": "AP_2",
        "case_type": "Adoption",
        "case_code": "102",
        "status": "Initial consultation",
        "stage_name": "initial_consultation",
        "priority": "low",
        "priority_display": "LOW",
        "priority_color": "#9E9E9E",
        "description": "No description available",
        "workflow_stage_id": 1,
        "progress_percentage": 7,
        "progress_display": "7%",
        "is_file_closed": false,
        "agents": {
          "migration_agent": {
            "id": 36524,
            "name": "John Smith"
          },
          "person_responsible": {
            "id": 36484,
            "name": "Sarah Johnson"
          },
          "person_assisting": {
            "id": 36532,
            "name": "Mike Wilson"
          }
        },
        "additional_info": "Unknown",
        "workflow_stage": {
          "id": 1,
          "name": "initial_consultation",
          "display_name": "Initial consultation"
        },
        "actions": {
          "can_view_timeline": true,
          "can_view_details": true,
          "can_edit": false,
          "timeline_url": "/api/cases/1/timeline",
          "details_url": "/api/cases/1/details"
        },
        "created_at": "2025-09-01T00:00:00Z",
        "updated_at": "2025-09-01T00:00:00Z",
        "last_updated": "18 days ago",
        "estimated_completion": null,
        "client_unique_matter_no": "AP_2"
      },
      {
        "id": 2,
        "title": "020 - Bridging (Class B) (BA_2)",
        "case_number": "Case #2",
        "matter_number": "BA_2",
        "case_type": "Bridging (Class B)",
        "case_code": "020",
        "status": "Initial consultation",
        "stage_name": "initial_consultation",
        "priority": "low",
        "priority_display": "LOW",
        "priority_color": "#9E9E9E",
        "description": "No description available",
        "workflow_stage_id": 1,
        "progress_percentage": 7,
        "progress_display": "7%",
        "is_file_closed": false,
        "agents": {
          "migration_agent": {
            "id": 36524,
            "name": "John Smith"
          },
          "person_responsible": {
            "id": 36484,
            "name": "Sarah Johnson"
          },
          "person_assisting": {
            "id": 36532,
            "name": "Mike Wilson"
          }
        },
        "additional_info": "Unknown",
        "workflow_stage": {
          "id": 1,
          "name": "initial_consultation",
          "display_name": "Initial consultation"
        },
        "actions": {
          "can_view_timeline": true,
          "can_view_details": true,
          "can_edit": false,
          "timeline_url": "/api/cases/2/timeline",
          "details_url": "/api/cases/2/details"
        },
        "created_at": "2025-08-15T00:00:00Z",
        "updated_at": "2025-08-20T00:00:00Z",
        "last_updated": "30 days ago",
        "estimated_completion": null,
        "client_unique_matter_no": "BA_2"
      }
    ],
    "summary": {
      "total_cases": 2,
      "active_cases": 2,
      "completed_cases": 0,
      "priority_breakdown": {
        "high": 0,
        "medium": 0,
        "low": 2
      },
      "status_breakdown": {
        "initial_consultation": 2,
        "in_progress": 0,
        "completed": 0
      }
    },
    "pagination": {
      "current_page": 1,
      "per_page": 10,
      "total_cases": 2,
      "total_pages": 1,
      "has_next_page": false,
      "has_prev_page": false
    },
    "filters": {
      "search": "",
      "status": "",
      "priority": "",
      "sel_matter_id": 1
    },
    "filter_options": {
      "status_filters": [
        {"key": "all", "label": "All Status", "count": 2},
        {"key": "initial_consultation", "label": "Initial Consultation", "count": 2},
        {"key": "in_progress", "label": "In Progress", "count": 0},
        {"key": "completed", "label": "Completed", "count": 0}
      ],
      "priority_filters": [
        {"key": "all", "label": "All Priority", "count": 2},
        {"key": "high", "label": "High", "count": 0},
        {"key": "medium", "label": "Medium", "count": 0},
        {"key": "low", "label": "Low", "count": 2}
      ]
    }
  }
}
```

#### 4. Dashboard - Documents (View All)
- **Get All Documents**: `GET /api/documents`
- **Headers**: `Authorization: Bearer {token}`
- **Query Parameters**:
  - `sel_matter_id` (required): Filter by specific matter ID (must be a positive integer) - applies only to visa documents, personal documents are always included
  - `page` (optional): Page number for pagination (default: 1)
  - `per_page` (optional): Number of items per page (default: 10)
  - `search` (optional): Search term for document name
  - `status` (optional): Filter by status (approved, pending, rejected)
  - `doc_type` (optional): Filter by document type (visa, personal)
- **Description**: Personal documents are always included regardless of the matter, but visa documents are filtered to show only those related to the selected matter.
- **Response**:
```json
{
  "success": true,
  "data": {
    "documents": [
      {
        "id": 1,
        "name": "vipul_passport",
        "file_name": "vipul_passport.pdf",
        "file_type": "pdf",
        "file_extension": "PDF",
        "doc_type": "personal",
        "status": "Approved",
        "original_status": "signed",
        "file_size": 2048576,
        "file_size_formatted": "2.0 MB",
        "uploaded_at": "2025-09-11T00:00:00Z",
        "updated_at": "2025-09-11T00:00:00Z",
        "uploaded_days_ago": 8,
        "uploaded_date_formatted": "Sep 11, 2025",
        "last_updated": "8 days ago",
        "file_url": "https://bansalimmigration.s3.ap-southeast-1.amazonaws.com/documents/vipul_passport.pdf",
        "file_key": "documents/vipul_passport.pdf",
        "download_url": "https://bansalimmigration.s3.ap-southeast-1.amazonaws.com/documents/vipul_passport.pdf",
        "is_downloadable": true,
        "mime_type": "application/pdf"
      },
      {
        "id": 2,
        "name": "nts_1756380947",
        "file_name": "nts_1756380947.pdf",
        "file_type": "pdf",
        "file_extension": "PDF",
        "doc_type": "visa",
        "status": "Pending",
        "original_status": "draft",
        "file_size": 1536000,
        "file_size_formatted": "1.5 MB",
        "uploaded_at": "2025-08-28T00:00:00Z",
        "updated_at": "2025-08-28T00:00:00Z",
        "uploaded_days_ago": 22,
        "uploaded_date_formatted": "Aug 28, 2025",
        "last_updated": "22 days ago",
        "file_url": "https://bansalimmigration.s3.ap-southeast-1.amazonaws.com/documents/nts_1756380947.pdf",
        "file_key": "documents/nts_1756380947.pdf",
        "download_url": "https://bansalimmigration.s3.ap-southeast-1.amazonaws.com/documents/nts_1756380947.pdf",
        "is_downloadable": true,
        "mime_type": "application/pdf"
      },
      {
        "id": 3,
        "name": "vipul_draft_90909900a71756377853",
        "file_name": "vipul_draft_90909900a71756377853.jpg",
        "file_type": "jpg",
        "file_extension": "JPEG",
        "doc_type": "personal",
        "status": "Approved",
        "original_status": "signed",
        "file_size": 3145728,
        "file_size_formatted": "3.0 MB",
        "uploaded_at": "2025-08-28T00:00:00Z",
        "updated_at": "2025-08-28T00:00:00Z",
        "uploaded_days_ago": 22,
        "uploaded_date_formatted": "Aug 28, 2025",
        "last_updated": "22 days ago",
        "file_url": "https://bansalimmigration.s3.ap-southeast-1.amazonaws.com/documents/vipul_draft_90909900a71756377853.jpg",
        "file_key": "documents/vipul_draft_90909900a71756377853.jpg",
        "download_url": "https://bansalimmigration.s3.ap-southeast-1.amazonaws.com/documents/vipul_draft_90909900a71756377853.jpg",
        "is_downloadable": true,
        "mime_type": "image/jpeg"
      },
      {
        "id": 4,
        "name": "33vipul_form_",
        "file_name": "33vipul_form_.pdf",
        "file_type": "pdf",
        "file_extension": "PDF",
        "doc_type": "visa",
        "status": "Pending",
        "original_status": "sent",
        "file_size": 1024000,
        "file_size_formatted": "1.0 MB",
        "uploaded_at": "2025-08-28T00:00:00Z",
        "updated_at": "2025-08-28T00:00:00Z",
        "uploaded_days_ago": 22,
        "uploaded_date_formatted": "Aug 28, 2025",
        "last_updated": "22 days ago",
        "file_url": "https://bansalimmigration.s3.ap-southeast-1.amazonaws.com/documents/33vipul_form_.pdf",
        "file_key": "documents/33vipul_form_.pdf",
        "download_url": "https://bansalimmigration.s3.ap-southeast-1.amazonaws.com/documents/33vipul_form_.pdf",
        "is_downloadable": true,
        "mime_type": "application/pdf"
      },
      {
        "id": 5,
        "name": "i_acledgement",
        "file_name": "i_acledgement.pdf",
        "file_type": "pdf",
        "file_extension": "PDF",
        "doc_type": "visa",
        "status": "Approved",
        "original_status": "signed",
        "file_size": 512000,
        "file_size_formatted": "500 KB",
        "uploaded_at": "2025-08-19T00:00:00Z",
        "updated_at": "2025-08-19T00:00:00Z",
        "uploaded_days_ago": 31,
        "uploaded_date_formatted": "Aug 19, 2025",
        "last_updated": "31 days ago",
        "file_url": "https://bansalimmigration.s3.ap-southeast-1.amazonaws.com/documents/i_acledgement.pdf",
        "file_key": "documents/i_acledgement.pdf",
        "download_url": "https://bansalimmigration.s3.ap-southeast-1.amazonaws.com/documents/i_acledgement.pdf",
        "is_downloadable": true,
        "mime_type": "application/pdf"
      }
    ],
    "summary": {
      "approved": 3,
      "pending": 2,
      "rejected": 0,
      "total": 5
    },
    "overall_progress": 60,
    "pagination": {
      "current_page": 1,
      "per_page": 10,
      "total_documents": 5,
      "total_pages": 1,
      "has_next_page": false,
      "has_prev_page": false
    },
    "filters": {
      "search": "",
      "status": "",
      "doc_type": "",
      "sel_matter_id": 1
    }
  }
}
```

#### 5. Dashboard - Tasks (View All)
- **Get All Tasks & Deadlines**: `GET /api/upcoming-deadlines`
- **Headers**: `Authorization: Bearer {token}`
- **Query Parameters**:
  - `page` (optional): Page number for pagination (default: 1)
  - `per_page` (optional): Number of items per page (default: 10)
  - `search` (optional): Search term for task title (searches both tasks and appointments)
  - `status` (optional): Filter by status (all, pending, in_progress, completed)
  - `priority` (optional): Filter by priority (all, high, medium, low)
- **Description**: Returns all tasks, deadlines, and appointments with comprehensive filtering options for status and priority.
- **Response**:
```json
{
  "success": true,
  "data": {
    "tasks": [
      {
        "id": 1,
        "title": "Submit additional documents",
        "due_date": "2025-09-23",
        "due_datetime": "Sep 23, 2025",
        "due_formatted": "Due: Sep 23, 2025",
        "status": "pending",
        "status_display": "Pending",
        "priority": "high",
        "priority_display": "High",
        "days_until": 4,
        "type": "task",
        "task_group": "Documentation",
        "description": "Submit required additional documents for visa application",
        "is_overdue": false,
        "is_due_soon": false,
        "status_color": "#FFA500",
        "priority_color": "#FF4444",
        "created_at": "2025-09-01T00:00:00Z",
        "updated_at": "2025-09-01T00:00:00Z",
        "last_updated": "18 days ago"
      },
      {
        "id": 2,
        "title": "Schedule medical examination",
        "due_date": "2025-09-28",
        "due_datetime": "Sep 28, 2025",
        "due_formatted": "Due: Sep 28, 2025",
        "status": "in_progress",
        "status_display": "In Progress",
        "priority": "medium",
        "priority_display": "Medium",
        "days_until": 9,
        "type": "task",
        "task_group": "Medical",
        "description": "Schedule and complete medical examination for visa application",
        "is_overdue": false,
        "is_due_soon": false,
        "status_color": "#2196F3",
        "priority_color": "#FF9800",
        "created_at": "2025-09-05T00:00:00Z",
        "updated_at": "2025-09-10T00:00:00Z",
        "last_updated": "9 days ago"
      },
      {
        "id": 3,
        "title": "Complete application form",
        "due_date": "2025-09-20",
        "due_datetime": "Sep 20, 2025",
        "due_formatted": "Due: Sep 20, 2025",
        "status": "pending",
        "status_display": "Pending",
        "priority": "high",
        "priority_display": "High",
        "days_until": 1,
        "type": "task",
        "task_group": "Application",
        "description": "Complete and submit the main visa application form",
        "is_overdue": false,
        "is_due_soon": true,
        "status_color": "#FFA500",
        "priority_color": "#FF4444",
        "created_at": "2025-09-01T00:00:00Z",
        "updated_at": "2025-09-15T00:00:00Z",
        "last_updated": "4 days ago"
      },
      {
        "id": 4,
        "title": "Review case documents",
        "due_date": "2025-09-17",
        "due_datetime": "Sep 17, 2025",
        "due_formatted": "Due: Sep 17, 2025",
        "status": "completed",
        "status_display": "Completed",
        "priority": "low",
        "priority_display": "Low",
        "days_until": -2,
        "type": "task",
        "task_group": "Review",
        "description": "Review all submitted case documents for accuracy",
        "is_overdue": false,
        "is_due_soon": false,
        "status_color": "#4CAF50",
        "priority_color": "#9E9E9E",
        "created_at": "2025-09-01T00:00:00Z",
        "updated_at": "2025-09-17T00:00:00Z",
        "last_updated": "2 days ago"
      }
    ],
    "summary": {
      "all_count": 4,
      "pending_count": 2,
      "in_progress_count": 1,
      "completed_count": 1,
      "high_priority_count": 2,
      "medium_priority_count": 1,
      "low_priority_count": 1,
      "due_soon_count": 1,
      "overdue_count": 0,
      "total_tasks": 4
    },
    "filter_options": {
      "status_filters": [
        {"key": "all", "label": "All", "count": 4},
        {"key": "pending", "label": "Pending", "count": 2},
        {"key": "in_progress", "label": "In Progress", "count": 1},
        {"key": "completed", "label": "Completed", "count": 1}
      ],
      "priority_filters": [
        {"key": "all", "label": "All Priorities", "count": 4},
        {"key": "high", "label": "High", "count": 2},
        {"key": "medium", "label": "Medium", "count": 1},
        {"key": "low", "label": "Low", "count": 1}
      ]
    },
    "pagination": {
      "current_page": 1,
      "per_page": 10,
      "total_tasks": 4,
      "total_pages": 1,
      "has_next_page": false,
      "has_prev_page": false
    },
    "filters": {
      "search": "",
      "status": "all",
      "priority": "all"
    }
  }
}
```

#### 6. Dashboard - Recent Activity (View All)
- **Get All Recent Activity**: `GET /api/recent-activity`
- **Headers**: `Authorization: Bearer {token}`
- **Query Parameters**:
  - `page` (optional): Page number for pagination (default: 1)
  - `per_page` (optional): Number of items per page (default: 10)
  - `search` (optional): Search term for subject or description
  - `type` (optional): Filter by activity type (Note, Action, Document, Email, Activity)
- **Response**:
```json
{
  "success": true,
  "data": {
    "activities": [
      {
        "id": 1,
        "type": "Document",
        "title": "Added personal document",
        "description": "Passport copy uploaded for Case #123. Document is now under review...",
        "created_at": "2024-01-01T00:00:00Z",
        "updated_at": "2024-01-01T00:00:00Z",
        "time_ago": "2 hours ago",
        "task_group": null
      },
      {
        "id": 2,
        "type": "Note",
        "title": "Added a note to case",
        "description": "Client provided additional information about employment history...",
        "created_at": "2024-01-02T00:00:00Z",
        "updated_at": "2024-01-02T00:00:00Z",
        "time_ago": "1 day ago",
        "task_group": "Case Management"
      }
    ],
    "type_summary": {
      "Note": 5,
      "Action": 3,
      "Document": 8,
      "Email": 2,
      "Activity": 12
    },
    "pagination": {
      "current_page": 1,
      "per_page": 10,
      "total_activities": 30,
      "total_pages": 3,
      "has_next_page": true,
      "has_prev_page": false
    },
    "filters": {
      "search": "",
      "type": ""
    }
  }
}
```

#### 7. Update Profile
- **Update Profile**: `PUT /api/profile`
- **Headers**: `Authorization: Bearer {token}`
- **Request Body**:
```json
{
  "first_name": "string",
  "last_name": "string",
  "phone": "string",
  "address": "string",
  "city": "string",
  "state": "string",
  "post_code": "string",
  "country": "string",
  "dob": "YYYY-MM-DD",
  "gender": "string",
  "marital_status": "string"
}
```

#### 8. Matters (Cases) - Get All Matters
- **Get All Matters**: `GET /api/matters`
- **Headers**: `Authorization: Bearer {token}`
- **Description**: Returns all active matters for the authenticated client. Records with `sel_matter_id=1` are displayed as "General Matter". Matter names are concatenated with client unique matter numbers when available.
- **Response**:
```json
{
  "success": true,
  "data": {
    "matters": [
      {
        "matter_id": 1,
        "matter_name": "General Matter (BA_2)"
      },
      {
        "matter_id": 2,
        "matter_name": "Immigration Visa Application (MAT-001)"
      },
      {
        "matter_id": 3,
        "matter_name": "Work Permit Application (WP-123)"
      },
      {
        "matter_id": 4,
        "matter_name": "Family Reunion Visa (FR-456)"
      }
    ]
  }
}
```

#### 9. Document Management - Personal Document Categories
- **Get Personal Document Categories**: `GET /api/documents/personal/categories`
- **Headers**: `Authorization: Bearer {token}`
- **Description**: Returns all personal document categories from `personal_document_types` table where `client_id` IS NULL (global) or matches the authenticated client. Personal documents are matter independent and apply to all cases.
- **Response**:
```json
{
  "success": true,
  "data": {
    "categories": [
      {
        "id": 1,
        "title": "General",
        "name": "General",
        "status": 1,
        "is_active": true,
        "is_global": true,
        "is_client_specific": false,
        "client_id": null,
        "category_type": "personal",
        "created_at": "2024-11-09T15:56:18Z",
        "updated_at": "2024-11-09T15:56:18Z"
      },
      {
        "id": 2,
        "title": "Education",
        "name": "Education",
        "status": 1,
        "is_active": true,
        "is_global": true,
        "is_client_specific": false,
        "client_id": null,
        "category_type": "personal",
        "created_at": "2024-11-09T15:56:32Z",
        "updated_at": "2024-11-09T15:56:32Z"
      },
      {
        "id": 14,
        "title": "Vipul",
        "name": "Vipul",
        "status": 1,
        "is_active": true,
        "is_global": false,
        "is_client_specific": true,
        "client_id": 36464,
        "category_type": "personal",
        "created_at": "2025-06-27T15:24:04Z",
        "updated_at": "2025-06-27T15:24:39Z"
      }
    ],
    "total_categories": 3,
    "category_type": "personal"
  }
}
```

#### 10. Document Management - Personal Document Checklist
- **Get Personal Document Checklist**: `GET /api/documents/personal/checklist`
- **Headers**: `Authorization: Bearer {token}`
- **Description**: Returns detailed checklist of personal documents from `document_checklists` table where `doc_type=1` (Personal) and `status=1`. Personal documents are matter independent.
- **Response**:
```json
{
  "success": true,
  "data": {
    "checklist": [
      {
        "id": 1,
        "name": "Passport",
        "description": "Valid passport document",
        "doc_type": 1,
        "doc_type_name": "Personal",
        "status": 1,
        "is_active": true,
        "document_type": "personal",
        "created_at": "2024-09-13T09:48:07Z",
        "updated_at": "2024-09-13T09:48:07Z"
      },
      {
        "id": 2,
        "name": "National Identity Card",
        "description": "National identity card document",
        "doc_type": 1,
        "doc_type_name": "Personal",
        "status": 1,
        "is_active": true,
        "document_type": "personal",
        "created_at": "2024-09-13T09:48:07Z",
        "updated_at": "2025-05-24T11:10:35Z"
      },
      {
        "id": 3,
        "name": "School Level Certificates",
        "description": "Educational certificates",
        "doc_type": 1,
        "doc_type_name": "Personal",
        "status": 1,
        "is_active": true,
        "document_type": "personal",
        "created_at": "2024-09-13T09:48:07Z",
        "updated_at": "2025-05-24T11:14:19Z"
      }
    ],
    "total_items": 3,
    "active_items": 3,
    "document_type": "personal",
    "doc_type_id": 1
  }
}
```

#### 11. Document Management - Visa Document Categories
- **Get Visa Document Categories**: `GET /api/documents/visa/categories`
- **Headers**: `Authorization: Bearer {token}`
- **Query Parameters**:
  - `client_matter_id` (optional): Filter by specific client matter ID
- **Description**: Returns visa document categories from `visa_document_types` table where `status=1` and either global (client_id IS NULL AND client_matter_id IS NULL) or client-specific (client_id matches with optional matter filtering).
- **Response**:
```json
{
  "success": true,
  "data": {
    "categories": [
      {
        "id": 1,
        "title": "General",
        "name": "General",
        "status": 1,
        "is_active": true,
        "is_global": true,
        "is_client_specific": false,
        "is_matter_specific": false,
        "client_id": null,
        "client_matter_id": null,
        "category_type": "visa",
        "created_at": "2025-06-27T16:19:53Z",
        "updated_at": "2025-06-27T16:19:53Z"
      },
      {
        "id": 8,
        "title": "AP1",
        "name": "AP1",
        "status": 1,
        "is_active": true,
        "is_global": false,
        "is_client_specific": true,
        "is_matter_specific": true,
        "client_id": 36464,
        "client_matter_id": 37,
        "category_type": "visa",
        "created_at": "2025-06-28T15:51:52Z",
        "updated_at": "2025-06-28T15:51:52Z"
      },
      {
        "id": 11,
        "title": "Visa",
        "name": "Visa",
        "status": 1,
        "is_active": true,
        "is_global": false,
        "is_client_specific": true,
        "is_matter_specific": true,
        "client_id": 36865,
        "client_matter_id": 427,
        "category_type": "visa",
        "created_at": "2025-06-30T18:00:18Z",
        "updated_at": "2025-06-30T18:00:18Z"
      }
    ],
    "total_categories": 3,
    "global_categories": 1,
    "client_specific_categories": 2,
    "matter_specific_categories": 2,
    "category_type": "visa",
    "client_id": 36464,
    "client_matter_id": null
  }
}
```

#### 12. Document Management - Visa Document Checklist
- **Get Visa Document Checklist**: `GET /api/documents/visa/checklist`
- **Headers**: `Authorization: Bearer {token}`
- **Description**: Returns detailed checklist of visa documents from `document_checklists` table where `doc_type=2` (Visa) and `status=1`. No matter dependency required.
- **Response**:
```json
{
  "success": true,
  "data": {
    "checklist": [
      {
        "id": 5,
        "name": "Form 80",
        "description": "Personal particulars for character assessment",
        "doc_type": 2,
        "doc_type_name": "Visa",
        "status": 1,
        "is_active": true,
        "document_type": "visa",
        "created_at": "2024-09-20T22:44:42Z",
        "updated_at": "2024-09-20T22:44:42Z"
      },
      {
        "id": 9,
        "name": "Draft Application",
        "description": "Draft visa application form",
        "doc_type": 2,
        "doc_type_name": "Visa",
        "status": 1,
        "is_active": true,
        "document_type": "visa",
        "created_at": "2024-09-20T22:46:31Z",
        "updated_at": "2024-09-20T22:46:48Z"
      },
      {
        "id": 13,
        "name": "Form 956",
        "description": "Advice by a migration agent/exempt person of providing immigration assistance",
        "doc_type": 2,
        "doc_type_name": "Visa",
        "status": 1,
        "is_active": true,
        "document_type": "visa",
        "created_at": "2024-09-21T14:05:03Z",
        "updated_at": "2024-09-21T14:05:03Z"
      }
    ],
    "total_items": 3,
    "active_items": 3,
    "document_type": "visa",
    "doc_type_id": 2
  }
}
```

#### 13. Document Management - Add Document Checklist
- **Add Document Checklist**: `POST /api/documents/checklist`
- **Headers**: `Authorization: Bearer {token}`
- **Description**: Adds a new document checklist entry to the documents table. Uses `checklist_id` to fetch checklist name from `document_checklists` table based on `doc_type`. Uses `doc_category_id` to validate category exists for the specified `doc_type`. For personal documents, `client_matter_id` must be null. For visa documents, `client_matter_id` is mandatory.
- **Request Body**:
```json
{
  "checklist_id": 1,
  "doc_type": "personal",
  "doc_category_id": 1,
  "client_matter_id": null
}
```
- **Request Body** (Visa Document):
```json
{
  "checklist_id": 5,
  "doc_type": "visa", 
  "doc_category_id": 8,
  "client_matter_id": 37
}
```
- **Response**:
```json
{
  "success": true,
  "message": "Document checklist added successfully",
  "data": {
    "id": 123,
    "checklist": "Passport Copy",
    "doc_type": "personal",
    "doc_category_id": 1,
    "client_matter_id": null,
    "status": "draft",
    "is_client_portal_verify": 2,
    "client_id": 36464,
    "user_id": 1,
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-01-01T00:00:00Z"
  }
}
```

#### 14. Document Management - Upload Document
- **Upload Document**: `POST /api/documents/upload`
- **Headers**: `Authorization: Bearer {token}`
- **Content-Type**: `multipart/form-data`
- **Description**: Uploads a file to an existing document record. Both `document_id` and `file` are MANDATORY parameters. Only ONE file can be uploaded at a time. The document must exist and belong to the authenticated client. File will be uploaded to AWS S3 and the document record will be updated with file details.
- **Request Body** (Form Data):
  - `document_id` (integer, required): ID of the document record to update
  - `file` (file, required): File to upload (max 10MB, single file only)
- **Response**:
```json
{
  "success": true,
  "message": "Document uploaded successfully",
  "data": {
    "id": 123,
    "file_name": "john_passport_1703123456",
    "file_type": "pdf",
    "file_size": 2048576,
    "file_size_formatted": "2.0 MB",
    "file_url": "https://bansalimmigration.s3.ap-southeast-1.amazonaws.com/CLI-001/personal/john_passport_1703123456.pdf",
    "file_key": "john_passport_1703123456.pdf",
    "doc_type": "personal",
    "checklist": "Passport Copy",
    "status": "draft",
    "uploaded_at": "2024-01-01T00:00:00Z"
  }
}
```

**Validation Errors:**
- Missing `document_id`: `{"success": false, "message": "Validation failed", "errors": {"document_id": ["The document id field is required."]}}`
- Missing `file`: `{"success": false, "message": "Validation failed", "errors": {"file": ["The file field is required."]}}`
- File too large: `{"success": false, "message": "Validation failed", "errors": {"file": ["The file may not be greater than 10240 kilobytes."]}}`
- Multiple files: `{"success": false, "message": "Only one file can be uploaded at a time"}`

#### 15. Workflow Management - Get Workflow Stages
- **Get Workflow Stages**: `GET /api/workflow/stages`
- **Headers**: `Authorization: Bearer {token}`
- **Query Parameters**:
  - `client_matter_id` (optional): Get active stage for specific matter
- **Description**: Get all workflow stages with the current active stage for the client. Optional client_matter_id parameter to get active stage for specific matter.
- **Response**:
```json
{
  "success": true,
  "data": {
    "workflow_stages": [
      {
        "id": 1,
        "name": "Initial consultation",
        "stage_name": "Initial consultation",
        "is_active": false,
        "is_current_stage": false,
        "created_at": "2024-01-01T00:00:00Z",
        "updated_at": "2024-01-01T00:00:00Z"
      },
      {
        "id": 12,
        "name": "Immi Request Received",
        "stage_name": "Immi Request Received",
        "is_active": true,
        "is_current_stage": true,
        "created_at": "2024-01-01T00:00:00Z",
        "updated_at": "2024-01-01T00:00:00Z"
      }
    ],
    "total_stages": 12,
    "active_stage": {
      "id": 12,
      "name": "Immi Request Received",
      "stage_name": "Immi Request Received",
      "client_matter_no": "GN_1",
      "matter_status": 1,
      "stage_updated_at": "2024-01-01T00:00:00Z",
      "is_active": true
    },
    "has_active_stage": true,
    "client_id": 36464,
    "client_matter_id": 123
  }
}
```

#### 16. Workflow Management - Get Workflow Stage Details
- **Get Workflow Stage Details**: `GET /api/workflow/stages/{stage_id}`
- **Headers**: `Authorization: Bearer {token}`
- **Description**: Get details of a specific workflow stage including client matters in that stage.
- **Response**:
```json
{
  "success": true,
  "data": {
    "stage": {
      "id": 12,
      "name": "Immi Request Received",
      "stage_name": "Immi Request Received",
      "is_active": true,
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-01T00:00:00Z"
    },
    "client_matters_in_stage": [
      {
        "id": 123,
        "client_unique_matter_no": "GN_1",
        "matter_status": 1,
        "matter_title": "General Migration Case",
        "updated_at": "2024-01-01T00:00:00Z"
      }
    ],
    "total_matters_in_stage": 1,
    "active_matters_in_stage": 1
  }
}
```

#### 17. Workflow Management - Get Allowed Checklist
- **Get Allowed Checklist**: `GET /api/workflow/allowed-checklist`
- **Headers**: `Authorization: Bearer {token}`
- **Query Parameters**:
  - `client_matter_id` (required): Client matter ID to get allowed checklist for
- **Description**: Get allowed checklist documents for a specific client matter. Returns checklist items that clients can upload documents for.
- **Response**:
```json
{
  "success": true,
  "data": {
    "application_info": {
      "application_id": 20736,
      "client_matter_id": 123,
      "client_id": 36464,
      "current_stage": "Immi Request Received",
      "status": 1
    },
    "allowed_checklists": [
      {
        "id": 14,
        "checklist_name": "Passport",
        "document_type": "Passport",
        "description": "Valid passport copy",
        "type": 1,
        "type_name": "Personal Document",
        "is_mandatory": true,
        "due_date": "2024-12-31",
        "due_time": "23:59:59",
        "created_at": "2024-01-01T00:00:00Z",
        "updated_at": "2024-01-01T00:00:00Z"
      },
      {
        "id": 15,
        "checklist_name": "Birth Certificate",
        "document_type": "Birth Certificate",
        "description": "Official birth certificate",
        "type": 1,
        "type_name": "Personal Document",
        "is_mandatory": false,
        "due_date": "2024-12-31",
        "due_time": "23:59:59",
        "created_at": "2024-01-01T00:00:00Z",
        "updated_at": "2024-01-01T00:00:00Z"
      }
    ],
    "total_allowed_checklists": 2,
    "mandatory_checklists": 1,
    "client_matter_id": 123
  }
}
```

#### 18. Workflow Management - Upload Allowed Checklist Document
- **Upload Allowed Checklist Document**: `POST /api/workflow/upload-allowed-checklist`
- **Headers**: `Authorization: Bearer {token}`
- **Content-Type**: `multipart/form-data`
- **Description**: Upload a document for an allowed checklist item. Requires client_matter_id, allowed_checklist_id, and file. The file will be uploaded to AWS S3 in the application_documents/{client_unique_id}/ folder and stored in the application_documents table.
- **Request Body** (Form Data):
  - `client_matter_id` (integer, required): Client matter ID
  - `allowed_checklist_id` (integer, required): Allowed checklist ID from the allowed checklist list
  - `file` (file, required): File to upload (max 10MB)
- **Response**:
```json
{
  "success": true,
  "message": "Allowed checklist document uploaded successfully",
  "data": {
    "document_id": 123,
    "application_id": 20736,
    "client_matter_id": 123,
    "allowed_checklist_id": 14,
    "checklist_name": "Passport",
    "file_name": "John_Passport_1703123456",
    "file_type": "pdf",
    "file_size": 2048576,
    "file_size_formatted": "2.0 MB",
    "file_url": "https://bansalcrm.s3.ap-southeast-2.amazonaws.com/application_documents/CLI-001/John_Passport_1703123456.pdf",
    "file_key": "John_Passport_1703123456.pdf",
    "s3_path": "application_documents/CLI-001/John_Passport_1703123456.pdf",
    "uploaded_at": "2024-01-01T00:00:00Z"
  }
}
```

#### 19. Messaging - Send Message
- **Send Message**: `POST /api/messages/send`
- **Headers**: `Authorization: Bearer {token}`
- **Content-Type**: `application/json`
- **Description**: Send a new text message for a specific client matter. Message will be broadcasted in real-time. Notifications will be sent to the migration agent, person responsible, person assisting for the matter, and all superadmin users (role=1). Notification messages will include 'Client Portal Mobile App' identifier to distinguish from web-based messages. No attachments or recipients - message is associated with the matter only.
- **Request Body**:
```json
{
  "message": "This is a test message sent via mobile app API",
  "client_matter_id": 9
}
```
- **Response**:
```json
{
  "success": true,
  "message": "Message sent successfully",
  "data": {
    "message_id": 123,
    "message": {
      "id": 123,
      "message": "This is a test message sent via mobile app API",
      "sender": "John Doe",
      "sender_id": 1,
      "recipient_id": null,
      "sent_at": "2024-01-01T00:00:00Z",
      "is_read": false
    },
    "sent_at": "2024-01-01T00:00:00Z"
  }
}
```

**Notification Message Format:**
When a message is sent via the mobile app, notifications are created with the following format:
```
"New message received by Client Portal Mobile App from [Sender Name] for matter [Matter Number]"
```

Example notification message:
```
"New message received by Client Portal Mobile App from Vipul Kumar for matter BA_1"
```

#### 20. Messaging - Get All Messages
- **Get All Messages**: `GET /api/messages`
- **Headers**: `Authorization: Bearer {token}`
- **Description**: Get all messages for a specific client matter. Returns both sent and received messages sorted by creation date (oldest first, newest at bottom). client_matter_id is required. Optional pagination with page and limit parameters.
- **Query Parameters**:
  - `client_matter_id` (required): Client matter ID to get messages for
  - `page` (optional): Page number for pagination (default: 1)
  - `limit` (optional): Number of messages per page (default: 20, max: 100)
- **Response**:
```json
{
  "success": true,
  "data": {
    "messages": [
      {
        "id": 1,
        "message": "Message content",
        "sender": "John Doe",
        "recipient": "Jane Smith",
        "sender_id": 1,
        "recipient_id": 2,
        "is_sender": true,
        "is_recipient": false,
        "sent_at": "2024-01-01T00:00:00Z",
        "read_at": null,
        "is_read": false,
        "client_matter_id": 9,
        "created_at": "2024-01-01T00:00:00Z",
        "updated_at": "2024-01-01T00:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 1,
      "last_page": 1
    },
    "filters": {
      "client_matter_id": 9
    }
  }
}
```

#### 21. Messaging - Get Message Details
- **Get Message Details**: `GET /api/messages/{id}`
- **Headers**: `Authorization: Bearer {token}`
- **Description**: Get detailed information about a specific message
- **Response**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "message": "Message content",
    "sender": "John Doe",
    "recipient": "Jane Smith",
    "sender_id": 1,
    "recipient_id": 2,
    "is_sender": true,
    "is_recipient": false,
    "sent_at": "2024-01-01T00:00:00Z",
    "read_at": null,
    "is_read": false,
    "client_matter_id": 9,
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-01-01T00:00:00Z"
  }
}
```

#### 22. Messaging - Mark Message as Read
- **Mark Message as Read**: `PUT /api/messages/{id}/read`
- **Headers**: `Authorization: Bearer {token}`
- **Description**: Mark a message as read. Only the recipient of the message can mark it as read. This will broadcast read status to the sender.
- **Response**:
```json
{
  "success": true,
  "message": "Message marked as read"
}
```
- **Error Responses**:
  - **404 Not Found**: `{"success": false, "message": "Message not found"}`
  - **403 Forbidden**: `{"success": false, "message": "You are not authorized for mark as read"}`

#### 23. Messaging - Get Unread Count
- **Get Unread Count**: `GET /api/messages/unread-count`
- **Headers**: `Authorization: Bearer {token}`
- **Description**: Get the count of unread messages for the authenticated user
- **Response**:
```json
{
  "success": true,
  "data": {
    "unread_count": 5
  }
}
```

## Data Models

### Client Model (Admin)
```dart
class Client {
  final int id;
  final String clientId;
  final String firstName;
  final String lastName;
  final String email;
  final String? phone;
  final String? city;
  final String? address;
  final String? country;
  final String? state;
  final String? zip;
  final String? profileImg;
  final int status;
  final int role; // 7 for client portal users
  final String? serviceToken;
  final DateTime? tokenGeneratedAt;
  final int cpStatus; // Client portal status
  final String? cpRandomCode;
  final int cpCodeVerify;
  final DateTime? emailVerifiedAt;
  final DateTime createdAt;
  final DateTime updatedAt;
  
  // Computed properties
  String get fullName => '$firstName $lastName';
  bool get isClientPortalActive => cpStatus == 1;
  bool get isEmailVerified => emailVerifiedAt != null;
}
```

### Matter Model (Case)
```dart
class Matter {
  final int id;
  final String title;
  final String? nickName;
  final String status;
  final int? packageId;
  final int? clientId;
  final int? agentId;
  final int? assignTo;
  final String? matterNumber;
  final String? matterType;
  final String? description;
  final String? priority;
  final DateTime? estimatedCompletion;
  final List<String>? tags;
  final Map<String, dynamic>? metadata;
  final DateTime createdAt;
  final DateTime updatedAt;
}
```

### Appointment Model
```dart
class Appointment {
  final int id;
  final int? clientId;
  final String? clientUniqueId;
  final String? timezone;
  final String? email;
  final int? noeId;
  final int? serviceId;
  final int? assignee;
  final String? fullName;
  final DateTime? appointmentDate;
  final String? appointmentTime;
  final String? title;
  final String? description;
  final int? invites;
  final String? status;
  final String? relatedTo;
  final String? preferredLanguage;
  final String? inpersonAddress;
  final String? timeslotFull;
  final String? appointmentDetails;
  final String? orderHash;
  final int? duration;
  final String? location;
  final String? notes;
  final String? serviceName;
  final String? staffName;
  final List<String>? attendees;
  final Map<String, dynamic>? metadata;
  final DateTime createdAt;
  final DateTime updatedAt;
}
```

### Document Model
```dart
class Document {
  final int id;
  final String? fileName;
  final String? filetype;
  final String? myfile; // AWS S3 URL
  final String? myfileKey; // S3 key
  final int? clientId;
  final int? userId; // Uploaded by admin
  final int? fileSize;
  final String? type;
  final String? docType;
  final String? status;
  final String? category;
  final String? urgency;
  final String? notes;
  final List<String>? tags;
  final Map<String, dynamic>? metadata;
  final DateTime createdAt;
  final DateTime updatedAt;
}
```

### Invoice Model
```dart
class Invoice {
  final int id;
  final int? clientId;
  final String? invoiceNumber;
  final String? status;
  final DateTime? dueDate;
  final DateTime? issuedDate;
  final DateTime? paidDate;
  final double? subtotal;
  final double? taxAmount;
  final double? totalAmount;
  final double? paidAmount;
  final double? balanceDue;
  final String? currency;
  final String? notes;
  final List<InvoiceItem>? items;
  final Map<String, dynamic>? metadata;
  final DateTime createdAt;
  final DateTime updatedAt;
}

class InvoiceItem {
  final int id;
  final String? description;
  final int? quantity;
  final double? unitPrice;
  final double? totalPrice;
  final String? itemType;
  final Map<String, dynamic>? metadata;
}
```

### Task Model
```dart
class Task {
  final int id;
  final int? clientId;
  final String? title;
  final String? mailId;
  final String? type;
  final int? assignedTo;
  final bool? pin;
  final DateTime? followupDate;
  final bool? followup;
  final String? status;
  final String? description;
  final String? taskGroup;
  final String? priority;
  final DateTime? dueDate;
  final String? assignedByName;
  final List<String>? tags;
  final Map<String, dynamic>? metadata;
  final List<String>? attachments;
  final String? notes;
  final DateTime createdAt;
  final DateTime updatedAt;
}
```

### Message Model
```dart
class Message {
  final int id;
  final String? message;
  final String? sender;
  final String? recipient;
  final int? senderId;
  final int? recipientId;
  final DateTime? sentAt;
  final DateTime? readAt;
  final bool isRead;
  final int? matterId; // Client matter ID
  final DateTime createdAt;
  final DateTime updatedAt;
  
  // Computed properties
  bool get isSender => senderId != null;
  bool get isRecipient => recipientId != null;
  bool get hasBeenRead => isRead && readAt != null;
}
```

## Authentication & Security

### Laravel Sanctum Token Management
- **API Token**: Long-lived (configurable)
- **Token Storage**: Secure storage (flutter_secure_storage)
- **Token Revocation**: On logout or manual revocation
- **Guard**: Uses 'admin' guard for client portal users

### Password Requirements
- **Minimum Length**: 8 characters
- **Required**: Special characters, numbers, uppercase letters
- **Validation**: Client-side and server-side

### Biometric Authentication
- **Supported**: Fingerprint, Face ID, Touch ID
- **Fallback**: PIN/Password
- **Platform Support**: Android, iOS

## File Upload & Management

### Supported File Types
- **Documents**: PDF, DOC, DOCX
- **Images**: JPG, JPEG, PNG, GIF
- **Spreadsheets**: XLS, XLSX
- **Presentations**: PPT, PPTX
- **Archives**: ZIP, RAR

### File Size Limits
- **Maximum Size**: 10MB per file
- **Multiple Files**: Supported
- **Progress Tracking**: Real-time upload progress

### File Categories
- **Immigration Documents**: Passport, visa, certificates
- **Legal Documents**: Contracts, agreements, court papers
- **Financial Documents**: Bank statements, tax returns
- **Personal Documents**: ID, birth certificate, marriage certificate

## Push Notifications

### Firebase Cloud Messaging (FCM)
- **Platform Support**: Android, iOS, Web
- **Notification Types**:
  - Appointment reminders
  - Document approval notifications
  - Case status updates
  - Payment reminders
  - Message notifications

### Notification Categories
- **Urgent**: Case deadlines, payment overdue
- **Important**: Document requests, appointment confirmations
- **Normal**: General updates, status changes
- **Low Priority**: Informational messages

## Laravel Backend Requirements

### Database Tables

#### Admins Table (Client Portal Users)
```sql
CREATE TABLE admins (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id VARCHAR(255) UNIQUE NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    decrypt_password VARCHAR(255),
    phone VARCHAR(255),
    city VARCHAR(255),
    address TEXT,
    country VARCHAR(255),
    state VARCHAR(255),
    zip VARCHAR(255),
    profile_img VARCHAR(255),
    status TINYINT DEFAULT 1,
    role TINYINT NOT NULL DEFAULT 7, -- 7 for client portal users
    service_token VARCHAR(255),
    token_generated_at TIMESTAMP NULL,
    cp_status TINYINT DEFAULT 0, -- Client portal status
    cp_random_code VARCHAR(255),
    cp_code_verify TINYINT DEFAULT 0,
    remember_token VARCHAR(100),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### Matters Table (Cases)
```sql
CREATE TABLE matters (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    nick_name VARCHAR(255),
    status ENUM('pending', 'in_progress', 'completed', 'cancelled', 'under_review', 'on_hold') DEFAULT 'pending',
    package_id BIGINT UNSIGNED,
    client_id BIGINT UNSIGNED,
    agent_id BIGINT UNSIGNED,
    assign_to BIGINT UNSIGNED,
    matter_number VARCHAR(255) UNIQUE,
    matter_type VARCHAR(255),
    description TEXT,
    priority ENUM('high', 'medium', 'low') DEFAULT 'medium',
    estimated_completion DATE,
    tags JSON,
    metadata JSON,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (client_id) REFERENCES admins(id),
    FOREIGN KEY (agent_id) REFERENCES admins(id),
    FOREIGN KEY (assign_to) REFERENCES admins(id)
);
```

#### Appointments Table
```sql
CREATE TABLE appointments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id BIGINT UNSIGNED,
    client_unique_id VARCHAR(255),
    timezone VARCHAR(255),
    email VARCHAR(255),
    noe_id BIGINT UNSIGNED,
    service_id BIGINT UNSIGNED,
    assignee BIGINT UNSIGNED,
    full_name VARCHAR(255),
    appointment_date DATE,
    appointment_time TIME,
    title VARCHAR(255),
    description TEXT,
    invites INT DEFAULT 0,
    status ENUM('pending', 'confirmed', 'cancelled', 'rescheduled', 'completed', 'no_show') DEFAULT 'pending',
    related_to VARCHAR(255),
    preferred_language VARCHAR(255),
    inperson_address TEXT,
    timeslot_full VARCHAR(255),
    appointment_details TEXT,
    order_hash VARCHAR(255),
    duration INT,
    location VARCHAR(255),
    notes TEXT,
    service_name VARCHAR(255),
    staff_name VARCHAR(255),
    attendees JSON,
    metadata JSON,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (client_id) REFERENCES admins(id),
    FOREIGN KEY (assignee) REFERENCES admins(id)
);
```

#### Documents Table
```sql
CREATE TABLE documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(255),
    filetype VARCHAR(255),
    myfile VARCHAR(255), -- AWS S3 URL
    myfile_key VARCHAR(255), -- S3 key
    client_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED, -- Uploaded by admin
    file_size BIGINT,
    type VARCHAR(255),
    doc_type VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected', 'under_review', 'processing') DEFAULT 'pending',
    category VARCHAR(255),
    urgency ENUM('critical', 'high', 'medium', 'low') DEFAULT 'medium',
    notes TEXT,
    tags JSON,
    metadata JSON,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (client_id) REFERENCES admins(id),
    FOREIGN KEY (user_id) REFERENCES admins(id)
);
```

#### Invoices Table
```sql
CREATE TABLE invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id BIGINT UNSIGNED,
    invoice_number VARCHAR(255) UNIQUE,
    status ENUM('draft', 'pending', 'paid', 'overdue', 'cancelled', 'partial') DEFAULT 'draft',
    due_date DATE,
    issued_date DATE,
    paid_date DATE,
    subtotal DECIMAL(10,2),
    tax_amount DECIMAL(10,2),
    total_amount DECIMAL(10,2),
    paid_amount DECIMAL(10,2),
    balance_due DECIMAL(10,2),
    currency VARCHAR(3) DEFAULT 'USD',
    notes TEXT,
    metadata JSON,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (client_id) REFERENCES admins(id)
);

CREATE TABLE invoice_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id BIGINT UNSIGNED,
    description TEXT,
    quantity INT DEFAULT 1,
    unit_price DECIMAL(10,2),
    total_price DECIMAL(10,2),
    item_type VARCHAR(255),
    metadata JSON,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
);
```

#### Tasks Table
```sql
CREATE TABLE tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id BIGINT UNSIGNED,
    title VARCHAR(255),
    mail_id VARCHAR(255),
    type VARCHAR(255),
    assigned_to BIGINT UNSIGNED,
    pin BOOLEAN DEFAULT FALSE,
    followup_date DATE,
    followup BOOLEAN DEFAULT FALSE,
    status ENUM('pending', 'in_progress', 'completed', 'cancelled', 'on_hold', 'deferred') DEFAULT 'pending',
    description TEXT,
    task_group VARCHAR(255),
    priority ENUM('urgent', 'high', 'medium', 'low') DEFAULT 'medium',
    due_date DATE,
    assigned_by_name VARCHAR(255),
    tags JSON,
    metadata JSON,
    attachments JSON,
    notes TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (client_id) REFERENCES admins(id),
    FOREIGN KEY (assigned_to) REFERENCES admins(id)
);
```

#### Messages Table
```sql
CREATE TABLE messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    message TEXT,
    sender VARCHAR(255),
    recipient VARCHAR(255),
    sender_id BIGINT UNSIGNED,
    recipient_id BIGINT UNSIGNED,
    sent_at TIMESTAMP NULL,
    read_at TIMESTAMP NULL,
    is_read BOOLEAN DEFAULT FALSE,
    client_matter_id BIGINT UNSIGNED,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (sender_id) REFERENCES admins(id),
    FOREIGN KEY (recipient_id) REFERENCES admins(id),
    FOREIGN KEY (client_matter_id) REFERENCES client_matters(id)
);
```

### API Controllers Required

1. **ClientPortalController** (Existing)
   - `login()` - Client authentication
   - `logout()` - Client logout
   - `logoutAll()` - Logout from all devices
   - `getProfile()` - Get authenticated client profile
   - `updateProfile()` - Update client profile
   - `refresh()` - Refresh authentication token
   - `forgotPassword()` - Send password reset code
   - `resetPassword()` - Reset password with verification code

2. **ClientPortalDashboardController** (Existing)
   - `dashboard()` - Get dashboard data with cases, documents, appointments, and activities
   - `recentCaseViewAll()` - Get all recent cases with pagination and filtering
   - `documentViewAll()` - Get all documents with pagination and filtering
   - `upcomingDeadlinesViewAll()` - Get all tasks and deadlines with pagination and filtering
   - `recentActivityViewAll()` - Get all recent activities with pagination and filtering
   - `getAllMatters()` - Get all active matters for the authenticated client

3. **ClientPortalDocumentController** (Existing)
   - `getPersonalDocumentCategories()` - Get personal document categories (matter independent)
   - `getPersonalDocumentChecklist()` - Get personal document checklist (matter independent)
   - `getVisaDocumentCategories()` - Get visa document categories (matter dependent)
   - `getVisaDocumentChecklist()` - Get visa document checklist (matter independent)
   - `addDocumentChecklist()` - Add a new document checklist entry
   - `uploadDocument()` - Upload a file to an existing document record

4. **ClientPortalWorkflowController** (Existing)
   - `getWorkflowStages()` - Get all workflow stages with current active stage
   - `getWorkflowStageDetails()` - Get details of a specific workflow stage
   - `allowedChecklistForStages()` - Get allowed checklist documents for a specific client matter
   - `uploadAllowedChecklistDocument()` - Upload a document for an allowed checklist item

5. **ClientPortalMessageController** (Existing)
   - `sendMessage()` - Send a new text message for a specific client matter
   - `getMessages()` - Get all messages for a specific client matter with pagination
   - `getMessageDetails()` - Get detailed information about a specific message
   - `markAsRead()` - Mark a message as read and broadcast read status
   - `getUnreadCount()` - Get the count of unread messages for the authenticated user

### Middleware Required

1. **Authentication Middleware**
   - Laravel Sanctum token validation
   - Admin guard authentication
   - Client portal status verification

2. **Authorization Middleware**
   - Client portal access (role = 7)
   - Resource ownership validation
   - Permission checks

3. **Rate Limiting Middleware**
   - API rate limiting
   - File upload limits
   - Message sending limits

### File Storage

1. **Document Storage**
   - Local storage or cloud storage (AWS S3, Google Cloud)
   - File path management
   - Access control

2. **Image Storage**
   - Profile pictures
   - Document thumbnails
   - Optimized image delivery

### Notification System

1. **Email Notifications**
   - Appointment confirmations
   - Document approval notifications
   - Payment reminders

2. **Push Notifications**
   - Firebase Cloud Messaging setup
   - Device token management
   - Notification scheduling

### Security Considerations

1. **API Security**
   - CORS configuration
   - Rate limiting
   - Input validation
   - SQL injection prevention

2. **File Security**
   - File type validation
   - Virus scanning
   - Access control
   - Secure file serving

3. **Data Protection**
   - GDPR compliance
   - Data encryption
   - Secure storage
   - Privacy controls

## Development Setup

### Prerequisites
- Flutter SDK 3.x
- Dart SDK 3.x
- Android Studio / VS Code
- Git

### Installation
```bash
# Clone the repository
git clone <repository-url>
cd client-portal

# Install dependencies
flutter pub get

# Generate code
flutter packages pub run build_runner build

# Run the application
flutter run
```

### Configuration
1. Update `lib/config/api_config.dart` with your backend URL
2. Configure Firebase for notifications
3. Set up file storage paths
4. Configure authentication settings

## Testing

### Unit Tests
```bash
flutter test
```

### Integration Tests
```bash
flutter test integration_test/
```

### Widget Tests
```bash
flutter test test/widget_test.dart
```

## Deployment

### Web Deployment
```bash
flutter build web
# Deploy the build/web directory to your web server
```

### Android Deployment
```bash
flutter build apk --release
# or
flutter build appbundle --release
```

### iOS Deployment
```bash
flutter build ios --release
```

## Support

For technical support or questions about the Laravel backend integration, please contact:
- **Email**: support@bansalimmigration.com
- **Phone**: +1-800-BANSAL-HELP
- **Website**: https://bansalimmigration.com

## License

This project is proprietary software owned by Bansal Immigration. All rights reserved.

---

**Note**: This documentation is comprehensive and should be used as a reference for implementing the Laravel backend. All API endpoints, data models, and database schemas are designed to work seamlessly with the Flutter client portal application.
```