# Pharma Company Web Application - Development Plan

## Technology Stack
- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade + Tailwind CSS v4
- **Authentication**: Laravel Breeze
- **Role/Permission**: spatie/laravel-permission
- **Database**: SQLite (development) / MySQL (production)
- **Build Tool**: Vite

---

## Phase 1: Project Setup & Authentication

### 1.1 Install Laravel Breeze
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install
npm run build
```

### 1.2 Install Spatie Permission Package
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### 1.3 Database Configuration
- Update `.env` for database settings
- Configure mail settings for password resets

### 1.4 Create Roles & Permissions Seeder
**File**: `database/seeders/RolesAndPermissionsSeeder.php`

**Roles**:
- Admin (full access)
- Sub Admin (limited admin access)
- Doctor (place orders, view targets, spin)
- Store (place orders, view sales)
- End User (place orders)

**Permissions**:
- `manage_users`, `manage_products`, `manage_orders`, `view_reports`
- `place_order`, `view_own_orders`
- `spin_wheel`, `view_targets`

### 1.5 Create Role Middleware
**File**: `app/Http/Middleware/RoleMiddleware.php`

Register in `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

### 1.6 Update User Model
**File**: `app/Models/User.php`
- Add `HasRoles` trait from Spatie
- Add fields: `code`, `status`, `phone`, `address`

### 1.7 Create Dashboard Controllers
**Files**:
- `app/Http/Controllers/Dashboard/AdminDashboardController.php`
- `app/Http/Controllers/Dashboard/DoctorDashboardController.php`
- `app/Http/Controllers/Dashboard/StoreDashboardController.php`
- `app/Http/Controllers/Dashboard/UserDashboardController.php`

### 1.8 Dashboard Views
**Files**:
- `resources/views/dashboard/admin.blade.php`
- `resources/views/dashboard/doctor.blade.php`
- `resources/views/dashboard/store.blade.php`
- `resources/views/dashboard/user.blade.php`

### 1.9 Update Routes
**File**: `routes/web.php`
```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::middleware(['role:Admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    });
    
    Route::middleware(['role:Doctor'])->prefix('doctor')->group(function () {
        Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('doctor.dashboard');
    });
    
    // Similar for Store and End User
});
```

### 1.10 Create Master Layout
**File**: `resources/views/layouts/master.blade.php`
- Extend Breeze layout
- Add role-based sidebar navigation
- Include responsive design with Tailwind

**Deliverables**:
- Login/Register working
- Role-based redirection after login
- Basic dashboard UI for each role

---

## Phase 2: User & Role Management (Admin Module)

### 2.1 Create User Migration Update
**File**: `database/migrations/xxxx_add_fields_to_users_table.php`
```php
Schema::table('users', function (Blueprint $table) {
    $table->string('code')->unique()->nullable();
    $table->enum('status', ['active', 'inactive'])->default('active');
    $table->string('phone')->nullable();
    $table->text('address')->nullable();
    $table->foreignId('created_by')->nullable()->constrained('users');
});
```

### 2.2 Create User Controller
**File**: `app/Http/Controllers/Admin/UserController.php`

**Methods**:
- `index()` - List users with pagination and search
- `create()` - Show create form
- `store()` - Store new user with unique code generation
- `edit()` - Show edit form
- `update()` - Update user
- `destroy()` - Delete user
- `toggleStatus()` - Activate/deactivate user

### 2.3 Unique Code Generation Logic
```php
private function generateUniqueCode($role)
{
    $prefix = match($role) {
        'Doctor' => 'DOC',
        'Store' => 'STR',
        'Sub Admin' => 'SAD',
        default => 'USR'
    };
    
    do {
        $code = $prefix . '-' . strtoupper(Str::random(6));
    } while (User::where('code', $code)->exists());
    
    return $code;
}
```

### 2.4 Create User Requests (Validation)
**Files**:
- `app/Http/Requests/Admin/StoreUserRequest.php`
- `app/Http/Requests/Admin/UpdateUserRequest.php`

**Validation Rules**:
- Name: required, string, max:255
- Email: required, email, unique:users
- Role: required, exists:roles,name
- Phone: nullable, string, max:20
- Status: required, in:active,inactive

### 2.5 Create User Views
**Files**:
- `resources/views/admin/users/index.blade.php` - List with search, pagination, status toggle
- `resources/views/admin/users/create.blade.php` - Add user form
- `resources/views/admin/users/edit.blade.php` - Edit user form

