# Pharma ERP System

A comprehensive Enterprise Resource Planning system built with Laravel 12 for the pharmaceutical industry. This system manages the complete workflow from MR (Medical Representative) registrations to order fulfillment, with role-based access control and extensive reporting capabilities.

## Features

### Multi-Role Authentication System
- **Admin** - Full system control, user management, reports, approvals
- **MR (Medical Representative)** - Doctor/Store registration, visit tracking, order management
- **Doctor** - Order placement, spin & rewards, referral tracking, performance reports
- **Store** - Stock management, sales recording, order tracking
- **End User** - Product catalog browsing, cart & wishlist, order placement

### Core Modules

#### 1. MR Management
- **Doctor Registration** - MRs can register doctors with PIN-code based location auto-fill
- **Store Registration** - MRs can register stores with same PIN-code system
- **Visit Tracking (DCR)** - Daily Call Reports for doctor visits
- **Sample Management** - Track medical samples distributed to doctors
- **Order Management** - Place orders on behalf of doctors/stores

#### 2. Admin Approval Workflows
- **Doctor Approval** - Admin review and approval for MR-registered doctors
- **Store Approval** - Admin review and approval for MR-registered stores
- **User Management** - Soft delete with trash/recycle bin functionality
- **Territory Management** - State → District → City → Area hierarchy

#### 3. Product & Order Management
- **Product Catalog** - Browse products with cart functionality
- **Cart System** - Add/remove items, quantity management
- **Wishlist** - Save products for later, move to cart
- **Order Processing** - Status tracking: Pending → Approved → Delivered
- **Bill Generation** - PDF bill generation with WhatsApp sharing

#### 4. Doctor Engagement
- **Spin & Win** - Gamified reward system for doctors
- **Grand Lucky Draw** - Monthly/Quarterly prize draws
- **Referral System** - Track referrals using unique codes
- **Performance Reports** - Monthly target tracking and achievements

#### 5. Store Operations
- **Stock Management** - Track inventory and record sales
- **Sales Reports** - Daily/weekly/monthly sales analytics
- **Order History** - View past orders and their status

#### 6. Reporting & Analytics
- **Sales Reports** - Export to PDF/Excel
- **Doctor Performance** - Order volume, referral tracking
- **Store Reports** - Sales by store location
- **Activity Logs** - Complete audit trail
- **Leaderboards** - Monthly and all-time rankings

## Tech Stack

- **Framework**: Laravel 12 (PHP 8.2+)
- **Authentication**: Laravel Auth with Spatie Laravel Permission (RBAC)
- **Database**: SQLite (default), MySQL compatible
- **Frontend**: Tailwind CSS, Blade templating
- **Export**: Laravel Excel (Maatwebsite), Laravel DOMPDF
- **Rate Limiting**: Built-in Laravel rate limiters for security

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- SQLite or MySQL

### Quick Setup

```bash
# Clone the repository
git clone <repository-url>
cd pharma

# Run automated setup
composer run setup

# Or manual setup
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

### Development Server

```bash
# Start all development services
composer run dev

# Or start individually
php artisan serve
npm run dev
```

### Default Credentials

After seeding, you can login with:
- **Admin**: admin@pharma.com / password
- **MR**: mr@pharma.com / password
- **Doctor**: doctor@pharma.com / password
- **Store**: store@pharma.com / password

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Admin-specific controllers
│   │   ├── Dashboard/      # Role-based dashboards
│   │   ├── Doctor/         # Doctor portal controllers
│   │   ├── MR/             # MR module controllers
│   │   ├── Store/          # Store portal controllers
│   │   └── Api/            # API controllers (Pincode, etc.)
│   └── Middleware/
├── Models/
│   ├── MR/                 # MR-related models (Doctor, Store, etc.)
│   └── ...                 # Core models (User, Order, Product)
├── Services/               # Business logic services
└── Helpers/                # Helper functions

resources/views/
├── admin/                  # Admin panel views
├── doctor/                 # Doctor portal views
├── mr/                     # MR module views
├── store/                  # Store portal views
├── dashboard/              # Dashboard views
└── layouts/                # Master layouts & partials

routes/
├── web.php                 # Main web routes
└── mr.php                  # MR module routes
```

## Key Features Explained

### PIN Code Location System
The system uses a PIN code lookup API that auto-fills location data:
- State → District → City → Area hierarchy
- Used in Doctor and Store registration
- Reduces manual entry errors
- Consistent territory mapping

### Approval Workflows
1. MR registers a Doctor/Store
2. System creates pending record + inactive user account
3. Admin reviews and approves/rejects
4. On approval: User account activated, unique code assigned
5. On rejection: Reason recorded, MR can edit and resubmit

### Rate Limiting
Security-focused rate limiting on sensitive endpoints:
- Login: 10 attempts per minute
- Orders: 10 per minute
- Prescription access: 10 per minute
- Spin actions: 2 per minute
- Admin actions: 30 per minute

### Soft Delete System
Users are soft-deleted with:
- Trash/recycle bin for recovery
- Permanent delete option
- Activity log preservation
- Referential integrity maintained

## API Endpoints

### Public API
- `GET /api/pincode/{pin}` - PIN code lookup for location auto-fill

### MR Module
- `GET /mr/dashboard` - MR dashboard
- `GET /mr/doctors` - Doctor management
- `GET /mr/stores` - Store management
- `GET /mr/visits` - Visit tracking (DCR)
- `GET /mr/orders` - Order management

### Admin Module
- `GET /admin/dashboard` - Admin dashboard
- `GET /admin/users` - User management
- `GET /admin/products` - Product management
- `GET /admin/orders` - Order management
- `GET /admin/doctors/approval` - Doctor approvals
- `GET /admin/stores/approval` - Store approvals
- `GET /admin/reports/*` - Various reports

## Database Schema Highlights

### Core Tables
- `users` - All user accounts with role assignments
- `mr_doctors` - Doctor registrations by MRs
- `mr_stores` - Store registrations by MRs
- `mr_visits` - Doctor visit records (DCR)
- `orders` & `order_items` - Order management
- `products` - Product catalog
- `rewards` & `spin_histories` - Gamification
- `mr_states`, `mr_districts`, `mr_cities`, `mr_areas` - Territory hierarchy

### Relationships
- MR → Doctors (1:many)
- MR → Stores (1:many)
- Doctor/Store → User (1:1, unified identity)
- Doctor → Orders (1:many)
- Store → Orders (1:many)
- Order → OrderItems (1:many)

## Security Features

- Role-based access control (Spatie Permission)
- Rate limiting on all sensitive endpoints
- CSRF protection on all forms
- SQL injection protection via Eloquent ORM
- XSS protection via Blade escaping
- Secure file uploads (prescriptions)
- Activity logging for audit trails

## License

This project is proprietary software. All rights reserved.

## Support

For technical support or feature requests, please contact the development team.
