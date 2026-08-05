<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel untuk menghindari duplikasi saat seeding ulang
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::truncate();
        Product::truncate();
        DB::table('product_variants')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Buat Kategori
        $catProduk = Category::create(['name' => 'Produk', 'slug' => 'produk']);
        $catHampers = Category::create(['name' => 'Hampers', 'slug' => 'hampers']);
        $catTumpeng = Category::create(['name' => 'Tumpeng', 'slug' => 'tumpeng']);

        // 2. Buat Produk untuk Kategori 'Produk'
        $kueIjo = $catProduk->products()->create([
            'name' => 'Kue Ijo',
            'description' => 'Kue Ijo terbuat dari tepung beras yang telah diayak lalu dicampur dengan campuran santan kelapa fresh dan air pandan asli. Kue ini memiliki tekstur yang kenyal dan lembut bersamaan ketika dikunyah didalam mulut. Dibalut taburan dengan kelapa parut segar menambah cita rasa gurih dan manis yang menjadikan kue Ijo cocok dinikmati pada suasana apapun.',
            'image_path' => 'assets/homepage/product/kue-ijo.jpg',
            'tag' => 'Ala Carte',
            'is_active' => true,
        ]);
        $kueIjo->variants()->createMany([
            ['name' => 'Isi 3 Kemasan Mika', 'price' => 9000],
            ['name' => 'Isi 5 Kemasan Mika', 'price' => 15000],
            ['name' => 'Isi 12 Kemasan Thinwall', 'price' => 36000],
        ]);

        $lumpurSurga = $catProduk->products()->create([
            'name' => 'Kue Lumpur Surga',
            'description' => 'Kue yang memiliki dua lapisan yaitu lapisan bawah bewarna hijau yang dihasilkan dari air pandan asli dan lapisan atas mirip dengan vla yang terbuat dari santan. Kue yang memiliki tekstur lembut dan lumer dimulut sangat nikmat jika disantap dalam keadaan dingin.',
            'image_path' => 'assets/homepage/product/kue-lumpur-surga.jpg',
            'tag' => 'Ala Carte',
            'is_active' => true,
        ]);
        $lumpurSurga->variants()->create(['name' => 'Per Cup', 'price' => 6000]);

        $ongol = $catProduk->products()->create([
            'name' => 'Kue Ongol Ongol',
            'description' => 'Kue Ongol kami terbuat dari tepung tapioka yang ditambahkan gula merah jawa dan sedikit tambahan air pandan asli membuat warna kue ini bewarna coklat cantik. Kue yang memiliki tekstur kenyal dan legit dan terasa manis dimulut.',
            'image_path' => 'assets/homepage/product/kue-ongol.jpg',
            'tag' => 'Ala Carte',
            'is_active' => true,
        ]);
        $ongol->variants()->createMany([
            ['name' => 'Isi 10 Kemasan Mika', 'price' => 10000],
            ['name' => 'Isi 30 Kemasan Thinwall', 'price' => 35000],
        ]);

        $pulutSrikaya = $catProduk->products()->create([
            'name' => 'Kue Pulut Srikaya',
            'description' => 'Kue Pulut yang dibuat dari beras ketan utuh yang akan menciptakan tekstur punel dan sedikit legit. Perpaduan warna Putih dari beras ketan dan Ungu alami dari bunga lawing (butterfly pea tea) menambah keindahan visual pada kue ini. Kue Pulut ini dilengkapi dengan saus srikaya yang membuat perpaduan antara manis dan sedikit gurih dari saus dan gurih dari kue pulut itu sendiri menjadikan perpaduan rasa yang lengkap dan lezat.',
            'image_path' => 'assets/homepage/product/kue-pulut.jpg',
            'tag' => 'Ala Carte',
            'is_active' => true,
        ]);
        $pulutSrikaya->variants()->createMany([
            ['name' => 'Isi 5 Kemasan Mika', 'price' => 17500],
            ['name' => 'Isi 10 Kemasan Thinwall', 'price' => 35500],
        ]);

        $ubiNanas = $catProduk->products()->create([
            'name' => 'Kue Ubi Nanas',
            'description' => 'Kue yang terbuat dari perpaduan ubi singkong yang memiliki rasa netral dan tekstur padat dan buah nanas yang memiliki rasa manis, segar dan masam dipadukan. Kue ini memiliki tekrstur sedikit chewy dan padat hampir seperti kue talam pada umumnya. Kue ini memiliki rasa manis dan sedikit rasa segar dari buah nanas dan rasa gurih dari bahan bahan kue. Terbuat dari bahan alami sehingga aman dikonsumsi dan sehat. Kue ubi nanas berisi 4 potong kue.',
            'image_path' => 'assets/homepage/product/kue-ubi-nanas.jpeg',
            'tag' => 'Ala Carte',
            'is_active' => true,
        ]);
        $ubiNanas->variants()->create(['name' => 'Per Cup', 'price' => 10000]);

        $selaiSrikaya = $catProduk->products()->create([
            'name' => 'Kue Selai Srikaya',
            'description' => 'Selai yang terbuat dari perpaduan santan kelapa, telur, air pandan dan gula merah jawa menghasilkan rasa yang gurih dan manis pada selai ini. Selai yang memiliki tekstur kental dan memiliki warna oren pekat dihasilkan dari telur dan gula merah. Selai ini cocok dipadukan dengan kue yang memiliki cita rasa netral maupun gurih untuk menambahkan cita rasa manis pada rasa kue tersebut. Kemasan Botol 160ml yang praktis dan mudah dibawa kemana saja.',
            'image_path' => 'assets/homepage/product/selai-srikaya.jpg',
            'tag' => 'Ala Carte',
            'is_active' => true,
        ]);
        $selaiSrikaya->variants()->create(['name' => 'Per Cup', 'price' => 60000]);

        $mixMini = $catProduk->products()->create([
            'name' => 'Kue Mix Mini',
            'description' => 'Kue MIX mini ( Kemasan Mika) Berisi : Kue ijo 3 pcs, Kue Ongol-ongol 4 pcs dan Kue Pulut Srikaya 2 pcs Kue ini cocok untuk dijadikan sebagai oleh-oleh atau sebagai cemilan ringan.',
            'image_path' => 'assets/homepage/product/kue-mix-mini.jpeg',
            'tag' => 'Ala Carte',
            'is_active' => true,
        ]);
        $mixMini->variants()->create(['name' => 'Per Cup', 'price' => 10000]);

        $mix = $catProduk->products()->create([
            'name' => 'Kue Mix (Kue Ijo & Kue Pulut)',
            'description' => 'Kue MIX (Kemasan Thinwall) Berisi : Kue ijo 4 pcs, Kue Pulut Srikaya 5 pcs.',
            'image_path' => 'assets/homepage/product/kue-mix-kueijo.jpeg',
            'tag' => 'Ala Carte',
            'is_active' => true,
        ]);
        $mix->variants()->create(['name' => 'Per Cup', 'price' => 35000]);

        // 3. Buat Produk untuk Kategori 'Hampers'
        $hampersA = $catHampers->products()->create([
            'name' => 'Hampers A (Anggun)',
            'description' => 'Berisi: Kue Ijo (12 pcs), Kue Ongol-ongol (30 pcs), Kue Pulut Srikaya (10 pcs), Lumpur Surga (4 cup @100ml).',
            'image_path' => 'assets/homepage/product/hampers-a.jpg',
            'tag' => 'Hampers',
            'is_active' => true,
        ]);
        $hampersA->variants()->create(['name' => 'Paket A', 'price' => 160000]);

        $hampersB = $catHampers->products()->create([
            'name' => 'Hampers B (Bagus)',
            'description' => 'Berisi: Kue Ijo (12 pcs), Kue Ongol-ongol (30 pcs), Kue Pulut Srikaya (10 pcs).',
            'image_path' => 'assets/homepage/product/hampers-b.jpg',
            'tag' => 'Hampers',
            'is_active' => true,
        ]);
        $hampersB->variants()->create(['name' => 'Paket B', 'price' => 115000]);

        $hampersC = $catHampers->products()->create([
            'name' => 'Hampers C (Cantik)',
            'description' => 'Belum ada data.',
            'image_path' => 'assets/homepage/product/hampers-c.png',
            'tag' => 'Hampers',
            'is_active' => true,
        ]);
        $hampersC->variants()->create(['name' => 'Paket A', 'price' => 0]);

        // 4. Buat Produk untuk Kategori 'Tumpeng'
        $tumpengMini = $catTumpeng->products()->create([
            'name' => 'Tumpeng Mini Mix',
            'description' => 'Berisi: Kue Ijo (25 pcs), Kue Pulut (20 pcs), Kue Ongol-ongol (50 pcs), Lumpur Surga (6 cup). Cocok untuk syukuran, ulang tahun, atau acara spesial lainnya.',
            'image_path' => 'assets/homepage/product/tumpeng-mini.jpg',
            'tag' => 'Tumpeng',
            'is_active' => true,
        ]);
        $tumpengMini->variants()->create(['name' => 'Paket Mini', 'price' => 250000]);

        $tumpengBesar = $catTumpeng->products()->create([
            'name' => 'Tumpeng Besar Mix',
            'description' => 'Berisi: Kue Ijo (50 pcs), Kue Pulut (40 pcs), Kue Ongol-ongol (100 pcs), Lumpur Surga (12 cup). Tumpeng besar mix untuk acara keluarga, kantor, arisan, atau perayaan penting lainnya.',
            'image_path' => 'assets/homepage/product/tumpeng-besar.jpg',
            'tag' => 'Tumpeng',
            'is_active' => true,
        ]);
        $tumpengBesar->variants()->create(['name' => 'Paket Besar', 'price' => 500000]);
    }
}