**UI Components**:
- Data table with sorting
- Search by name/email/code
- Filter by role/status
- Pagination (10 per page)
- Status badge (Active/Inactive)
- Action buttons (Edit, Delete, Toggle Status)

### 2.6 Update Admin Sidebar
**File**: `resources/views/layouts/partials/admin-sidebar.blade.php`
Add User Management menu with submenu for:
- All Users
- Doctors
- Stores
- Sub Admins

### 2.7 Routes
```php
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
});
```

**Deliverables**:
- Admin can create/edit/delete users
- Unique code auto-generated for Doctors, Stores, Sub Admins
- User status toggle (Active/Inactive)
- Role-based permission assignment
- Search and pagination working

---

## Phase 3: Product Management Module

### 3.1 Create Product Migration
**File**: `database/migrations/xxxx_create_products_table.php`
```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('category');
    $table->decimal('price', 10, 2);
    $table->decimal('commission', 10, 2)->default(0);
    $table->integer('stock')->default(0);
    $table->enum('status', ['active', 'inactive'])->default('active');
    $table->text('description')->nullable();
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();
});
```

### 3.2 Create Product Model
**File**: `app/Models/Product.php`
```php
protected $fillable = ['name', 'category', 'price', 'commission', 'stock', 'status', 'description', 'created_by'];

protected $casts = [
    'price' => 'decimal:2',
    'commission' => 'decimal:2',
];
```

### 3.3 Create Product Controller
**File**: `app/Http/Controllers/Admin/ProductController.php`

**Methods**:
- `index()` - List products with search/filter
- `create()`, `store()` - Create product
- `edit()`, `update()` - Edit product
- `destroy()` - Delete product
- `updateStock()` - Quick stock update

### 3.4 Create Product Requests
**Files**:
- `app/Http/Requests/Admin/StoreProductRequest.php`
- `app/Http/Requests/Admin/UpdateProductRequest.php`

**Validation Rules**:
- Name: required, string, max:255
- Category: required, string, max:100
- Price: required, numeric, min:0
- Commission: required, numeric, min:0
- Stock: required, integer, min:0
- Status: required, in:active,inactive

### 3.5 Create Product Views
**Files**:
- `resources/views/admin/products/index.blade.php`
- `resources/views/admin/products/create.blade.php`
- `resources/views/admin/products/edit.blade.php`

**UI Features**:
- Product cards or table view
- Search by name/category
- Filter by status, category
- Stock indicator (low stock warning)
- Price and commission display
- Pagination

### 3.6 Routes
```php
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/update-stock', [ProductController::class, 'updateStock'])->name('products.update-stock');
});
```

**Deliverables**:
- Complete Product CRUD
- Stock management system
- Search and filter functionality
- Responsive UI

---

## Phase 4: Order Management System

### 4.1 Create Orders Migration
**File**: `database/migrations/xxxx_create_orders_table.php`
```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('order_number')->unique();
    $table->foreignId('user_id')->constrained();
    $table->foreignId('doctor_id')->nullable()->constrained('users');
    $table->foreignId('store_id')->nullable()->constrained('users');
    $table->string('referral_code')->nullable();
    $table->enum('status', ['pending', 'approved', 'rejected', 'delivered'])->default('pending');
    $table->enum('sale_type', ['doctor_direct', 'referral', 'store_linked', 'company_direct'])->nullable();
    $table->decimal('total_amount', 12, 2);
    $table->text('notes')->nullable();
    $table->timestamp('approved_at')->nullable();
    $table->foreignId('approved_by')->nullable()->constrained('users');
    $table->timestamp('delivered_at')->nullable();
    $table->timestamps();
});
```

### 4.2 Create Order Items Migration
**File**: `database/migrations/xxxx_create_order_items_table.php`
```php
Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->constrained();
    $table->integer('quantity');
    $table->decimal('price', 10, 2);
    $table->decimal('commission', 10, 2)->default(0);
    $table->decimal('subtotal', 10, 2);
    $table->timestamps();
});
```

### 4.3 Create Models
**Files**:
- `app/Models/Order.php` - With relationships to User, OrderItem
- `app/Models/OrderItem.php` - With relationships to Order, Product

### 4.4 Create Order Controller
**File**: `app/Http/Controllers/OrderController.php`

