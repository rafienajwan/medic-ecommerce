<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\GuestBook;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@medic-ecommerce.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'address' => 'Jl. Admin No. 1, Menteng',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'postal_code' => '10310',
            'phone' => '081234567890',
            'email_verified_at' => now(),
        ]);

        // Create Vendor Users (Personal accounts - names are individual)
        $vendor1User = User::create([
            'name' => 'Budi Santoso',
            'email' => 'vendor1@medic-ecommerce.com',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'address' => 'Jl. Kesehatan No. 123, Dago',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'postal_code' => '40135',
            'phone' => '081234567891',
            'email_verified_at' => now(),
        ]);

        $vendor2User = User::create([
            'name' => 'Siti Rahayu',
            'email' => 'vendor2@medic-ecommerce.com',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'address' => 'Jl. Medika Raya No. 45, Gubeng',
            'city' => 'Surabaya',
            'province' => 'Jawa Timur',
            'postal_code' => '60281',
            'phone' => '081234567892',
            'email_verified_at' => now(),
        ]);

        $vendor3User = User::create([
            'name' => 'Ahmad Wijaya',
            'email' => 'vendor3@medic-ecommerce.com',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'address' => 'Jl. Gatot Subroto No. 88, Kuningan',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'postal_code' => '12950',
            'phone' => '081234567893',
            'email_verified_at' => now(),
        ]);

        $vendor4User = User::create([
            'name' => 'Rina Kusuma',
            'email' => 'vendor4@medic-ecommerce.com',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'address' => 'Jl. Sisingamangaraja No. 22, Polonia',
            'city' => 'Medan',
            'province' => 'Sumatera Utara',
            'postal_code' => '20157',
            'phone' => '081234567894',
            'email_verified_at' => now(),
        ]);

        $vendor5User = User::create([
            'name' => 'Dedi Firmansyah',
            'email' => 'vendor5@medic-ecommerce.com',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'address' => 'Jl. Pemuda No. 15, Simpang Lima',
            'city' => 'Semarang',
            'province' => 'Jawa Tengah',
            'postal_code' => '50132',
            'phone' => '081234567895',
            'email_verified_at' => now(),
        ]);

        // Create Regular Customers
        $customerData = [
            [
                'name' => 'Customer 1',
                'email' => 'customer1@example.com',
                'address' => 'Jl. Sudirman No. 10, Thamrin',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '10220',
            ],
            [
                'name' => 'Customer 2',
                'email' => 'customer2@example.com',
                'address' => 'Jl. Asia Afrika No. 25, Sumur Bandung',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40111',
            ],
            [
                'name' => 'Customer 3',
                'email' => 'customer3@example.com',
                'address' => 'Jl. Tunjungan No. 88, Genteng',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'postal_code' => '60275',
            ],
            [
                'name' => 'Customer 4',
                'email' => 'customer4@example.com',
                'address' => 'Jl. Imam Bonjol No. 5, Petisah',
                'city' => 'Medan',
                'province' => 'Sumatera Utara',
                'postal_code' => '20112',
            ],
            [
                'name' => 'Customer 5',
                'email' => 'customer5@example.com',
                'address' => 'Jl. Pahlawan No. 12, Semarang Tengah',
                'city' => 'Semarang',
                'province' => 'Jawa Tengah',
                'postal_code' => '50241',
            ],
        ];

        foreach ($customerData as $index => $customer) {
            User::create([
                'name' => $customer['name'],
                'email' => $customer['email'],
                'password' => Hash::make('password'),
                'role' => 'customer',
                'address' => $customer['address'],
                'city' => $customer['city'],
                'province' => $customer['province'],
                'postal_code' => $customer['postal_code'],
                'phone' => '0812345678' . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                'date_of_birth' => now()->subYears(rand(20, 50)),
                'gender' => ['male', 'female'][array_rand(['male', 'female'])],
                'email_verified_at' => now(),
            ]);
        }

        // Create Vendors (with new schema)
        $vendor1 = Vendor::create([
            'user_id' => $vendor1User->id,
            'business_name' => 'Alat Medis Sejahtera',
            'description' => 'Supplier alat medis profesional sejak 2010',
            'business_type' => 'Distributor',
            'business_address' => 'Jl. Kesehatan No. 123, Bandung',
            'business_phone' => '022-1234567',
            'business_email' => 'info@ams-medis.com',
            'tax_id' => '01.234.567.8-901.000',
            'business_license' => 'SIUP/001/2010',
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $admin->id,
        ]);

        $vendor2 = Vendor::create([
            'user_id' => $vendor2User->id,
            'business_name' => 'Prima Tech Solution',
            'description' => 'Penyedia solusi teknologi kesehatan terpadu',
            'business_type' => 'Toko',
            'business_address' => 'Jl. Medika Raya No. 45, Surabaya',
            'business_phone' => '031-9876543',
            'business_email' => 'contact@primatech.co.id',
            'tax_id' => '02.345.678.9-012.000',
            'business_license' => 'NIB/002/2015',
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $admin->id,
        ]);

        $vendor3 = Vendor::create([
            'user_id' => $vendor3User->id,
            'business_name' => 'Jakarta Medical Store',
            'description' => 'Toko alat kesehatan terlengkap di Jakarta',
            'business_type' => 'Toko',
            'business_address' => 'Jl. Gatot Subroto No. 88, Jakarta Pusat',
            'business_phone' => '021-5551234',
            'business_email' => 'info@jktmedical.com',
            'tax_id' => '03.456.789.0-123.000',
            'business_license' => 'SIUP/003/2012',
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $admin->id,
        ]);

        $vendor4 = Vendor::create([
            'user_id' => $vendor4User->id,
            'business_name' => 'Medan Health Equipment',
            'description' => 'Distributor alat kesehatan Sumatera Utara',
            'business_type' => 'Distributor',
            'business_address' => 'Jl. Sisingamangaraja No. 22, Medan',
            'business_phone' => '061-7778888',
            'business_email' => 'sales@medanhealth.co.id',
            'tax_id' => '04.567.890.1-234.000',
            'business_license' => 'NIB/004/2014',
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $admin->id,
        ]);

        $vendor5 = Vendor::create([
            'user_id' => $vendor5User->id,
            'business_name' => 'Semarang Medical Center',
            'description' => 'Pusat alat kesehatan Jawa Tengah',
            'business_type' => 'Toko',
            'business_address' => 'Jl. Pemuda No. 15, Semarang',
            'business_phone' => '024-3332222',
            'business_email' => 'info@smgmedical.com',
            'tax_id' => '05.678.901.2-345.000',
            'business_license' => 'SIUP/005/2016',
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $admin->id,
        ]);

        // Create Categories
        $categories = [
            [
                'name' => 'Alat Diagnostik',
                'description' => 'Alat-alat untuk diagnosis dan pemeriksaan kesehatan',
                'slug' => 'alat-diagnostik',
                'image_url' => '/storage/products/tensimeter-digital.jpg',
            ],
            [
                'name' => 'Alat Terapi',
                'description' => 'Peralatan untuk terapi dan rehabilitasi',
                'slug' => 'alat-terapi',
                'image_url' => '/storage/products/nebulizer.jpg',
            ],
            [
                'name' => 'Perlengkapan Medis',
                'description' => 'Perlengkapan medis dan rumah sakit',
                'slug' => 'perlengkapan-medis',
                'image_url' => '/storage/products/tandu-medis.jpg',
            ],
            [
                'name' => 'Obat & Perawatan',
                'description' => 'Obat-obatan dan produk perawatan kesehatan',
                'slug' => 'obat-perawatan',
                'image_url' => '/storage/products/betadine.jpg',
            ],
            [
                'name' => 'Kesehatan Ibu & Anak',
                'description' => 'Produk kesehatan untuk ibu dan anak',
                'slug' => 'kesehatan-ibu-anak',
                'image_url' => '/storage/products/pompa-asi-elektrik.jpg',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create Products based on actual photos
        $products = [
            // VENDOR 1 - Alat Medis Sejahtera (Bandung) - 4 products
            [
                'vendor_id' => $vendor1->id,
                'category_id' => 1, // Alat Diagnostik
                'name' => 'Tensimeter Digital Omron HEM-7120',
                'sku' => 'TEN-DIG-001',
                'description' => 'Tensimeter digital otomatis dengan teknologi Intellisense untuk pengukuran tekanan darah yang akurat dan cepat. Dilengkapi dengan indikator hipertensi dan memori untuk 30 pengukuran terakhir.',
                'price' => 375000,
                'stock' => 45,
                'image_url' => '/storage/products/tensimeter-digital.jpg',
                'is_active' => true,
            ],
            [
                'vendor_id' => $vendor1->id,
                'category_id' => 1, // Alat Diagnostik
                'name' => 'Oximeter Pulse Digital Fingertip',
                'sku' => 'OXI-001',
                'description' => 'Pulse oximeter untuk mengukur saturasi oksigen (SpO2) dan detak jantung dengan akurasi tinggi. Display OLED dengan 6 mode tampilan berbeda. Cocok untuk monitoring kesehatan di rumah.',
                'price' => 165000,
                'stock' => 80,
                'image_url' => '/storage/products/oximeter-pulse.png',
                'is_active' => true,
            ],
            [
                'vendor_id' => $vendor1->id,
                'category_id' => 1, // Alat Diagnostik
                'name' => 'Stetoskop Littmann Classic III',
                'sku' => 'STETO-LIT-003',
                'description' => 'Stetoskop profesional dengan akustik superior untuk mendengar suara jantung, paru-paru, dan tekanan darah dengan jelas. Double-sided chestpiece untuk dewasa dan anak. Garansi 5 tahun.',
                'price' => 2850000,
                'stock' => 25,
                'image_url' => '/storage/products/stetoskop.jpg',
                'is_active' => true,
            ],
            [
                'vendor_id' => $vendor1->id,
                'category_id' => 3, // Perlengkapan Medis
                'name' => 'Masker Medis 3 Ply Surgical (Box 50 pcs)',
                'sku' => 'MASK-3PLY-50',
                'description' => 'Masker bedah 3 lapis dengan BFE >95%, fluid resistant, dan breathable. Earloop elastis nyaman untuk pemakaian lama. Isi 50 pcs per box. Standar medis Indonesia.',
                'price' => 42000,
                'stock' => 250,
                'image_url' => '/storage/products/masker-medis.jpg',
                'is_active' => true,
            ],

            // VENDOR 2 - Prima Tech Solution (Surabaya) - 3 products
            [
                'vendor_id' => $vendor2->id,
                'category_id' => 1, // Alat Diagnostik
                'name' => 'Thermometer Infrared Non-Contact',
                'sku' => 'THERMO-IR-001',
                'description' => 'Thermometer infrared tanpa sentuh untuk pengukuran suhu tubuh yang cepat dan higienis. Jarak pengukuran 3-5 cm, hasil dalam 1 detik. Dilengkapi alarm demam dan memory 32 data.',
                'price' => 285000,
                'stock' => 120,
                'image_url' => '/storage/products/thermometer-infrared.jpg',
                'is_active' => true,
            ],
            [
                'vendor_id' => $vendor2->id,
                'category_id' => 1, // Alat Diagnostik
                'name' => 'Alat Cek Gula Darah GlucoDr AGM-4000',
                'sku' => 'GLUCO-AGM-4000',
                'description' => 'Alat tes gula darah digital dengan akurasi tinggi dan hasil cepat 5 detik. Sampel darah hanya 0.6μL. Termasuk 25 strip test, lancet device, dan carrying case. Memori 500 data.',
                'price' => 235000,
                'stock' => 65,
                'image_url' => '/storage/products/alat-cek-gula-darah.jpeg',
                'is_active' => true,
            ],
            [
                'vendor_id' => $vendor2->id,
                'category_id' => 2, // Alat Terapi
                'name' => 'Nebulizer Compressor OneMed',
                'sku' => 'NEB-ONEM-001',
                'description' => 'Nebulizer kompresor untuk terapi pernapasan asma, bronkitis, dan PPOK. Particle size <5μm untuk penetrasi optimal. Dilengkapi masker dewasa, anak, dan mouthpiece. Low noise operation.',
                'price' => 425000,
                'stock' => 55,
                'image_url' => '/storage/products/nebulizer.jpg',
                'is_active' => true,
            ],

            // VENDOR 3 - Jakarta Medical Store (Jakarta) - 4 products
            [
                'vendor_id' => $vendor3->id,
                'category_id' => 4, // Obat & Perawatan
                'name' => 'Betadine Solution 60ml',
                'sku' => 'BETA-SOL-60',
                'description' => 'Antiseptik povidone iodine 10% untuk luka, goresan, dan luka bakar ringan. Membunuh kuman, bakteri, jamur, dan virus. Tidak perih saat digunakan. Kemasan 60ml dengan aplikator.',
                'price' => 28500,
                'stock' => 180,
                'image_url' => '/storage/products/betadine.jpg',
                'is_active' => true,
            ],
            [
                'vendor_id' => $vendor3->id,
                'category_id' => 4, // Obat & Perawatan
                'name' => 'Hand Sanitizer Gel 500ml',
                'sku' => 'SANITIZER-500',
                'description' => 'Hand sanitizer gel dengan kandungan alkohol 70% dan vitamin E. Efektif membunuh 99.9% kuman tanpa air. Tidak membuat kulit kering. Aroma fresh. Kemasan 500ml dengan pump dispenser.',
                'price' => 35000,
                'stock' => 200,
                'image_url' => '/storage/products/hand-sanitizer.jpg',
                'is_active' => true,
            ],
            [
                'vendor_id' => $vendor3->id,
                'category_id' => 4, // Obat & Perawatan
                'name' => 'Hansaplast Plester Luka Strip 100 pcs',
                'sku' => 'HANS-STRIP-100',
                'description' => 'Plester luka strip berbagai ukuran untuk luka kecil sehari-hari. Anti air, breathable, dan hypoallergenic. Pad non-stick yang tidak lengket pada luka. Isi 100 strips dalam 4 ukuran.',
                'price' => 45000,
                'stock' => 150,
                'image_url' => '/storage/products/hansaplast.jpg',
                'is_active' => true,
            ],
            [
                'vendor_id' => $vendor3->id,
                'category_id' => 4, // Obat & Perawatan
                'name' => 'Sabun Antibakteri Dettol 250ml',
                'sku' => 'DETTOL-250',
                'description' => 'Sabun cair antibakteri dengan perlindungan terhadap 99.9% kuman. Formula pH balanced, mengandung moisturizer. Cocok untuk seluruh keluarga. Aroma fresh. Kemasan pump 250ml.',
                'price' => 32000,
                'stock' => 170,
                'image_url' => '/storage/products/sabun-antibakteri.png',
                'is_active' => true,
            ],

            // VENDOR 4 - Medan Health Equipment (Medan) - 3 products
            [
                'vendor_id' => $vendor4->id,
                'category_id' => 3, // Perlengkapan Medis
                'name' => 'Tandu Lipat Aluminium Emergency',
                'sku' => 'TANDU-ALU-001',
                'description' => 'Tandu lipat portable dari aluminium alloy ringan namun kuat. Kapasitas beban 159 kg. Dilengkapi safety belt dan pegangan ergonomis. Mudah dibersihkan. Untuk ambulance dan emergency rescue.',
                'price' => 3250000,
                'stock' => 15,
                'image_url' => '/storage/products/tandu-medis.jpg',
                'is_active' => true,
            ],
            [
                'vendor_id' => $vendor4->id,
                'category_id' => 3, // Perlengkapan Medis
                'name' => 'Kotak P3K Lengkap First Aid Kit',
                'sku' => 'P3K-KIT-001',
                'description' => 'Kotak P3K lengkap dengan isi 78 items termasuk perban, plester, gunting, pinset, alkohol swab, sarung tangan, dll. Box plastik tahan air dengan handle. Untuk rumah, kantor, mobil.',
                'price' => 185000,
                'stock' => 90,
                'image_url' => '/storage/products/kotak-p3k.jpg',
                'is_active' => true,
            ],
            [
                'vendor_id' => $vendor4->id,
                'category_id' => 3, // Perlengkapan Medis
                'name' => 'Tas P3K Portable Emergency Bag',
                'sku' => 'TAS-P3K-001',
                'description' => 'Tas P3K portable dengan compartment terorganisir. Material tahan air dan tahan lama. Strap adjustable untuk digantung atau dijinjing. Cocok untuk outdoor, traveling, event organizer. Isi dapat disesuaikan.',
                'price' => 125000,
                'stock' => 75,
                'image_url' => '/storage/products/tas-p3k.png',
                'is_active' => true,
            ],

            // VENDOR 5 - Semarang Medical Center (Semarang) - 2 products
            [
                'vendor_id' => $vendor5->id,
                'category_id' => 5, // Kesehatan Ibu & Anak
                'name' => 'Pompa ASI Elektrik Double Pump',
                'sku' => 'POMPA-ASI-E01',
                'description' => 'Pompa ASI elektrik double pump dengan teknologi massage cushion untuk kenyamanan maksimal. 9 level suction, rechargeable battery, LCD display. Termasuk 2 botol kaca, cooler bag, dan adaptor.',
                'price' => 1450000,
                'stock' => 35,
                'image_url' => '/storage/products/pompa-asi-elektrik.jpg',
                'is_active' => true,
            ],
            [
                'vendor_id' => $vendor5->id,
                'category_id' => 1, // Alat Diagnostik
                'name' => 'Timbangan Badan Digital Akurat',
                'sku' => 'SCALE-DIG-001',
                'description' => 'Timbangan badan digital dengan akurasi tinggi hingga 0.1 kg. Kapasitas maksimal 180 kg. Auto on/off, LCD besar, tempered glass platform anti slip. Baterai AAA included. Untuk home use dan klinik.',
                'price' => 165000,
                'stock' => 60,
                'image_url' => '/storage/products/timbangan-badan.jpg',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Create Guest Book Entries
        $this->command->info('Seeding guest book entries...');

        // Approved entries
        GuestBook::create([
            'user_id' => User::where('email', 'customer1@example.com')->first()->id,
            'name' => 'Customer 1',
            'email' => 'customer1@example.com',
            'message' => 'Sangat puas dengan pelayanan dan kualitas produk! Pengiriman cepat dan packaging rapi. Recommended!',
            'ip_address' => '127.0.0.1',
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $admin->id,
        ]);

        GuestBook::create([
            'name' => 'Dr. Ahmad Budiman',
            'email' => 'ahmad@hospital.com',
            'message' => 'Website sangat informatif dan mudah digunakan. Harga kompetitif dibanding toko lain. Terima kasih!',
            'ip_address' => '192.168.1.1',
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $admin->id,
        ]);

        GuestBook::create([
            'user_id' => User::where('email', 'customer2@example.com')->first()->id,
            'name' => 'Customer 2',
            'email' => 'customer2@example.com',
            'message' => 'Alat medis berkualitas dengan sertifikasi lengkap. Customer service responsif. Akan order lagi!',
            'ip_address' => '127.0.0.2',
            'is_approved' => true,
            'approved_at' => now()->subDays(1),
            'approved_by' => $admin->id,
        ]);

        // Pending entry (waiting approval)
        GuestBook::create([
            'name' => 'Visitor Guest',
            'email' => 'visitor@example.com',
            'message' => 'Tertarik dengan produk-produknya. Apakah ada garansi untuk alat diagnostik?',
            'ip_address' => '192.168.1.100',
            'is_approved' => false,
        ]);

        // Create Product Reviews
        $this->command->info('Seeding product reviews...');

        $products = Product::all();
        $customers = User::where('role', 'customer')->get();

        // Review untuk beberapa produk
        ProductReview::create([
            'product_id' => $products->first()->id,
            'user_id' => $customers->first()->id,
            'rating' => 5,
            'title' => 'Produk Sangat Bagus!',
            'review' => 'Kualitas produk excellent, sesuai deskripsi. Packaging aman dan pengiriman cepat. Sangat merekomendasikan produk ini untuk kebutuhan medis profesional.',
            'is_verified_purchase' => true,
            'is_approved' => true,
        ]);

        ProductReview::create([
            'product_id' => $products->first()->id,
            'user_id' => $customers->skip(1)->first()->id,
            'rating' => 4,
            'title' => 'Worth the price',
            'review' => 'Produk sesuai ekspektasi. Akurasi tinggi dan mudah digunakan. Hanya sedikit delay di pengiriman tapi overall bagus.',
            'is_verified_purchase' => false,
            'is_approved' => true,
        ]);

        ProductReview::create([
            'product_id' => $products->skip(1)->first()->id,
            'user_id' => $customers->skip(2)->first()->id,
            'rating' => 5,
            'title' => 'Highly Recommended',
            'review' => 'Sudah pakai produk ini selama 2 minggu, hasil akurat dan konsisten. Harga terjangkau untuk kualitas premium. Pasti beli lagi!',
            'is_verified_purchase' => true,
            'is_approved' => true,
        ]);

        ProductReview::create([
            'product_id' => $products->skip(2)->first()->id,
            'user_id' => $customers->skip(3)->first()->id,
            'rating' => 3,
            'title' => 'Good but could be better',
            'review' => 'Produk cukup bagus untuk harga segini. Build quality solid tapi manual instruction kurang jelas. Overall satisfied.',
            'is_verified_purchase' => true,
            'is_approved' => true,
        ]);

        // Create Orders
        $this->command->info('Seeding orders...');

        $allProducts = Product::all();
        $allCustomers = User::where('role', 'customer')->get();

        // Generate order numbers
        $generateOrderNumber = function () {
            return 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        };

        // Helper function to create order item
        $createOrderItem = function ($orderId, $productName, $quantity) use ($allProducts) {
            $product = $allProducts->where('name', $productName)->first();
            if (!$product) {
                throw new \Exception("Product not found: {$productName}");
            }

            return OrderItem::create([
                'order_id' => $orderId,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'quantity' => $quantity,
                'price' => $product->price,
                'subtotal' => $product->price * $quantity,
            ]);
        };

        // Order 1 - Delivered (Jakarta customer, COD)
        $order1 = Order::create([
            'user_id' => $allCustomers->where('city', 'Jakarta')->first()->id,
            'order_number' => $generateOrderNumber(),
            'status' => 'delivered',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'subtotal' => 460000,
            'shipping_fee' => 15000,
            'total' => 475000,
            'shipping_name' => $allCustomers->where('city', 'Jakarta')->first()->name,
            'shipping_email' => $allCustomers->where('city', 'Jakarta')->first()->email,
            'shipping_phone' => $allCustomers->where('city', 'Jakarta')->first()->phone,
            'shipping_address' => $allCustomers->where('city', 'Jakarta')->first()->address,
            'shipping_city' => 'Jakarta',
            'shipping_province' => 'DKI Jakarta',
            'shipping_postal_code' => '10220',
            'notes' => 'Mohon kirim secepatnya',
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(3),
        ]);

        $createOrderItem($order1->id, 'Nebulizer Compressor OneMed', 1);
        $createOrderItem($order1->id, 'Hand Sanitizer Gel 500ml', 1);

        // Order 2 - Processing (Bandung customer, Prepaid BCA)
        $order2 = Order::create([
            'user_id' => $allCustomers->where('city', 'Bandung')->first()->id,
            'order_number' => $generateOrderNumber(),
            'status' => 'processing',
            'payment_method' => 'prepaid',
            'bank_type' => 'BCA',
            'payment_status' => 'paid',
            'subtotal' => 375000,
            'shipping_fee' => 12000,
            'total' => 387000,
            'shipping_name' => $allCustomers->where('city', 'Bandung')->first()->name,
            'shipping_email' => $allCustomers->where('city', 'Bandung')->first()->email,
            'shipping_phone' => $allCustomers->where('city', 'Bandung')->first()->phone,
            'shipping_address' => $allCustomers->where('city', 'Bandung')->first()->address,
            'shipping_city' => 'Bandung',
            'shipping_province' => 'Jawa Barat',
            'shipping_postal_code' => '40111',
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(4),
        ]);

        $createOrderItem($order2->id, 'Tensimeter Digital Omron HEM-7120', 1);

        // Order 3 - Shipped (Surabaya customer, PayPal)
        $order3 = Order::create([
            'user_id' => $allCustomers->where('city', 'Surabaya')->first()->id,
            'order_number' => $generateOrderNumber(),
            'status' => 'shipped',
            'payment_method' => 'paypal',
            'payment_status' => 'paid',
            'subtotal' => 635000,
            'shipping_fee' => 18000,
            'total' => 653000,
            'shipping_name' => $allCustomers->where('city', 'Surabaya')->first()->name,
            'shipping_email' => $allCustomers->where('city', 'Surabaya')->first()->email,
            'shipping_phone' => $allCustomers->where('city', 'Surabaya')->first()->phone,
            'shipping_address' => $allCustomers->where('city', 'Surabaya')->first()->address,
            'shipping_city' => 'Surabaya',
            'shipping_province' => 'Jawa Timur',
            'shipping_postal_code' => '60275',
            'created_at' => now()->subDays(7),
            'updated_at' => now()->subDays(2),
        ]);

        $createOrderItem($order3->id, 'Thermometer Infrared Non-Contact', 2);
        $createOrderItem($order3->id, 'Alat Cek Gula Darah GlucoDr AGM-4000', 1);
        $createOrderItem($order3->id, 'Betadine Solution 60ml', 2);

        // Order 4 - Pending (Medan customer, Prepaid Mandiri)
        $order4 = Order::create([
            'user_id' => $allCustomers->where('city', 'Medan')->first()->id,
            'order_number' => $generateOrderNumber(),
            'status' => 'pending',
            'payment_method' => 'prepaid',
            'bank_type' => 'Mandiri',
            'payment_status' => 'pending',
            'subtotal' => 2850000,
            'shipping_fee' => 0, // Free shipping for expensive item
            'total' => 2850000,
            'shipping_name' => $allCustomers->where('city', 'Medan')->first()->name,
            'shipping_email' => $allCustomers->where('city', 'Medan')->first()->email,
            'shipping_phone' => $allCustomers->where('city', 'Medan')->first()->phone,
            'shipping_address' => $allCustomers->where('city', 'Medan')->first()->address,
            'shipping_city' => 'Medan',
            'shipping_province' => 'Sumatera Utara',
            'shipping_postal_code' => '20112',
            'notes' => 'Harap konfirmasi setelah pembayaran diterima',
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        $createOrderItem($order4->id, 'Stetoskop Littmann Classic III', 1);

        // Order 5 - Cancelled (Semarang customer, COD) - Stock should be restored
        $order5 = Order::create([
            'user_id' => $allCustomers->where('city', 'Semarang')->first()->id,
            'order_number' => $generateOrderNumber(),
            'status' => 'cancelled',
            'payment_method' => 'cod',
            'payment_status' => 'failed',
            'subtotal' => 360000,
            'shipping_fee' => 15000,
            'total' => 375000,
            'shipping_name' => $allCustomers->where('city', 'Semarang')->first()->name,
            'shipping_email' => $allCustomers->where('city', 'Semarang')->first()->email,
            'shipping_phone' => $allCustomers->where('city', 'Semarang')->first()->phone,
            'shipping_address' => $allCustomers->where('city', 'Semarang')->first()->address,
            'shipping_city' => 'Semarang',
            'shipping_province' => 'Jawa Tengah',
            'shipping_postal_code' => '50241',
            'notes' => 'Dibatalkan oleh admin: Alamat tidak lengkap dan customer tidak merespon',
            'created_at' => now()->subDays(8),
            'updated_at' => now()->subDays(6),
        ]);

        $createOrderItem($order5->id, 'Oximeter Pulse Digital Fingertip', 1);
        $createOrderItem($order5->id, 'Timbangan Badan Digital Akurat', 1);
        $createOrderItem($order5->id, 'Sabun Antibakteri Dettol 250ml', 1);

        // Stock should remain as is for cancelled order (already restored when cancelled)

        // Order 6 - Delivered (Jakarta customer, Prepaid BNI) - Big Order
        $order6 = Order::create([
            'user_id' => $allCustomers->where('city', 'Jakarta')->first()->id,
            'order_number' => $generateOrderNumber(),
            'status' => 'delivered',
            'payment_method' => 'prepaid',
            'bank_type' => 'BNI',
            'payment_status' => 'paid',
            'subtotal' => 3690000,
            'shipping_fee' => 0, // Free shipping for big order
            'total' => 3690000,
            'shipping_name' => $allCustomers->where('city', 'Jakarta')->first()->name,
            'shipping_email' => $allCustomers->where('city', 'Jakarta')->first()->email,
            'shipping_phone' => $allCustomers->where('city', 'Jakarta')->first()->phone,
            'shipping_address' => $allCustomers->where('city', 'Jakarta')->first()->address,
            'shipping_city' => 'Jakarta',
            'shipping_province' => 'DKI Jakarta',
            'shipping_postal_code' => '10220',
            'notes' => 'Pembelian untuk klinik, mohon pastikan packaging aman',
            'created_at' => now()->subDays(15),
            'updated_at' => now()->subDays(10),
        ]);

        $createOrderItem($order6->id, 'Tandu Lipat Aluminium Emergency', 1);
        $createOrderItem($order6->id, 'Kotak P3K Lengkap First Aid Kit', 2);
        $createOrderItem($order6->id, 'Tas P3K Portable Emergency Bag', 2);

        // Order 7 - Processing (Bandung customer, Prepaid Visa)
        $order7 = Order::create([
            'user_id' => $allCustomers->where('city', 'Bandung')->first()->id,
            'order_number' => $generateOrderNumber(),
            'status' => 'processing',
            'payment_method' => 'prepaid',
            'bank_type' => 'Visa',
            'payment_status' => 'paid',
            'subtotal' => 1450000,
            'shipping_fee' => 0, // Free shipping
            'total' => 1450000,
            'shipping_name' => $allCustomers->where('city', 'Bandung')->first()->name,
            'shipping_email' => $allCustomers->where('city', 'Bandung')->first()->email,
            'shipping_phone' => $allCustomers->where('city', 'Bandung')->first()->phone,
            'shipping_address' => $allCustomers->where('city', 'Bandung')->first()->address,
            'shipping_city' => 'Bandung',
            'shipping_province' => 'Jawa Barat',
            'shipping_postal_code' => '40111',
            'notes' => 'Untuk hadiah, mohon packaging premium',
            'created_at' => now()->subDays(3),
            'updated_at' => now()->subDays(2),
        ]);

        $createOrderItem($order7->id, 'Pompa ASI Elektrik Double Pump', 1);

        // Order 8 - Delivered (Surabaya customer, COD) - Multiple items
        $order8 = Order::create([
            'user_id' => $allCustomers->where('city', 'Surabaya')->first()->id,
            'order_number' => $generateOrderNumber(),
            'status' => 'delivered',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'subtotal' => 339000,
            'shipping_fee' => 15000,
            'total' => 354000,
            'shipping_name' => $allCustomers->where('city', 'Surabaya')->first()->name,
            'shipping_email' => $allCustomers->where('city', 'Surabaya')->first()->email,
            'shipping_phone' => $allCustomers->where('city', 'Surabaya')->first()->phone,
            'shipping_address' => $allCustomers->where('city', 'Surabaya')->first()->address,
            'shipping_city' => 'Surabaya',
            'shipping_province' => 'Jawa Timur',
            'shipping_postal_code' => '60275',
            'created_at' => now()->subDays(12),
            'updated_at' => now()->subDays(7),
        ]);

        $createOrderItem($order8->id, 'Masker Medis 3 Ply Surgical (Box 50 pcs)', 5);
        $createOrderItem($order8->id, 'Hansaplast Plester Luka Strip 100 pcs', 2);
        $createOrderItem($order8->id, 'Betadine Solution 60ml', 3);

        // Reduce stock for delivered/processing/shipped orders
        $allProducts->where('name', 'Nebulizer Compressor OneMed')->first()->decrement('stock', 1);
        $allProducts->where('name', 'Hand Sanitizer Gel 500ml')->first()->decrement('stock', 1);
        $allProducts->where('name', 'Tensimeter Digital Omron HEM-7120')->first()->decrement('stock', 1);
        $allProducts->where('name', 'Thermometer Infrared Non-Contact')->first()->decrement('stock', 2);
        $allProducts->where('name', 'Alat Cek Gula Darah GlucoDr AGM-4000')->first()->decrement('stock', 1);
        $allProducts->where('name', 'Betadine Solution 60ml')->first()->decrement('stock', 5);
        $allProducts->where('name', 'Tandu Lipat Aluminium Emergency')->first()->decrement('stock', 1);
        $allProducts->where('name', 'Kotak P3K Lengkap First Aid Kit')->first()->decrement('stock', 2);
        $allProducts->where('name', 'Tas P3K Portable Emergency Bag')->first()->decrement('stock', 2);
        $allProducts->where('name', 'Pompa ASI Elektrik Double Pump')->first()->decrement('stock', 1);
        $allProducts->where('name', 'Masker Medis 3 Ply Surgical (Box 50 pcs)')->first()->decrement('stock', 5);
        $allProducts->where('name', 'Hansaplast Plester Luka Strip 100 pcs')->first()->decrement('stock', 2);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@medic-ecommerce.com / password');
        $this->command->info('Vendors:');
        $this->command->info('  - vendor1@medic-ecommerce.com (Alat Medis Sejahtera - Bandung) / password');
        $this->command->info('  - vendor2@medic-ecommerce.com (Prima Tech Solution - Surabaya) / password');
        $this->command->info('  - vendor3@medic-ecommerce.com (Jakarta Medical Store - Jakarta) / password');
        $this->command->info('  - vendor4@medic-ecommerce.com (Medan Health Equipment - Medan) / password');
        $this->command->info('  - vendor5@medic-ecommerce.com (Semarang Medical Center - Semarang) / password');
        $this->command->info('Customers: customer1@example.com to customer5@example.com / password');
        $this->command->info('Products: 16 products from 5 vendors (based on actual photos in storage/app/public/products)');
        $this->command->info('Categories: 5 categories (Alat Diagnostik, Alat Terapi, Perlengkapan Medis, Obat & Perawatan, Kesehatan Ibu & Anak)');
        $this->command->info('Orders: 8 orders with various statuses and payment methods');
        $this->command->info('  - 3 Delivered orders');
        $this->command->info('  - 2 Processing orders');
        $this->command->info('  - 1 Shipped order');
        $this->command->info('  - 1 Pending order');
        $this->command->info('  - 1 Cancelled order');
        $this->command->info('Guest Book: 3 approved, 1 pending');
        $this->command->info('Reviews: 4 reviews for demo products');
    }
}
