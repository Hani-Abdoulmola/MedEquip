<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Product Category Seeder
 *
 * Seeds a comprehensive hierarchical structure of medical equipment categories
 * with bilingual support (English/Arabic).
 *
 * @package Database\Seeders
 */
class ProductCategorySeeder extends Seeder
{
    /**
     * Sort order counter for maintaining category sequence.
     */
    private int $sortOrder = 1;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if categories already exist (prevents duplicate seeding)
        if (ProductCategory::exists()) {
            $this->command->warn('⚠️  Product categories already exist. Skipping seeding.');
            return;
        }

        $this->command->info('🌱 Seeding product categories...');

        DB::transaction(function () {
            $categories = $this->getCategoryData();

            foreach ($categories as $index => $categoryData) {
                $this->createCategoryTree($categoryData);
                $this->command->info("   ✓ Seeded: {$categoryData['name']}");
            }
        });

        $totalCategories = ProductCategory::count();
        $this->command->info("✅ Successfully seeded {$totalCategories} product categories!");
    }

    /**
     * Create a category and all its children recursively.
     *
     * @param array<string, mixed> $data Category data including name, name_ar, and optional children
     * @param int|null $parentId Parent category ID (null for root categories)
     * @return ProductCategory The created category instance
     */
    protected function createCategoryTree(array $data, ?int $parentId = null): ProductCategory
    {
        $category = ProductCategory::create([
            'name'        => $data['name'],
            'name_ar'     => $data['name_ar'] ?? null,
            'description' => $data['description'] ?? null,
            'parent_id'   => $parentId,
            'is_active'   => $data['is_active'] ?? true,
            'sort_order'  => $this->sortOrder++,
        ]);

        // Recursively create child categories if they exist
        if (!empty($data['children']) && is_array($data['children'])) {
            foreach ($data['children'] as $childData) {
                $this->createCategoryTree($childData, $category->id);
            }
        }

        return $category;
    }

    /**
     * Get the medical equipment category data structure.
     *
     * Returns a comprehensive hierarchical array of medical equipment categories
     * organized by specialty area with bilingual names.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getCategoryData(): array
    {
        return [
            // ==========================================
            // 1. Imaging & Diagnostic Equipment
            // ==========================================
            [
                'name'     => 'Imaging & Diagnostic Equipment',
                'name_ar'  => 'أجهزة التصوير والتشخيص',
                'children' => [
                    [
                        'name'     => 'X-Ray Systems',
                        'name_ar'  => 'أجهزة الأشعة السينية',
                        'children' => [
                            ['name' => 'Digital X-Ray Systems', 'name_ar' => 'أجهزة أشعة رقمية'],
                            ['name' => 'Mobile X-Ray Units',    'name_ar' => 'أجهزة أشعة متنقلة'],
                            ['name' => 'C-Arm Systems',         'name_ar' => 'أجهزة سي-آرم'],
                        ],
                    ],
                    [
                        'name'     => 'Ultrasound Systems',
                        'name_ar'  => 'أجهزة السونار (الأمواج فوق الصوتية)',
                        'children' => [
                            ['name' => 'General Ultrasound',  'name_ar' => 'سونار عام'],
                            ['name' => 'Portable Ultrasound', 'name_ar' => 'سونار محمول'],
                            ['name' => 'Cardiac Ultrasound',  'name_ar' => 'سونار قلبي'],
                            ['name' => '3D/4D Ultrasound',    'name_ar' => 'سونار ثلاثي/رباعي الأبعاد'],
                        ],
                    ],
                    [
                        'name'     => 'CT & MRI',
                        'name_ar'  => 'أجهزة الأشعة المقطعية والرنين',
                        'children' => [
                            ['name' => 'CT Scanners',  'name_ar' => 'أجهزة الأشعة المقطعية (CT)'],
                            ['name' => 'MRI Scanners', 'name_ar' => 'أجهزة الرنين المغناطيسي (MRI)'],
                            ['name' => 'CT Injectors', 'name_ar' => 'حقّانات الصبغة للأشعة المقطعية'],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 2. Patient Monitoring & Life Support
            // ==========================================
            [
                'name'     => 'Patient Monitoring & Life Support',
                'name_ar'  => 'أجهزة مراقبة المريض ودعم الحياة',
                'children' => [
                    [
                        'name'     => 'Patient Monitors',
                        'name_ar'  => 'أجهزة مراقبة المريض',
                        'children' => [
                            ['name' => 'Multi-Parameter Monitors', 'name_ar' => 'مونيتور متعدد المؤشرات'],
                            ['name' => 'Bedside Monitors',         'name_ar' => 'مونيتور جانب السرير'],
                            ['name' => 'Central Monitoring',       'name_ar' => 'نظام مراقبة مركزي'],
                        ],
                    ],
                    [
                        'name'     => 'Ventilators & Respiratory',
                        'name_ar'  => 'أجهزة التنفس والعناية التنفسية',
                        'children' => [
                            ['name' => 'ICU Ventilators',      'name_ar' => 'أجهزة تنفس للعناية المركزة'],
                            ['name' => 'Transport Ventilators', 'name_ar' => 'أجهزة تنفس للنقل'],
                            ['name' => 'CPAP/BiPAP Systems',   'name_ar' => 'أنظمة CPAP/BiPAP'],
                        ],
                    ],
                    [
                        'name'     => 'Infusion & Syringe Pumps',
                        'name_ar'  => 'مضخات المحاليل والحقن',
                        'children' => [
                            ['name' => 'Infusion Pumps', 'name_ar' => 'مضخات المحاليل'],
                            ['name' => 'Syringe Pumps',  'name_ar' => 'مضخات الحقن'],
                            ['name' => 'Feeding Pumps',  'name_ar' => 'مضخات التغذية الوريدية/الأنبوبية'],
                        ],
                    ],
                    [
                        'name'     => 'Anesthesia Machines',
                        'name_ar'  => 'أجهزة التخدير',
                        'children' => [
                            ['name' => 'OR Anesthesia Machines', 'name_ar' => 'أجهزة تخدير لغرف العمليات'],
                            ['name' => 'Portable Anesthesia',    'name_ar' => 'أجهزة تخدير متنقلة'],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 3. Operating Room & Surgical
            // ==========================================
            [
                'name'     => 'Operating Room & Surgical',
                'name_ar'  => 'غرف العمليات والجراحة',
                'children' => [
                    [
                        'name'     => 'Operating Tables',
                        'name_ar'  => 'طاولات العمليات',
                        'children' => [
                            ['name' => 'General OR Tables',          'name_ar' => 'طاولات عمليات عامة'],
                            ['name' => 'Electro-Hydraulic Tables',   'name_ar' => 'طاولات عمليات كهربائية/هيدروليكية'],
                        ],
                    ],
                    [
                        'name'     => 'Surgical Lights',
                        'name_ar'  => 'إضاءة جراحية',
                        'children' => [
                            ['name' => 'Ceiling Surgical Lights', 'name_ar' => 'كشافات سقفية لغرف العمليات'],
                            ['name' => 'Mobile Surgical Lights',  'name_ar' => 'كشافات عمليات متنقلة'],
                        ],
                    ],
                    [
                        'name'     => 'Electrosurgical Units',
                        'name_ar'  => 'أجهزة الكي الجراحي',
                        'children' => [
                            ['name' => 'Monopolar ESU', 'name_ar' => 'أجهزة كي أحادي القطب'],
                            ['name' => 'Bipolar ESU',   'name_ar' => 'أجهزة كي ثنائي القطب'],
                        ],
                    ],
                    [
                        'name'     => 'Endoscopy Systems',
                        'name_ar'  => 'أنظمة المناظير',
                        'children' => [
                            ['name' => 'GI Endoscopy', 'name_ar' => 'مناظير الجهاز الهضمي'],
                            ['name' => 'Laparoscopy',  'name_ar' => 'مناظير جراحية (مناظير البطن)'],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 4. Laboratory & Diagnostics
            // ==========================================
            [
                'name'     => 'Laboratory & Diagnostics',
                'name_ar'  => 'المختبرات والتشخيص المخبري',
                'children' => [
                    [
                        'name'     => 'Hematology Analyzers',
                        'name_ar'  => 'محللات أمراض الدم',
                        'children' => [
                            ['name' => '3-Part Differential', 'name_ar' => 'محلل دم 3 أجزاء'],
                            ['name' => '5-Part Differential', 'name_ar' => 'محلل دم 5 أجزاء'],
                        ],
                    ],
                    [
                        'name'     => 'Biochemistry Analyzers',
                        'name_ar'  => 'محللات الكيمياء الحيوية',
                        'children' => [
                            ['name' => 'Semi-Auto Analyzers',  'name_ar' => 'محللات شبه آلية'],
                            ['name' => 'Fully Auto Analyzers', 'name_ar' => 'محللات أوتوماتيكية كاملة'],
                        ],
                    ],
                    [
                        'name'     => 'Microbiology & Incubators',
                        'name_ar'  => 'الميكروبيولوجي والحضّانات',
                        'children' => [
                            ['name' => 'Incubators',         'name_ar' => 'حضّانات مخبرية'],
                            ['name' => 'Biosafety Cabinets', 'name_ar' => 'خزائن أمان حيوي'],
                        ],
                    ],
                    [
                        'name'     => 'POCT & Rapid Tests',
                        'name_ar'  => 'الاختبارات السريعة ونقطة الرعاية',
                        'children' => [
                            ['name' => 'Glucose Meters',  'name_ar' => 'أجهزة قياس السكر المحمولة'],
                            ['name' => 'POCT Analyzers', 'name_ar' => 'أجهزة تحليل نقطة الرعاية'],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 5. ICU & Emergency Care
            // ==========================================
            [
                'name'     => 'ICU & Emergency Care',
                'name_ar'  => 'العناية المركزة والطوارئ',
                'children' => [
                    [
                        'name'     => 'ICU Beds',
                        'name_ar'  => 'أسرة العناية المركزة',
                        'children' => [
                            ['name' => 'Electric ICU Beds', 'name_ar' => 'أسرة عناية كهربائية'],
                            ['name' => 'Manual ICU Beds',   'name_ar' => 'أسرة عناية يدوية'],
                        ],
                    ],
                    [
                        'name'     => 'Emergency & Transport',
                        'name_ar'  => 'الطوارئ والنقل الإسعافي',
                        'children' => [
                            ['name' => 'Stretcher Trolleys', 'name_ar' => 'نقالات إسعاف'],
                            ['name' => 'Spine Boards',       'name_ar' => 'ألواح العمود الفقري'],
                        ],
                    ],
                    [
                        'name'     => 'Defibrillators',
                        'name_ar'  => 'أجهزة الصدمات الكهربائية (Defibrillators)',
                        'children' => [
                            ['name' => 'Manual Defibrillators', 'name_ar' => 'صدمات كهربائية يدوية'],
                            ['name' => 'AED Devices',           'name_ar' => 'أجهزة صدمات أوتوماتيكية (AED)'],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 6. Rehabilitation & Physiotherapy
            // ==========================================
            [
                'name'     => 'Rehabilitation & Physiotherapy',
                'name_ar'  => 'التأهيل والعلاج الطبيعي',
                'children' => [
                    [
                        'name'     => 'Electrotherapy',
                        'name_ar'  => 'العلاج الكهربائي',
                        'children' => [
                            ['name' => 'TENS Units',         'name_ar' => 'أجهزة TENS'],
                            ['name' => 'Ultrasound Therapy', 'name_ar' => 'العلاج بالموجات فوق الصوتية'],
                        ],
                    ],
                    [
                        'name'     => 'Exercise & Training',
                        'name_ar'  => 'تمارين وتقوية العضلات',
                        'children' => [
                            ['name' => 'Treadmills',  'name_ar' => 'أجهزة السير المتحرك'],
                            ['name' => 'Rehab Bikes', 'name_ar' => 'دراجات تأهيلية'],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 7. Consumables & Disposables
            // ==========================================
            [
                'name'     => 'Consumables & Disposables',
                'name_ar'  => 'المستلزمات الطبية والاستهلاكية',
                'children' => [
                    [
                        'name'     => 'Syringes & Needles',
                        'name_ar'  => 'الحقن والإبر',
                        'children' => [
                            ['name' => 'Syringes', 'name_ar' => 'حقن'],
                            ['name' => 'Needles',  'name_ar' => 'إبر طبية'],
                        ],
                    ],
                    [
                        'name'     => 'Catheters & Tubes',
                        'name_ar'  => 'القساطر والأنابيب',
                        'children' => [
                            ['name' => 'IV Catheters',      'name_ar' => 'قساطر وريدية'],
                            ['name' => 'Urinary Catheters', 'name_ar' => 'قساطر بولية'],
                        ],
                    ],
                    [
                        'name'     => 'Dressings & Wound Care',
                        'name_ar'  => 'ضمادات وعناية بالجروح',
                        'children' => [
                            ['name' => 'Gauze & Bandages', 'name_ar' => 'شاش وضمادات'],
                            ['name' => 'Wound Dressings',  'name_ar' => 'ضمادات متقدمة للجروح'],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 8. Hospital Furniture & General
            // ==========================================
            [
                'name'     => 'Hospital Furniture & General',
                'name_ar'  => 'الأثاث الطبي والتجهيزات العامة',
                'children' => [
                    [
                        'name'     => 'Hospital Beds',
                        'name_ar'  => 'أسرة المستشفيات العامة',
                        'children' => [
                            ['name' => 'Manual Hospital Beds',   'name_ar' => 'أسرة يدوية'],
                            ['name' => 'Electric Hospital Beds', 'name_ar' => 'أسرة كهربائية'],
                        ],
                    ],
                    [
                        'name'     => 'Carts & Trolleys',
                        'name_ar'  => 'العربات الطبية',
                        'children' => [
                            ['name' => 'Medicine Trolleys',   'name_ar' => 'عربات أدوية'],
                            ['name' => 'Instrument Trolleys', 'name_ar' => 'عربات أدوات'],
                        ],
                    ],
                    [
                        'name'     => 'Waiting & Reception',
                        'name_ar'  => 'الاستقبال والانتظار',
                        'children' => [
                            ['name' => 'Waiting Chairs',  'name_ar' => 'كراسي انتظار'],
                            ['name' => 'Reception Desks', 'name_ar' => 'مكاتب استقبال'],
                        ],
                    ],
                ],
            ],
        ];
    }
}