**Methods**:
- `index()` - List orders (role-based filtering)
- `create()` - Show order form with product selection
- `store()` - Create order with validation
- `show()` - Order details
- `approve()` - Admin approve order
- `reject()` - Admin reject order
- `deliver()` - Mark as delivered

### 4.5 Create Admin Order Controller
**File**: `app/Http/Controllers/Admin/OrderController.php`
- View all orders
- Manage order status
- Filter by status, date, user type

### 4.6 Order Number Generation
```php
private function generateOrderNumber()
{
    $prefix = 'ORD';
    $date = now()->format('Ymd');
    $lastOrder = Order::whereDate('created_at', today())->count() + 1;
    return $prefix . '-' . $date . '-' . str_pad($lastOrder, 4, '0', STR_PAD_LEFT);
}
```

### 4.7 Referral Code Validation
```php
private function validateReferralCode($code)
{
    if (!$code) return null;
    
    $doctor = User::where('code', $code)
        ->whereHas('roles', fn($q) => $q->where('name', 'Doctor'))
        ->where('status', 'active')
        ->first();
    
    return $doctor?->id;
}
```

### 4.8 Create Order Requests
**Files**:
- `app/Http/Requests/StoreOrderRequest.php`
- Validation for items array, quantities, referral code

### 4.9 Create Order Views
**Files**:
- `resources/views/orders/index.blade.php` - Order history
- `resources/views/orders/create.blade.php` - Create order form
- `resources/views/orders/show.blade.php` - Order details
- `resources/views/admin/orders/index.blade.php` - Admin order management
- `resources/views/admin/orders/show.blade.php` - Admin order details

**UI Features**:
- Product selection with quantity
- Dynamic total calculation (JavaScript)
- Referral code input with validation
- Order status badges
- Order timeline/status history
- Pagination and search

### 4.10 Routes
```php
Route::middleware(['auth'])->group(function () {
    Route::resource('orders', OrderController::class)->only(['index', 'create', 'store', 'show']);
});

Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/approve', [AdminOrderController::class, 'approve'])->name('orders.approve');
    Route::post('orders/{order}/reject', [AdminOrderController::class, 'reject'])->name('orders.reject');
    Route::post('orders/{order}/deliver', [AdminOrderController::class, 'deliver'])->name('orders.deliver');
});
```

**Deliverables**:
- Order creation workflow for all user types
- Referral code validation
- Admin approval system
- Order status tracking
- Complete UI for order management

---

## Phase 5: Sales Classification Logic

### 5.1 Sale Type Classification Logic
**File**: `app/Services/SaleClassificationService.php`
```php
class SaleClassificationService
{
    public function classify(Order $order): string
    {
        // Doctor Direct Sale: Order placed by Doctor for themselves
        if ($order->user->hasRole('Doctor') && !$order->referral_code) {
            return 'doctor_direct';
        }
        
        // Referral Sale: Order placed with a Doctor's referral code
        if ($order->referral_code && $order->doctor_id) {
            return 'referral';
        }
        
        // Store Linked Sale: Order placed by Store
        if ($order->user->hasRole('Store')) {
            return 'store_linked';
        }
        
        // Company Direct Sale: Order placed by End User directly
        return 'company_direct';
    }
}
```

### 5.2 Update Order Model
**File**: `app/Models/Order.php`
```php
protected static function booted()
{
    static::created(function ($order) {
        $order->sale_type = app(SaleClassificationService::class)->classify($order);
        $order->save();
    });
}

public function getSaleTypeLabelAttribute(): string
{
    return match($this->sale_type) {
        'doctor_direct' => 'Doctor Direct Sale',
        'referral' => 'Referral Sale',
        'store_linked' => 'Store Linked Sale',
        'company_direct' => 'Company Direct Sale',
        default => 'Unknown'
    };
}
```

### 5.3 Display Sale Type in Admin Views
Update order views to show sale type badge with color coding:
- Doctor Direct: Blue
- Referral: Green
- Store Linked: Yellow
- Company Direct: Gray

**Deliverables**:
- Automatic sale type classification
- Visible in admin order views and reports

---

## Phase 6: Doctor Target & Incentive System

