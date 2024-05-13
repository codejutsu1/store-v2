<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Fashion and Apparel',
            'Electronics and Gadgets',
            'Home and Kitchen Appliances',
            'Beauty and Personal Care',
            'Health and Wellness',
            'Sports and Outdoor Equipment',
            'Books and Stationery',
            'Toys and Games',
            'Automotive Parts and Accessories',
            'Jewelry and Accessories',
            'Furniture and Home Decor',
            'Food and Grocery',
            'Baby and Kids Products',
            'Pet Supplies',
            'Office Supplies',
            'Arts and Crafts',
            'Travel and Luggage',
            'Musical Instruments',
            'Industrial and Scientific Equipment',
            'Services (such as travel bookings, event tickets)',
        ];

        foreach($tags as $tag){
            Tag::create(['name' => $tag]);
        }
    }
}
