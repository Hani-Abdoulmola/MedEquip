<?php

namespace Database\Factories;

use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Product Factory
 *
 * Generates realistic dummy data for medical equipment products.
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Medical equipment product names (English / Arabic)
     */
    private array $productNames = [
        ['en' => 'X-Ray Machine', 'ar' => 'جهاز الأشعة السينية'],
        ['en' => 'CT Scanner', 'ar' => 'جهاز التصوير المقطعي'],
        ['en' => 'MRI System', 'ar' => 'جهاز الرنين المغناطيسي'],
        ['en' => 'Ultrasound System', 'ar' => 'جهاز الموجات فوق الصوتية'],
        ['en' => 'ECG Machine', 'ar' => 'جهاز تخطيط القلب'],
        ['en' => 'Ventilator', 'ar' => 'جهاز التنفس الصناعي'],
        ['en' => 'Defibrillator', 'ar' => 'جهاز إزالة الرجفان'],
        ['en' => 'Infusion Pump', 'ar' => 'مضخة الحقن الوريدي'],
        ['en' => 'Patient Monitor', 'ar' => 'جهاز مراقبة المريض'],
        ['en' => 'Blood Pressure Monitor', 'ar' => 'جهاز قياس ضغط الدم'],
        ['en' => 'Pulse Oximeter', 'ar' => 'جهاز قياس الأكسجين'],
        ['en' => 'Autoclave', 'ar' => 'جهاز التعقيم بالبخار'],
        ['en' => 'Centrifuge', 'ar' => 'جهاز الطرد المركزي'],
        ['en' => 'Microscope', 'ar' => 'المجهر'],
        ['en' => 'Incubator', 'ar' => 'الحاضنة'],
        ['en' => 'Anesthesia Machine', 'ar' => 'جهاز التخدير'],
        ['en' => 'Dialysis Machine', 'ar' => 'جهاز غسيل الكلى'],
        ['en' => 'Surgical Light', 'ar' => 'مصباح الجراحة'],
        ['en' => 'Operating Table', 'ar' => 'طاولة العمليات'],
        ['en' => 'Electrosurgical Unit', 'ar' => 'وحدة الجراحة الكهربائية'],
        ['en' => 'Endoscope', 'ar' => 'المنظار الداخلي'],
        ['en' => 'Laparoscope', 'ar' => 'منظار البطن'],
        ['en' => 'Surgical Laser', 'ar' => 'ليزر الجراحة'],
        ['en' => 'C-Arm System', 'ar' => 'نظام C-Arm'],
        ['en' => 'Mammography System', 'ar' => 'جهاز تصوير الثدي'],
        ['en' => 'Bone Densitometer', 'ar' => 'جهاز قياس كثافة العظام'],
        ['en' => 'Ultrasound Probe', 'ar' => 'مسبار الموجات فوق الصوتية'],
        ['en' => 'Doppler System', 'ar' => 'نظام دوبلر'],
        ['en' => 'Holter Monitor', 'ar' => 'جهاز هولتر'],
        ['en' => 'Stress Test System', 'ar' => 'جهاز اختبار الإجهاد'],
    ];

    private array $brands = [
        'MedTech Pro', 'HealthCare Plus', 'MediEquip', 'LifeSupport Systems', 'Precision Medical',
        'Advanced Diagnostics', 'Surgical Excellence', 'Patient Care Solutions', 'Medical Innovations',
        'Global Health Tech', 'ProCare Medical', 'UltraMed Systems', 'Prime Healthcare', 'Elite Medical',
    ];

    private array $models = [
        'X-2000', 'CT-500', 'MRI-3T', 'US-3000', 'ECG-Pro', 'VENT-X1', 'DEF-A200',
        'INF-P500', 'MON-VS200', 'BP-300', 'OXI-P100', 'AUTO-S200', 'CENT-5000',
        'MICRO-2000X', 'INCU-B200', 'ANES-A300', 'DIAL-D500', 'LIGHT-SL300', 'TABLE-OT200',
        'ESU-P300', 'ENDO-F200', 'LAP-HD300', 'LASER-L200', 'CARM-C300', 'MAM-M300',
    ];

    private array $medicalClasses = [
        'I', 'IIa', 'IIb', 'III', 'exempt', 'not_applicable',
    ];

    private array $isoCertifications = [
        'ISO 13485', 'ISO 9001', 'ISO 14001', 'ISO 27001',
    ];

    /**
     * Arabic product descriptions templates
     */
    private array $arabicDescriptions = [
        'جهاز طبي متقدم مصمم لتوفير دقة عالية في التشخيص والعلاج. يتميز بسهولة الاستخدام والموثوقية العالية.',
        'معدات طبية حديثة توفر حلولاً متكاملة للرعاية الصحية. مصممة وفقاً لأعلى المعايير الدولية.',
        'جهاز طبي احترافي يوفر نتائج دقيقة وموثوقة. مناسب للاستخدام في المستشفيات والعيادات.',
        'معدات طبية عالية الجودة مصنوعة من أفضل المواد. تضمن الأداء الأمثل والسلامة للمرضى.',
        'جهاز طبي متطور يوفر تقنيات حديثة في مجال الرعاية الصحية. سهل التشغيل والصيانة.',
        'معدات طبية موثوقة مصممة لتلبية احتياجات المؤسسات الصحية. تتميز بالكفاءة والجودة العالية.',
        'جهاز طبي متكامل يوفر حلولاً شاملة للرعاية الطبية. مصمم وفقاً لأحدث التقنيات.',
        'معدات طبية احترافية توفر دقة عالية في القياسات والتشخيص. مناسبة للاستخدام المكثف.',
        'جهاز طبي حديث يتميز بالتصميم العصري والأداء المتميز. يوفر راحة للمرضى والعاملين.',
        'معدات طبية عالية الجودة مصنوعة وفقاً للمعايير الدولية. تضمن السلامة والفعالية.',
        'جهاز طبي متقدم يوفر تقنيات مبتكرة في التشخيص والعلاج. سهل الاستخدام والصيانة.',
        'معدات طبية موثوقة مصممة لتوفير أفضل النتائج. مناسبة للاستخدام في مختلف المؤسسات الصحية.',
        'جهاز طبي احترافي يتميز بالدقة والموثوقية. مصمم لتحسين جودة الرعاية الصحية.',
        'معدات طبية عالية التقنية توفر حلولاً متكاملة. تضمن الأداء الأمثل والسلامة.',
        'جهاز طبي متطور مصمم لتلبية احتياجات المؤسسات الصحية الحديثة. يوفر كفاءة عالية.',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productName = $this->faker->randomElement($this->productNames);
        $nameEn = $productName['en'];
        $nameAr = $productName['ar'];
        // Combine both names: "English Name / Arabic Name"
        $name = $nameEn . ' / ' . $nameAr;

        $model = $this->faker->randomElement($this->models);
        $brand = $this->faker->randomElement($this->brands);

        // Generate SKU using English name
        $sku = strtoupper(substr($brand, 0, 3)) . '-' .
               strtoupper(substr($nameEn, 0, 3)) . '-' .
               $this->faker->unique()->numerify('####');

        return [
            'sku' => $sku,
            'name' => $name,
            'model' => $model,
            'brand' => $brand,
            'manufacturer_id' => Manufacturer::inRandomOrder()->first()?->id ?? Manufacturer::factory(),
            'category_id' => ProductCategory::inRandomOrder()->first()?->id ?? ProductCategory::factory(),
            'description' => $this->generateArabicDescription(),
            'is_active' => $this->faker->boolean(90), // 90% active
            'review_status' => $this->faker->randomElement([
                Product::REVIEW_PENDING,
                Product::REVIEW_APPROVED,
                Product::REVIEW_APPROVED, // More approved products
                Product::REVIEW_APPROVED,
                Product::REVIEW_NEEDS_UPDATE,
            ]),
            'review_notes' => $this->faker->optional(0.2)->randomElement([
                'تمت المراجعة والموافقة',
                'المنتج يلبي جميع المعايير المطلوبة',
                'معلومات المنتج كاملة وصحيحة',
                'تم التحقق من جميع المواصفات',
            ]),
            'rejection_reason' => $this->faker->optional(0.1)->randomElement([
                'المعلومات غير كاملة',
                'المواصفات غير واضحة',
                'يحتاج إلى مراجعة إضافية',
            ]),
            'specifications' => $this->generateSpecifications(),
            'features' => $this->generateFeatures(),
            'technical_data' => $this->generateTechnicalData(),
            'certifications' => $this->generateCertifications(),
            'installation_requirements' => $this->faker->optional(0.7)->randomElement([
                'يتطلب مساحة أرضية لا تقل عن 2x2 متر',
                'يحتاج إلى مصدر طاقة 220 فولت',
                'يتطلب تهوية مناسبة ودرجة حرارة محيطة',
                'يحتاج إلى اتصال بالإنترنت للعمل الكامل',
                'يتطلب تركيب من قبل فني متخصص',
            ]),
            'medical_class' => $this->faker->randomElement($this->medicalClasses),
            'ce_marked' => $this->faker->boolean(80),
            'fda_cleared' => $this->faker->boolean(60),
            'iso_certification' => $this->faker->optional(0.7)->randomElement($this->isoCertifications),
            'version' => $this->faker->numberBetween(1, 5),
            'source' => $this->faker->randomElement(['admin', 'supplier_request', 'import', 'seeder']),
            'created_by' => User::inRandomOrder()->first()?->id,
            'updated_by' => User::inRandomOrder()->first()?->id,
        ];
    }

    /**
     * Generate product specifications
     */
    private function generateSpecifications(): array
    {
        $specs = [];
        $possibleSpecs = [
            'Weight' => fn() => $this->faker->numberBetween(10, 500) . ' kg',
            'Dimensions' => fn() => $this->faker->numberBetween(50, 200) . ' x ' .
                                   $this->faker->numberBetween(50, 200) . ' x ' .
                                   $this->faker->numberBetween(50, 200) . ' cm',
            'Power Consumption' => fn() => $this->faker->numberBetween(100, 5000) . ' W',
            'Voltage' => fn() => $this->faker->randomElement(['110V', '220V', '110V/220V']),
            'Frequency' => fn() => $this->faker->randomElement(['50Hz', '60Hz', '50/60Hz']),
            'Display Size' => fn() => $this->faker->numberBetween(7, 24) . ' inches',
            'Resolution' => fn() => $this->faker->numberBetween(800, 4096) . ' x ' .
                                  $this->faker->numberBetween(600, 2160),
            'Operating Temperature' => fn() => $this->faker->numberBetween(5, 15) . '°C to ' .
                                               $this->faker->numberBetween(30, 40) . '°C',
            'Storage Temperature' => fn() => $this->faker->numberBetween(-20, 0) . '°C to ' .
                                            $this->faker->numberBetween(50, 70) . '°C',
            'Humidity' => fn() => $this->faker->numberBetween(20, 40) . '% to ' .
                                 $this->faker->numberBetween(60, 80) . '%',
        ];

        // Select 4-7 random specifications
        $selectedSpecs = $this->faker->randomElements(array_keys($possibleSpecs),
            $this->faker->numberBetween(4, 7));

        foreach ($selectedSpecs as $spec) {
            $specs[$spec] = $possibleSpecs[$spec]();
        }

        return $specs;
    }

    /**
     * Generate product features
     */
    private function generateFeatures(): array
    {
        $features = [
            'High-resolution display',
            'Touch screen interface',
            'Wireless connectivity',
            'Portable design',
            'Battery backup',
            'Auto-calibration',
            'Multi-language support',
            'Data export capability',
            'Cloud integration',
            'Mobile app support',
            'Real-time monitoring',
            'Alarm system',
            'User-friendly interface',
            'Compact size',
            'Energy efficient',
            'Durable construction',
            'Easy maintenance',
            'Comprehensive warranty',
        ];

        return $this->faker->randomElements($features, $this->faker->numberBetween(5, 10));
    }

    /**
     * Generate technical data
     */
    private function generateTechnicalData(): array
    {
        return [
            'warranty_period' => $this->faker->randomElement(['1 year', '2 years', '3 years', '5 years']),
            'service_interval' => $this->faker->randomElement(['6 months', '12 months', '18 months']),
            'calibration_required' => $this->faker->boolean(70),
            'training_provided' => $this->faker->boolean(80),
            'software_version' => $this->faker->randomFloat(1, 1.0, 5.0),
            'compatibility' => $this->faker->randomElement(['Windows', 'Linux', 'MacOS', 'All']),
        ];
    }

    /**
     * Generate Arabic description
     */
    private function generateArabicDescription(): string
    {
        // Get base description
        $baseDescription = $this->faker->randomElement($this->arabicDescriptions);

        // Add additional details (50% chance)
        if ($this->faker->boolean(50)) {
            $additionalDetails = [
                ' يتميز بواجهة مستخدم سهلة وواضحة.',
                ' يوفر تقارير مفصلة ودقيقة.',
                ' مصمم ليكون صديقاً للبيئة.',
                ' يتميز بكفاءة الطاقة العالية.',
                ' يوفر دعم فني متواصل.',
                ' مصنوع من مواد عالية الجودة.',
                ' يلبي جميع المعايير الصحية الدولية.',
            ];

            $baseDescription .= $this->faker->randomElement($additionalDetails);
        }

        // Add warranty/service info (30% chance)
        if ($this->faker->boolean(30)) {
            $warrantyInfo = [
                ' يشمل ضمان شامل لمدة سنتين.',
                ' يتضمن خدمة صيانة دورية.',
                ' يوفر دعم فني على مدار الساعة.',
            ];

            $baseDescription .= $this->faker->randomElement($warrantyInfo);
        }

        return $baseDescription;
    }

    /**
     * Generate certifications
     */
    private function generateCertifications(): array
    {
        $certifications = [];
        $possibleCerts = [
            'CE Mark',
            'FDA 510(k)',
            'ISO 13485',
            'ISO 9001',
            'IEC 60601',
            'UL Listed',
            'CSA Certified',
            'TÜV Certified',
        ];

        return $this->faker->randomElements($possibleCerts, $this->faker->numberBetween(2, 5));
    }

    /**
     * Configure the factory.
     * Placeholder image generation has been removed.
     * Use the artisan command 'products:add-placeholder-images' if needed.
     */
    public function configure(): static
    {
        // Image generation removed - no longer automatically adding placeholder images
        return $this;
    }

    /**
     * Indicate that the product is approved
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'review_status' => Product::REVIEW_APPROVED,
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the product is pending review
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'review_status' => Product::REVIEW_PENDING,
        ]);
    }

    /**
     * Indicate that the product is active
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the product is inactive
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