### 6.1 Create Doctor Targets Migration
**File**: `database/migrations/xxxx_create_doctor_targets_table.php`
```php
Schema::create('doctor_targets', function (Blueprint $table) {
    $table->id();
    $table->foreignId('doctor_id')->constrained('users');
    $table->year('year');
    $table->tinyInteger('month');
    $table->integer('target_quantity')->default(30);
    $table->integer('achieved_quantity')->default(0);
    $table->boolean('target_completed')->default(false);
    $table->boolean('spin_eligible')->default(false);
    $table->timestamp('spin_completed_at')->nullable();
    $table->timestamps();
});
```

### 6.2 Create Doctor Target Model
**File**: `app/Models/DoctorTarget.php`
- Relationships to Doctor (User)
- Scopes for current month, completed targets

### 6.3 Create Target Service
**File**: `app/Services/DoctorTargetService.php`
```php
class DoctorTargetService
{
    public function updateTarget($doctorId, $quantity = 1)
    {
        $target = DoctorTarget::firstOrCreate(
            [
                'doctor_id' => $doctorId,
                'year' => now()->year,
                'month' => now()->month,
            ],
            ['target_quantity' => 30]
        );
        
        $target->achieved_quantity += $quantity;
        
        if ($target->achieved_quantity >= $target->target_quantity) {
            $target->target_completed = true;
            $target->spin_eligible = true;
        }
        
        $target->save();
        return $target;
    }
    
    public function getProgress($doctorId)
    {
        $target = DoctorTarget::where('doctor_id', $doctorId)
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->first();
            
        if (!$target) {
            return ['current' => 0, 'target' => 30, 'percentage' => 0];
        }
        
        return [
            'current' => $target->achieved_quantity,
            'target' => $target->target_quantity,
            'percentage' => min(100, round(($target->achieved_quantity / $target->target_quantity) * 100)),
            'completed' => $target->target_completed,
            'spin_eligible' => $target->spin_eligible,
        ];
    }
}
```

### 6.4 Update Order Controller
When order is approved and sale type is 'doctor_direct' or 'referral':
```php
// In OrderController@approve
if (in_array($order->sale_type, ['doctor_direct', 'referral'])) {
    $doctorId = $order->sale_type === 'doctor_direct' 
        ? $order->user_id 
        : $order->doctor_id;
        
    app(DoctorTargetService::class)->updateTarget($doctorId, $order->items->sum('quantity'));
}
```

### 6.5 Update Doctor Dashboard
**File**: `resources/views/dashboard/doctor.blade.php`
Add target progress widget:
- Progress bar showing current/target
- Percentage complete
- Spin eligibility status
- Monthly performance summary

### 6.6 Create Target History View
**File**: `resources/views/doctor/targets/index.blade.php`
- Monthly target history
- Achievement status
- Spin history

### 6.7 Routes
```php
Route::middleware(['auth', 'role:Doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('targets', [DoctorTargetController::class, 'index'])->name('targets.index');
    Route::get('targets/progress', [DoctorTargetController::class, 'progress'])->name('targets.progress');
});
```

**Deliverables**:
- Monthly target tracking (30 products)
- Automatic progress update on order approval
- Progress bar in Doctor Dashboard
- Spin eligibility activation

---

## Phase 7: Spin & Reward System

### 7.1 Create Rewards Migration
**File**: `database/migrations/xxxx_create_rewards_table.php`
```php
Schema::create('rewards', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->enum('type', ['cash', 'gift', 'voucher'])->default('gift');
    $table->decimal('value', 10, 2)->nullable();
    $table->integer('quantity')->default(0);
    $table->integer('remaining')->default(0);
    $table->enum('status', ['active', 'inactive'])->default('active');
    $table->timestamps();
});
```

### 7.2 Create Spin History Migration
**File**: `database/migrations/xxxx_create_spin_histories_table.php`
```php
Schema::create('spin_histories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('doctor_id')->constrained('users');
    $table->foreignId('reward_id')->constrained();
    $table->foreignId('doctor_target_id')->constrained();
    $table->timestamp('spun_at');
    $table->timestamps();
});
```

### 7.3 Create Models
**Files**:
- `app/Models/Reward.php`
- `app/Models/SpinHistory.php`

### 7.4 Create Admin Reward Controller
**File**: `app/Http/Controllers/Admin/RewardController.php`
- CRUD operations for rewards
- Assign reward for specific spin

