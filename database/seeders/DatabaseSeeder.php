<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Roles & Permissions
        |--------------------------------------------------------------------------
        */

        $this->call([
            RolePermissionSeeder::class,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Subscription Plans
        |--------------------------------------------------------------------------
        */

        SubscriptionPlan::query()->upsert(
            [
                [
                    'name' => 'Student Monthly',
                    'slug' => 'student-monthly',
                    'audience' => 'student',
                    'billing_period' => 'monthly',
                    'price_minor' => 50000,
                    'currency' => 'KES',
                    'user_limit' => 1,
                    'features' => json_encode([
                        'core_library',
                        'reader',
                        'notes',
                        'quizzes',
                    ]),
                    'is_active' => true,
                ],

                [
                    'name' => 'Student Annual',
                    'slug' => 'student-annual',
                    'audience' => 'student',
                    'billing_period' => 'annual',
                    'price_minor' => 500000,
                    'currency' => 'KES',
                    'user_limit' => 1,
                    'features' => json_encode([
                        'full_library',
                        'reader',
                        'notes',
                        'quizzes',
                    ]),
                    'is_active' => true,
                ],

                [
                    'name' => 'Starter School',
                    'slug' => 'starter-school',
                    'audience' => 'school',
                    'billing_period' => 'annual',
                    'price_minor' => 5000000,
                    'currency' => 'KES',
                    'user_limit' => 100,
                    'features' => json_encode([
                        'core_library',
                        'assignments',
                        'basic_reports',
                    ]),
                    'is_active' => true,
                ],

                [
                    'name' => 'Standard School',
                    'slug' => 'standard-school',
                    'audience' => 'school',
                    'billing_period' => 'annual',
                    'price_minor' => 12000000,
                    'currency' => 'KES',
                    'user_limit' => 500,
                    'features' => json_encode([
                        'full_library',
                        'assignments',
                        'quizzes',
                        'reports',
                    ]),
                    'is_active' => true,
                ],
            ],
            ['slug'],
            [
                'name',
                'audience',
                'billing_period',
                'price_minor',
                'currency',
                'user_limit',
                'features',
                'is_active',
            ]
        );
    }
}