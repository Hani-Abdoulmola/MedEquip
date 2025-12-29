<?php

namespace Database\Seeders;

use App\Models\Manufacturer;
use Illuminate\Database\Seeder;

class ManufacturerSeeder extends Seeder
{
    public function run(): void
    {
        $manufacturers = [
            ['name' => 'GE Healthcare', 'country' => 'USA', 'website' => 'https://www.gehealthcare.com'],
            ['name' => 'Siemens Healthineers', 'country' => 'Germany', 'website' => 'https://www.siemens-healthineers.com'],
            ['name' => 'Philips Healthcare', 'country' => 'Netherlands', 'website' => 'https://www.philips.com/healthcare'],
            ['name' => 'Canon Medical', 'country' => 'Japan', 'website' => 'https://global.canon/en/medical'],
            ['name' => 'Medtronic', 'country' => 'Ireland', 'website' => 'https://www.medtronic.com'],
            ['name' => 'Dräger', 'country' => 'Germany', 'website' => 'https://www.draeger.com'],
            ['name' => 'Mindray', 'country' => 'China', 'website' => 'https://www.mindray.com'],
            ['name' => 'Roche Diagnostics', 'country' => 'Switzerland', 'website' => 'https://www.roche.com'],
            ['name' => 'Abbott', 'country' => 'USA', 'website' => 'https://www.abbott.com'],
            ['name' => 'Stryker', 'country' => 'USA', 'website' => 'https://www.stryker.com'],
            ['name' => 'Olympus Medical', 'country' => 'Japan', 'website' => 'https://medical.olympusamerica.com'],
            ['name' => 'Hill-Rom', 'country' => 'USA', 'website' => 'https://www.hillrom.com'],
            ['name' => 'Getinge', 'country' => 'Sweden', 'website' => 'https://www.getinge.com'],
            ['name' => 'Steris', 'country' => 'USA', 'website' => 'https://www.steris.com'],
        ];

        foreach ($manufacturers as $data) {
            Manufacturer::updateOrCreate(
                ['name' => $data['name']],
                array_merge($data, ['is_active' => true])
            );
        }
    }
}