### 7.5 Create Doctor Spin Controller
**File**: `app/Http/Controllers/Doctor/SpinController.php`
```php
public function index()
{
    $doctor = auth()->user();
    $target = DoctorTarget::where('doctor_id', $doctor->id)
        ->where('spin_eligible', true)
        ->whereNull('spin_completed_at')
        ->first();
        
    return view('doctor.spin.index', compact('target'));
}

public function spin(Request $request)
{
    $doctor = auth()->user();
    $target = DoctorTarget::where('doctor_id', $doctor->id)
        ->where('spin_eligible', true)
        ->whereNull('spin_completed_at')
        ->firstOrFail();
    
    // Admin pre-assigns reward - no random logic
    $reward = Reward::where('status', 'active')
        ->where('remaining', '>', 0)
        ->first();
        
    if (!$reward) {
        return back()->with('error', 'No rewards available');
    }
    
    // Record spin
    SpinHistory::create([
        'doctor_id' => $doctor->id,
        'reward_id' => $reward->id,
        'doctor_target_id' => $target->id,
        'spun_at' => now(),
    ]);
    
    // Update target and reward
    $target->update(['spin_completed_at' => now()]);
    $reward->decrement('remaining');
    
    return redirect()->route('doctor.spin.result', $reward);
}
```

### 7.6 Create Reward Views (Admin)
**Files**:
- `resources/views/admin/rewards/index.blade.php`
- `resources/views/admin/rewards/create.blade.php`
- `resources/views/admin/rewards/edit.blade.php`

### 7.7 Create Spin Views (Doctor)
**Files**:
- `resources/views/doctor/spin/index.blade.php` - Spin wheel UI
- `resources/views/doctor/spin/result.blade.php` - Spin result
- `resources/views/doctor/spin/history.blade.php` - Reward history

**Spin Wheel UI**:
- Animated spin wheel (CSS/JS)
- Shows eligibility status
- Spin button (enabled only when eligible)
- Previous rewards list

### 7.8 Routes
```php
// Admin Routes
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('rewards', RewardController::class);
});

// Doctor Routes
Route::middleware(['auth', 'role:Doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('spin', [SpinController::class, 'index'])->name('spin.index');
    Route::post('spin', [SpinController::class, 'spin'])->name('spin.do');
    Route::get('spin/result/{reward}', [SpinController::class, 'result'])->name('spin.result');
    Route::get('spin/history', [SpinController::class, 'history'])->name('spin.history');
});
```

**Deliverables**:
- Admin can create/manage rewards
- Admin assigns reward before spin (controlled system)
- Doctor spin button when eligible
- Spin animation and result display
- Reward history page

---

## Phase 8: Reporting & Dashboard Analytics

### 8.1 Create Report Service
**File**: `app/Services/ReportService.php`
```php
class ReportService
{
    // Month wise sales
    public function getMonthlySales($year = null)
    {
        $year = $year ?? now()->year;
        return Order::whereYear('created_at', $year)
            ->where('status', 'delivered')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as orders, SUM(total_amount) as total')
            ->groupBy('month')
            ->get();
    }
    
    // Product wise sales
    public function getProductSales($from = null, $to = null)
    {
        $query = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'delivered');
            
        if ($from) $query->whereDate('orders.created_at', '>=', $from);
        if ($to) $query->whereDate('orders.created_at', '<=', $to);
        
        return $query->selectRaw('product_id, SUM(quantity) as total_qty, SUM(subtotal) as total_amount')
            ->groupBy('product_id')
            ->with('product')
            ->get();
    }
    
    // Doctor wise sales
    public function getDoctorSales($from = null, $to = null)
    {
        // Doctor direct + referral sales
        $query = Order::whereIn('sale_type', ['doctor_direct', 'referral'])
            ->where('status', 'delivered');
            
        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to) $query->whereDate('created_at', '<=', $to);
        
        return $query->selectRaw('
            CASE 
                WHEN sale_type = "doctor_direct" THEN user_id 
                ELSE doctor_id 
            END as doctor_id,
            COUNT(*) as total_orders,
            SUM(total_amount) as total_amount
        ')
        ->groupBy('doctor_id')
        ->with('doctor')
        ->get();
    }
    
    // Store wise sales
    public function getStoreSales($from = null, $to = null)
    {
        $query = Order::where('sale_type', 'store_linked')
            ->where('status', 'delivered');
            
        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to) $query->whereDate('created_at', '<=', $to);
        
        return $query->selectRaw('user_id as store_id, COUNT(*) as total_orders, SUM(total_amount) as total_amount')
            ->groupBy('store_id')
            ->with('user')
            ->get();
    }
}
```

