<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Buyer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\ActivityLog;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard with real-time statistics.
     */
    public function index(): View
    {
        // Calculate real statistics
        $stats = [
            'users' => User::count(),
            'suppliers' => Supplier::count(),
            'buyers' => Buyer::count(),
            'products' => Product::count(),
            'orders' => Order::count(),
            'rfqs' => Rfq::count(),
            'quotations' => Quotation::count(),
            'invoices' => Invoice::count(),
            'revenue' => number_format(Invoice::where('status', 'approved')->sum('total_amount'), 2) . ' د.ل',
        ];

        // Calculate trends (comparing last 30 days with previous 30 days)
        $now = now();
        $last30Days = $now->copy()->subDays(30);
        $previous30Days = $last30Days->copy()->subDays(30);

        $usersLast30 = User::where('created_at', '>=', $last30Days)->count();
        $usersPrevious30 = User::whereBetween('created_at', [$previous30Days, $last30Days])->count();
        $usersTrend = $usersPrevious30 > 0 
            ? round((($usersLast30 - $usersPrevious30) / $usersPrevious30) * 100, 1)
            : ($usersLast30 > 0 ? 100 : 0);

        $suppliersLast30 = Supplier::where('created_at', '>=', $last30Days)->count();
        $suppliersPrevious30 = Supplier::whereBetween('created_at', [$previous30Days, $last30Days])->count();
        $suppliersTrend = $suppliersPrevious30 > 0 
            ? round((($suppliersLast30 - $suppliersPrevious30) / $suppliersPrevious30) * 100, 1)
            : ($suppliersLast30 > 0 ? 100 : 0);

        $buyersLast30 = Buyer::where('created_at', '>=', $last30Days)->count();
        $buyersPrevious30 = Buyer::whereBetween('created_at', [$previous30Days, $last30Days])->count();
        $buyersTrend = $buyersPrevious30 > 0 
            ? round((($buyersLast30 - $buyersPrevious30) / $buyersPrevious30) * 100, 1)
            : ($buyersLast30 > 0 ? 100 : 0);

        // Get recent activities from activity log
        $recentActivities = ActivityLog::with(['causer', 'subject'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($activity) {
                $description = $activity->description ?? 'نشاط غير محدد';
                $causerName = $activity->causer?->name ?? 'نظام';
                
                // Format activity based on subject type
                if ($activity->subject_type && $activity->subject) {
                    $subjectType = class_basename($activity->subject_type);
                    switch ($subjectType) {
                        case 'Order':
                            $orderNumber = $activity->subject->order_number ?? 'N/A';
                            $description = "طلب جديد: {$orderNumber}";
                            break;
                        case 'Quotation':
                            $referenceCode = $activity->subject->reference_code ?? 'N/A';
                            $description = "عرض سعر جديد: {$referenceCode}";
                            break;
                        case 'Supplier':
                            $companyName = $activity->subject->company_name ?? 'N/A';
                            $description = "مورد جديد: {$companyName}";
                            break;
                        case 'Buyer':
                            $orgName = $activity->subject->organization_name ?? 'N/A';
                            $description = "مشتري جديد: {$orgName}";
                            break;
                        case 'Product':
                            $productName = $activity->subject->name ?? 'N/A';
                            $description = "منتج جديد: {$productName}";
                            break;
                    }
                }

                return [
                    'icon' => $this->getActivityIcon($activity->event ?? 'created'),
                    'iconBg' => $this->getActivityIconBg($activity->event ?? 'created'),
                    'iconColor' => $this->getActivityIconColor($activity->event ?? 'created'),
                    'title' => $description,
                    'description' => "بواسطة: {$causerName}",
                    'time' => $activity->created_at->diffForHumans(),
                    'badge' => $this->getActivityBadge($activity->event ?? 'created'),
                    'badgeClass' => $this->getActivityBadgeClass($activity->event ?? 'created'),
                ];
            })
            ->toArray();

        // If no activities, use default
        if (empty($recentActivities)) {
            $recentActivities = [
                [
                    'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
                    'iconBg' => 'from-medical-blue-100 to-medical-blue-200',
                    'iconColor' => 'text-medical-blue-600',
                    'title' => 'مرحباً بك في لوحة التحكم',
                    'description' => 'ابدأ بإدارة النظام',
                    'time' => 'الآن',
                    'badge' => 'جديد',
                    'badgeClass' => 'bg-medical-blue-50 text-medical-blue-600',
                ],
            ];
        }

        // Get orders and users data for last 7 days for chart
        $last7Days = collect(range(6, 0))->map(function ($daysAgo) {
            return now()->subDays($daysAgo)->format('Y-m-d');
        });

        $ordersData = $last7Days->map(function ($date) {
            return Order::whereDate('created_at', $date)->count();
        })->toArray();

        $usersData = $last7Days->map(function ($date) {
            return User::whereDate('created_at', $date)->count();
        })->toArray();

        $chartSeries = [
            ['name' => 'الطلبات', 'data' => $ordersData],
            ['name' => 'المستخدمين الجدد', 'data' => $usersData],
        ];

        $chartCategories = $last7Days->map(function ($date) {
            return now()->parse($date)->format('D');
        })->toArray();

        // Quick actions
        $quickActions = [
            [
                'title' => 'إضافة مستخدم',
                'description' => 'إنشاء حساب جديد',
                'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
                'url' => route('admin.users'),
                'gradient' => 'from-medical-blue-50 to-medical-blue-100',
                'iconColor' => 'text-medical-blue-600',
                'textColor' => 'text-medical-blue-700',
                'descColor' => 'text-medical-blue-600',
            ],
            [
                'title' => 'إدارة الموردين',
                'description' => 'عرض وإدارة الموردين',
                'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                'url' => route('admin.suppliers'),
                'gradient' => 'from-medical-green-50 to-medical-green-100',
                'iconColor' => 'text-medical-green-600',
                'textColor' => 'text-medical-green-700',
                'descColor' => 'text-medical-green-600',
            ],
            [
                'title' => 'عرض التقارير',
                'description' => 'تقارير وإحصائيات',
                'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                'url' => route('admin.reports'),
                'gradient' => 'from-medical-blue-50 to-medical-green-50',
                'iconColor' => 'text-medical-blue-600',
                'textColor' => 'text-medical-blue-700',
                'descColor' => 'text-medical-blue-600',
            ],
            [
                'title' => 'الإعدادات',
                'description' => 'إعدادات النظام',
                'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                'url' => route('admin.settings.index'),
                'gradient' => 'from-medical-gray-50 to-medical-gray-100',
                'iconColor' => 'text-medical-gray-600',
                'textColor' => 'text-medical-gray-700',
                'descColor' => 'text-medical-gray-600',
            ],
        ];

        return view('dashboards.admin', compact(
            'stats',
            'usersTrend',
            'suppliersTrend',
            'buyersTrend',
            'recentActivities',
            'chartSeries',
            'chartCategories',
            'quickActions'
        ));
    }

    /**
     * Get activity icon based on event type.
     */
    private function getActivityIcon(string $event): string
    {
        return match ($event) {
            'created' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
            'updated' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
            'deleted' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
            default => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        };
    }

    /**
     * Get activity icon background color.
     */
    private function getActivityIconBg(string $event): string
    {
        return match ($event) {
            'created' => 'from-medical-blue-100 to-medical-blue-200',
            'updated' => 'from-medical-yellow-100 to-medical-yellow-200',
            'deleted' => 'from-medical-red-100 to-medical-red-200',
            default => 'from-medical-gray-100 to-medical-gray-200',
        };
    }

    /**
     * Get activity icon color.
     */
    private function getActivityIconColor(string $event): string
    {
        return match ($event) {
            'created' => 'text-medical-blue-600',
            'updated' => 'text-medical-yellow-600',
            'deleted' => 'text-medical-red-600',
            default => 'text-medical-gray-600',
        };
    }

    /**
     * Get activity badge text.
     */
    private function getActivityBadge(string $event): string
    {
        return match ($event) {
            'created' => 'جديد',
            'updated' => 'محدث',
            'deleted' => 'محذوف',
            default => 'نشاط',
        };
    }

    /**
     * Get activity badge class.
     */
    private function getActivityBadgeClass(string $event): string
    {
        return match ($event) {
            'created' => 'bg-medical-blue-50 text-medical-blue-600',
            'updated' => 'bg-medical-yellow-50 text-medical-yellow-600',
            'deleted' => 'bg-medical-red-50 text-medical-red-600',
            default => 'bg-medical-gray-50 text-medical-gray-600',
        };
    }
}

