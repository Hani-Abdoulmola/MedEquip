<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Supplier Factory
 * 
 * Generates realistic dummy data for suppliers with user accounts.
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    /**
     * Libyan cities
     */
    private array $libyanCities = [
        'طرابلس', 'بنغازي', 'مصراتة', 'سبها', 'زليتن', 'البيضاء', 'أجدابيا',
        'درنة', 'طبرق', 'الخمس', 'غريان', 'يفرن', 'زوارة', 'صبراتة',
    ];

    /**
     * Company name prefixes and suffixes
     */
    private array $companyPrefixes = [
        'شركة', 'مؤسسة', 'مجموعة', 'شركة', 'مؤسسة',
    ];

    private array $companySuffixes = [
        'للأجهزة الطبية', 'للتجهيزات الطبية', 'للمعدات الطبية', 'للخدمات الطبية',
        'للأدوات الطبية', 'للتقنيات الطبية', 'للمستلزمات الطبية',
    ];

    private array $companyNames = [
        'الطبية المتقدمة', 'الطبية الحديثة', 'الطبية العالمية', 'الطبية المتميزة',
        'الطبية المتطورة', 'الطبية الرائدة', 'الطبية النموذجية', 'الطبية المثالية',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate company name
        $prefix = $this->faker->randomElement($this->companyPrefixes);
        $name = $this->faker->randomElement($this->companyNames);
        $suffix = $this->faker->randomElement($this->companySuffixes);
        $companyName = $prefix . ' ' . $name . ' ' . $suffix;

        // Generate contact info
        $contactEmail = $this->faker->unique()->companyEmail();
        $contactPhone = '09' . $this->faker->numerify('#######'); // Libyan phone format

        // Generate commercial register and tax number
        $commercialRegister = 'CR-' . $this->faker->unique()->numerify('######');
        $taxNumber = 'TAX-' . $this->faker->unique()->numerify('######');

        return [
            'company_name' => $companyName,
            'commercial_register' => $commercialRegister,
            'tax_number' => $taxNumber,
            'country' => 'ليبيا',
            'city' => $this->faker->randomElement($this->libyanCities),
            'address' => $this->faker->address(),
            'contact_email' => $contactEmail,
            'contact_phone' => $contactPhone,
            'is_verified' => $this->faker->boolean(80), // 80% verified
            'verified_at' => $this->faker->optional(0.8)->dateTimeBetween('-1 year', 'now'),
            'is_active' => $this->faker->boolean(90), // 90% active
            'rejection_reason' => $this->faker->optional(0.1)->sentence(),
            'created_by' => User::inRandomOrder()->first()?->id,
            'updated_by' => User::inRandomOrder()->first()?->id,
        ];
    }

    /**
     * Create a supplier with a user account
     */
    public function withUser(): static
    {
        return $this->afterCreating(function (Supplier $supplier) {
            // Get or create supplier user type
            $supplierType = UserType::where('slug', 'supplier')->first();
            
            if (!$supplierType) {
                $supplierType = UserType::create([
                    'name' => 'Supplier',
                    'slug' => 'supplier',
                    'is_active' => true,
                ]);
            }

            // Create user account for supplier
            $user = User::create([
                'user_type_id' => $supplierType->id,
                'name' => $supplier->company_name,
                'email' => $supplier->contact_email,
                'phone' => $supplier->contact_phone,
                'password' => Hash::make('1234567890'), // Default password
                'status' => 'active',
            ]);

            // Assign Supplier role
            $user->assignRole('Supplier');

            // Link user to supplier
            $supplier->update(['user_id' => $user->id]);
        });
    }

    /**
     * Indicate that the supplier is verified
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    /**
     * Indicate that the supplier is not verified
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => false,
            'verified_at' => null,
        ]);
    }

    /**
     * Indicate that the supplier is active
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the supplier is inactive
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