### 8.2 Create Admin Report Controller
**File**: `app/Http/Controllers/Admin/ReportController.php`
- `dashboard()` - Admin dashboard with charts
- `monthlySales()` - Monthly sales report
- `productSales()` - Product wise report
- `doctorSales()` - Doctor performance report
- `storeSales()` - Store performance report

### 8.3 Create Doctor Report Controller
**File**: `app/Http/Controllers/Doctor/ReportController.php`
- `monthlyPerformance()` - Doctor's monthly stats
- `spinHistory()` - Reward/spin history

### 8.4 Create Store Report Controller
**File**: `app/Http/Controllers/Store/ReportController.php`
- `monthlySales()` - Store's monthly sales

### 8.5 Install Export Package
```bash
composer require maatwebsite/excel
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
```

### 8.6 Create Export Classes
**Files**:
- `app/Exports/MonthlySalesExport.php`
- `app/Exports/ProductSalesExport.php`
- `app/Exports/DoctorSalesExport.php`

### 8.7 Create Report Views
**Admin Reports**:
- `resources/views/admin/reports/dashboard.blade.php`
- `resources/views/admin/reports/monthly-sales.blade.php`
- `resources/views/admin/reports/product-sales.blade.php`
- `resources/views/admin/reports/doctor-sales.blade.php`
- `resources/views/admin/reports/store-sales.blade.php`

**Doctor Reports**:
- `resources/views/doctor/reports/performance.blade.php`
- `resources/views/doctor/reports/spin-history.blade.php`

**Store Reports**:
- `resources/views/store/reports/sales.blade.php`

**UI Features**:
- Date range filters
- Charts (using Chart.js or similar)
- Data tables with sorting
- Export buttons (PDF, Excel)
- Summary cards

### 8.8 Routes
```php
// Admin Reports
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('reports/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');
    Route::get('reports/monthly-sales', [ReportController::class, 'monthlySales'])->name('reports.monthly-sales');
    Route::get('reports/product-sales', [ReportController::class, 'productSales'])->name('reports.product-sales');
    Route::get('reports/doctor-sales', [ReportController::class, 'doctorSales'])->name('reports.doctor-sales');
    Route::get('reports/store-sales', [ReportController::class, 'storeSales'])->name('reports.store-sales');
    
    // Exports
    Route::get('reports/export/monthly-sales', [ReportController::class, 'exportMonthlySales'])->name('reports.export.monthly');
});
```

**Deliverables**:
- Admin dashboard with analytics
- Month-wise, product-wise, doctor-wise, store-wise reports
- Doctor performance and spin history
- Store monthly sales report
- PDF and Excel export functionality

---

## Phase 9: Security & Validation

### 9.1 Form Request Validation
All forms must have dedicated Request classes with proper validation rules.

### 9.2 Role Middleware Protection
All routes must be protected with appropriate role middleware.

### 9.3 Referral Code Security
- Validate referral code belongs to active Doctor
- Prevent self-referral
- Rate limiting on validation attempts

### 9.4 Activity Logging (Optional)
```bash
composer require spatie/laravel-activitylog
```
Log important actions:
- User login/logout
- Order creation/approval
- Status changes

### 9.5 Prevent Duplicate Order Submission
Use database transactions and unique constraints:
```php
// In OrderController@store
return DB::transaction(function () {
    $order = Order::create([...]);
    // Create items
    return $order;
});
```

Add JavaScript prevention:
```javascript
// Disable submit button after click
form.addEventListener('submit', function() {
    submitButton.disabled = true;
    submitButton.textContent = 'Processing...';
});
```

### 9.6 Password Security
- Laravel's default Bcrypt hashing (already configured)
- Password validation rules in requests

### 9.7 CSRF Protection
All forms must include `@csrf` directive.

### 9.8 XSS Prevention
Use `{{ }}` (escaped) output in Blade templates.

### 9.9 SQL Injection Prevention
Use Eloquent ORM or query bindings throughout.

**Deliverables**:
- Form validation on all inputs
- Role-based access control
- Secure referral validation
- Activity logging
- Duplicate submission prevention

---

## Phase 10: Final Testing & Deployment Ready

### 10.1 Testing Checklist
- [ ] User authentication (all roles)
- [ ] User CRUD operations
- [ ] Product CRUD operations
- [ ] Order workflow (create → approve → deliver)
- [ ] Referral code validation
- [ ] Sale type classification
- [ ] Doctor target tracking
- [ ] Spin and reward system
- [ ] Reports and exports
- [ ] Form validation
- [ ] Role-based access
- [ ] Responsive UI on mobile/tablet/desktop

