<?php

namespace Agency\ProductSample\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSampleSeeder extends Seeder
{
    public function run(): void
    {
        $customer = DB::table('customers')->first();
        $products = DB::table('products')->take(3)->get();

        if (! $customer || $products->isEmpty()) {
            echo "No customers or products found. Skipping sample seeder.\n";
            return;
        }

        // Create sample inventory for products
        foreach ($products as $product) {
            DB::table('sample_inventory')->insertOrIgnore([
                'product_id'   => $product->id,
                'sample_stock' => rand(5, 20),
                'allocated'    => 0,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // Request 1: Pending
        DB::table('sample_requests')->insert([
            'customer_id'      => $customer->id,
            'product_id'       => $products[0]->id,
            'quantity'         => 1,
            'shipping_address' => '123 MG Road, Bangalore, Karnataka 560001',
            'status'           => 'pending',
            'created_at'       => now()->subDay(),
            'updated_at'       => now()->subDay(),
        ]);

        // Request 2: Approved
        DB::table('sample_requests')->insert([
            'customer_id'      => $customer->id,
            'product_id'       => $products->count() > 1 ? $products[1]->id : $products[0]->id,
            'quantity'         => 2,
            'shipping_address' => '456 Nehru Place, New Delhi 110019',
            'status'           => 'approved',
            'created_at'       => now()->subDays(3),
            'updated_at'       => now()->subDays(2),
        ]);

        // Request 3: Shipped
        DB::table('sample_requests')->insert([
            'customer_id'      => $customer->id,
            'product_id'       => $products->count() > 2 ? $products[2]->id : $products[0]->id,
            'quantity'         => 1,
            'shipping_address' => '789 Anna Salai, Chennai, Tamil Nadu 600002',
            'status'           => 'shipped',
            'shipped_at'       => now()->subDay(),
            'tracking_info'    => 'SR12345678IN',
            'created_at'       => now()->subDays(5),
            'updated_at'       => now()->subDay(),
        ]);

        // Request 4: Delivered
        DB::table('sample_requests')->insert([
            'customer_id'      => $customer->id,
            'product_id'       => $products[0]->id,
            'quantity'         => 1,
            'shipping_address' => '101 FC Road, Pune, Maharashtra 411004',
            'status'           => 'delivered',
            'shipped_at'       => now()->subDays(4),
            'tracking_info'    => 'SR98765432IN',
            'delivered_at'     => now()->subDays(2),
            'created_at'       => now()->subDays(7),
            'updated_at'       => now()->subDays(2),
        ]);

        // Request 5: Rejected
        DB::table('sample_requests')->insert([
            'customer_id'       => $customer->id,
            'product_id'        => $products[0]->id,
            'quantity'          => 1,
            'shipping_address'  => '222 Brigade Road, Bangalore 560025',
            'status'            => 'rejected',
            'rejection_reason'  => 'Sample stock temporarily unavailable.',
            'created_at'        => now()->subDays(6),
            'updated_at'        => now()->subDays(5),
        ]);

        echo "✓ 5 sample requests + inventory created\n";
    }
}
