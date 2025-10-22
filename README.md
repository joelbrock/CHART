# CHaRT - Modernized Version

## Overview

This is the modernized version of CHaRT (Client Hours and Reporting Tool), built with Laravel 12 and modern PHP practices. The application has been completely refactored while preserving all core functionality.

## Key Improvements

### 🏗️ **Modern Architecture**
- **Laravel 12** with PHP 8.2+
- **Eloquent ORM** for database interactions
- **Service Layer** pattern for business logic
- **Proper MVC** separation of concerns

### 🗄️ **Database Modernization**
- **Migration-based** schema management
- **Foreign key constraints** for data integrity
- **Proper relationships** between models
- **Type casting** and validation

### 📊 **Enhanced PDF Generation**
- **DomPDF** instead of legacy FPDF
- **Blade templates** for report layouts
- **Modern CSS** styling
- **Better error handling**

### 🎨 **Modern Frontend**
- **Tailwind CSS** for responsive design
- **Clean, intuitive interface**
- **Mobile-friendly** dashboard
- **Real-time filtering** and search

## Features Preserved

✅ **Time Tracking** - All original journal functionality  
✅ **Quarterly Reports** - Enhanced PDF generation  
✅ **Client Management** - Full CRUD operations  
✅ **Staff Assignments** - Many-to-many relationships  
✅ **Event Attendance** - Complete tracking system  
✅ **Usage Analytics** - Hours balance calculations  

## New Features

🚀 **Modern Dashboard** - Clean, responsive interface  
🚀 **API Endpoints** - RESTful API for future integrations  
🚀 **Better Security** - CSRF protection, input validation  
🚀 **Improved Performance** - Optimized queries, caching  
🚀 **Mobile Support** - Responsive design  

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd modern-chart
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Start development server**
   ```bash
   php artisan serve
   ```

## API Endpoints

### Reports
- `GET /reports/quarterly/{client}` - Generate quarterly report
- `GET /reports/batch` - Generate batch reports

### Dashboard
- `GET /` - Main dashboard with filtering

## Database Schema

### Core Tables
- **clients** - Client information and settings
- **staff** - Staff members and authentication
- **journal** - Time tracking entries
- **attendance** - Event attendance records
- **staff_clients** - Many-to-many relationship

### Key Relationships
- Client → Journal (One-to-Many)
- Staff → Journal (One-to-Many)
- Client ↔ Staff (Many-to-Many)
- Client → Attendance (One-to-Many)

## Migration from Legacy

### Data Migration
1. Export data from legacy MySQL database
2. Transform data to match new schema
3. Import using Laravel seeders
4. Verify relationships and constraints

### Feature Mapping
| Legacy Feature | Modern Implementation |
|----------------|----------------------|
| `pdf.php` | `ReportService` + Blade templates |
| `dashboard.php` | `DashboardController` + Tailwind |
| `entry.php` | Journal CRUD with validation |
| Raw SQL queries | Eloquent relationships |

## Development

### Code Structure
```
app/
├── Http/Controllers/     # Request handling
├── Models/               # Eloquent models
├── Services/            # Business logic
└── Providers/           # Service providers

resources/
├── views/               # Blade templates
└── css/                 # Styling

database/
├── migrations/          # Schema definitions
└── seeders/            # Sample data
```

### Key Services
- **ReportService** - PDF generation and report logic
- **HoursCalculationService** - Usage analytics
- **ClientService** - Client management

## Security Improvements

- **CSRF Protection** on all forms
- **Input Validation** with Laravel rules
- **SQL Injection Prevention** via Eloquent
- **XSS Protection** with Blade escaping
- **Authentication** ready for implementation

## Performance Optimizations

- **Eager Loading** to prevent N+1 queries
- **Database Indexing** on foreign keys
- **Query Optimization** with proper relationships
- **Caching** ready for implementation

## Future Enhancements

- [ ] **Authentication System** (Laravel Breeze/Sanctum)
- [ ] **Real-time Updates** (WebSockets)
- [ ] **Mobile App** (API-based)
- [ ] **Advanced Analytics** (Charts/Graphs)
- [ ] **Automated Notifications** (Email/SMS)
- [ ] **Multi-tenant Support** (Organization isolation)

## Contributing

1. Follow PSR-12 coding standards
2. Write tests for new features
3. Update documentation
4. Use meaningful commit messages

## License

Same as original CHaRT application - see LICENSE file for details.