### 10.2 UI Polishing
- Consistent color scheme
- Loading states
- Empty states
- Error pages (404, 403, 500)
- Toast notifications for actions

### 10.3 Error Handling
**File**: `app/Exceptions/Handler.php`
- Custom error pages
- User-friendly error messages
- Log errors appropriately

### 10.4 Production Configuration
Update `.env` for production:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pharma_production
DB_USERNAME=your_username
DB_PASSWORD=your_password

CACHE_DRIVER=file
SESSION_DRIVER=database
QUEUE_DRIVER=database
```

### 10.5 Optimization
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 10.6 Database Seeding for Demo
**File**: `database/seeders/DemoDataSeeder.php`
- Sample users for each role
- Sample products
- Sample orders

**Deliverables**:
- Fully tested system
- Polished UI
- Production-ready configuration
- Demo data for client review

---

## Folder Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── UserController.php
│   │   │   ├── ProductController.php
│   │   │   ├── OrderController.php
│   │   │   ├── RewardController.php
│   │   │   └── ReportController.php
│   │   ├── Dashboard/
│   │   │   ├── AdminDashboardController.php
│   │   │   ├── DoctorDashboardController.php
│     │   │   ├── StoreDashboardController.php
│   │   │   └── UserDashboardController.php
│   │   ├── Doctor/
│   │   │   ├── SpinController.php
│   │   │   ├── TargetController.php
│   │   │   └── ReportController.php
│   │   ├── Store/
│   │   │   └── ReportController.php
│   │   ├── OrderController.php
│   │   └── Controller.php
│   ├── Middleware/
│   │   └── RoleMiddleware.php
│   └── Requests/
│       ├── Admin/
│       │   ├── StoreUserRequest.php
│       │   ├── UpdateUserRequest.php
│       │   ├── StoreProductRequest.php
│       │   └── UpdateProductRequest.php
│       └── StoreOrderRequest.php
├── Models/
│   ├── User.php
│   ├── Product.php
│   ├── Order.php
│   ├── OrderItem.php
│   ├── DoctorTarget.php
│   ├── Reward.php
│   └── SpinHistory.php
├── Services/
│   ├── SaleClassificationService.php
│   ├── DoctorTargetService.php
│   └── ReportService.php
└── Exports/
    ├── MonthlySalesExport.php
    ├── ProductSalesExport.php
    └── DoctorSalesExport.php

resources/views/
├── layouts/
│   ├── master.blade.php
│   └── partials/
│       ├── admin-sidebar.blade.php
│       ├── doctor-sidebar.blade.php
│       ├── store-sidebar.blade.php
│       └── navbar.blade.php
├── dashboard/
│   ├── admin.blade.php
│   ├── doctor.blade.php
│   ├── store.blade.php
│   └── user.blade.php
├── admin/
│   ├── users/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── products/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── orders/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── rewards/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   └── reports/
│       ├── dashboard.blade.php
│       ├── monthly-sales.blade.php
│       ├── product-sales.blade.php
│       ├── doctor-sales.blade.php
│       └── store-sales.blade.php
├── doctor/
│   ├── spin/
│   │   ├── index.blade.php
│   │   ├── result.blade.php
│   │   └── history.blade.php
│   ├── targets/
│   │   └── index.blade.php
│   └── reports/
│       ├── performance.blade.php
│       └── spin-history.blade.php
├── store/
│   └── reports/
│       └── sales.blade.php
├── orders/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── show.blade.php
└── components/
    └── (reusable components)

routes/
└── web.php
```

---

## Development Workflow

1. **Start each phase** by creating migrations and models
2. **Implement backend** (controllers, services, validation)
3. **Build UI** (Blade views with Tailwind CSS)
4. **Test thoroughly** before moving to next phase
5. **Review and fix** any issues
6. **Proceed to next phase** only after approval

## Key Principles

- **Modular Structure**: Each module is self-contained
- **Clean Code**: Follow PSR standards, use type hints
- **Reusable Components**: Create Blade components for common UI elements
- **No Hardcoded Logic**: Use config files and constants
- **Comments**: Document complex business logic
- **Scalable**: Design for future enhancements
- **Consistent UI**: Use same design patterns throughout
