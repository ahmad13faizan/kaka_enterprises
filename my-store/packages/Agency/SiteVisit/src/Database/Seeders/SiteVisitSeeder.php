<?php

namespace Agency\SiteVisit\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteVisitSeeder extends Seeder
{
    public function run(): void
    {
        $customer = DB::table('customers')->first();
        $product = DB::table('products')->first();
        $admin = DB::table('admins')->first();

        if (! $customer || ! $product) {
            echo "No customers or products found. Skipping site visit seeder.\n";
            return;
        }

        // Visit 1: Requested
        DB::table('site_visits')->insert([
            'customer_id'         => $customer->id,
            'product_id'          => $product->id,
            'address'             => '123 MG Road, Bangalore, Karnataka 560001',
            'preferred_date'      => now()->addDays(3)->format('Y-m-d'),
            'preferred_time_slot' => 'morning',
            'notes'               => 'Please call before arriving',
            'status'              => 'requested',
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        // Visit 2: Assigned
        DB::table('site_visits')->insert([
            'customer_id'         => $customer->id,
            'product_id'          => $product->id,
            'address'             => '456 Connaught Place, New Delhi 110001',
            'preferred_date'      => now()->addDays(5)->format('Y-m-d'),
            'preferred_time_slot' => 'afternoon',
            'notes'               => 'Ground floor office',
            'status'              => 'assigned',
            'assigned_rep_id'     => $admin->id,
            'assigned_by_id'      => $admin->id,
            'assigned_at'         => now(),
            'created_at'          => now()->subDays(2),
            'updated_at'          => now(),
        ]);

        // Visit 3: Scheduled
        DB::table('site_visits')->insert([
            'customer_id'         => $customer->id,
            'product_id'          => $product->id,
            'address'             => '789 Marine Drive, Mumbai, Maharashtra 400002',
            'preferred_date'      => now()->addDay()->format('Y-m-d'),
            'preferred_time_slot' => 'evening',
            'status'              => 'scheduled',
            'assigned_rep_id'     => $admin->id,
            'assigned_by_id'      => $admin->id,
            'assigned_at'         => now()->subDays(3),
            'scheduled_date'      => now()->addDay()->format('Y-m-d'),
            'scheduled_time'      => '4:00 PM',
            'created_at'          => now()->subDays(4),
            'updated_at'          => now(),
        ]);

        // Visit 4: Completed
        DB::table('site_visits')->insert([
            'customer_id'         => $customer->id,
            'product_id'          => $product->id,
            'address'             => '321 Park Street, Kolkata, West Bengal 700016',
            'preferred_date'      => now()->subDays(5)->format('Y-m-d'),
            'preferred_time_slot' => 'morning',
            'notes'               => 'Customer was very satisfied',
            'status'              => 'completed',
            'assigned_rep_id'     => $admin->id,
            'assigned_by_id'      => $admin->id,
            'assigned_at'         => now()->subDays(7),
            'scheduled_date'      => now()->subDays(5)->format('Y-m-d'),
            'scheduled_time'      => '10:00 AM',
            'completed_at'        => now()->subDays(5),
            'completion_notes'    => 'Demo completed. Customer interested in bulk order.',
            'created_at'          => now()->subDays(8),
            'updated_at'          => now()->subDays(5),
        ]);

        echo "✓ 4 sample site visits created\n";
    }
}
