<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\Table;
use App\Models\TableSession;
use App\Models\SessionCustomer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemAddon;
use App\Models\Expense;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Users
        $admin = User::create(['name' => 'Administrator', 'email' => 'admin@caffe.com', 'password' => bcrypt('password'), 'role' => 'admin']);
        User::create(['name' => 'Kasir Depan', 'email' => 'kasir@caffe.com', 'password' => bcrypt('password'), 'role' => 'cashier']);
        User::create(['name' => 'Dapur Utama', 'email' => 'dapur@caffe.com', 'password' => bcrypt('password'), 'role' => 'kitchen']);

        // 2. Data Kategori
        $categories = ['Kopi', 'Non-Kopi', 'Makanan Berat', 'Snack', 'Dessert'];
        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat] = Category::create(['name' => $cat]);
        }

        // 3. Data Produk (30+ Menu Realistis)
        $products = [
            // Kopi
            ['cat' => 'Kopi', 'name' => 'Espresso', 'price' => 15000],
            ['cat' => 'Kopi', 'name' => 'Americano (Hot/Ice)', 'price' => 18000],
            ['cat' => 'Kopi', 'name' => 'Cafe Latte', 'price' => 22000],
            ['cat' => 'Kopi', 'name' => 'Cappuccino', 'price' => 22000],
            ['cat' => 'Kopi', 'name' => 'Kopi Susu Gula Aren', 'price' => 20000],
            ['cat' => 'Kopi', 'name' => 'Caramel Macchiato', 'price' => 26000],
            ['cat' => 'Kopi', 'name' => 'Hazelnut Latte', 'price' => 25000],
            ['cat' => 'Kopi', 'name' => 'Vanilla Latte', 'price' => 25000],
            ['cat' => 'Kopi', 'name' => 'Mochaccino', 'price' => 24000],
            ['cat' => 'Kopi', 'name' => 'Affogato', 'price' => 23000],

            // Non-Kopi
            ['cat' => 'Non-Kopi', 'name' => 'Matcha Latte', 'price' => 24000],
            ['cat' => 'Non-Kopi', 'name' => 'Taro Latte', 'price' => 23000],
            ['cat' => 'Non-Kopi', 'name' => 'Red Velvet Latte', 'price' => 24000],
            ['cat' => 'Non-Kopi', 'name' => 'Signature Chocolate', 'price' => 25000],
            ['cat' => 'Non-Kopi', 'name' => 'Lemon Tea', 'price' => 15000],
            ['cat' => 'Non-Kopi', 'name' => 'Lychee Tea', 'price' => 18000],
            ['cat' => 'Non-Kopi', 'name' => 'Peach Tea', 'price' => 18000],
            ['cat' => 'Non-Kopi', 'name' => 'Thai Tea', 'price' => 16000],
            ['cat' => 'Non-Kopi', 'name' => 'Mineral Water', 'price' => 8000],

            // Makanan Berat
            ['cat' => 'Makanan Berat', 'name' => 'Nasi Goreng Spesial', 'price' => 28000],
            ['cat' => 'Makanan Berat', 'name' => 'Mie Goreng Jawa', 'price' => 25000],
            ['cat' => 'Makanan Berat', 'name' => 'Spaghetti Carbonara', 'price' => 32000],
            ['cat' => 'Makanan Berat', 'name' => 'Spaghetti Bolognese', 'price' => 30000],
            ['cat' => 'Makanan Berat', 'name' => 'Chicken Katsu Curry', 'price' => 35000],
            ['cat' => 'Makanan Berat', 'name' => 'Rice Bowl Beef Yakiniku', 'price' => 38000],
            ['cat' => 'Makanan Berat', 'name' => 'Nasi Gila Caffe', 'price' => 27000],

            // Snack
            ['cat' => 'Snack', 'name' => 'French Fries', 'price' => 15000],
            ['cat' => 'Snack', 'name' => 'Potato Wedges', 'price' => 18000],
            ['cat' => 'Snack', 'name' => 'Mix Platter (Sosis, Nugget, Fries)', 'price' => 30000],
            ['cat' => 'Snack', 'name' => 'Dimsum Mentai (4 pcs)', 'price' => 20000],
            ['cat' => 'Snack', 'name' => 'Pisang Goreng Keju Susu', 'price' => 18000],
            ['cat' => 'Snack', 'name' => 'Jamur Crispy', 'price' => 15000],

            // Dessert
            ['cat' => 'Dessert', 'name' => 'Butter Croissant', 'price' => 18000],
            ['cat' => 'Dessert', 'name' => 'Almond Croissant', 'price' => 22000],
            ['cat' => 'Dessert', 'name' => 'Fudge Brownies Ice Cream', 'price' => 25000],
            ['cat' => 'Dessert', 'name' => 'New York Cheesecake', 'price' => 30000],
            ['cat' => 'Dessert', 'name' => 'Waffle Ice Cream Maple', 'price' => 24000],
        ];

        // Insert semua produk ke database dengan HPP (Modal simulasi 40% dari harga jual)
        $productModels = [];
        foreach ($products as $p) {
            $productModels[] = Product::create([
                'category_id' => $categoryModels[$p['cat']]->id,
                'name' => $p['name'],
                'price' => $p['price'],
                'cogs' => $p['price'] * 0.4, // MODAL: 40% dari harga
                'description' => 'Menu lezat pilihan dari Caffe kami.',
            ]);
        }

        // 4. Data Add-ons (Opsi Tambahan untuk Nasi Goreng Spesial - Index 19)
        $nasiGoreng = $productModels[19];
        $addonPedas = ProductAddon::create(['product_id' => $nasiGoreng->id, 'category' => 'Level Pedas', 'name' => 'Pedas Mampus', 'additional_price' => 0, 'additional_cogs' => 0]);
        $addonTelur = ProductAddon::create(['product_id' => $nasiGoreng->id, 'category' => 'Tambahan', 'name' => 'Telur Ceplok', 'additional_price' => 5000, 'additional_cogs' => 2000]);

        // 5. Data Meja (15 Meja)
        $tables = [];
        for ($i = 1; $i <= 15; $i++) {
            $tableNumber = 'Meja ' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $tables[] = Table::create([
                'table_number' => $tableNumber,
                'qr_token' => Str::random(12),
                'status' => ($i == 1) ? 'occupied' : 'available',
            ]);
        }

        // ====================================================================
        // 6. SIMULASI TRANSAKSI AKTIF (Meja 01 - Belum Bayar)
        // ====================================================================
        $activeSession = TableSession::create([
            'table_id' => $tables[0]->id,
            'status' => 'active'
        ]);

        $customerA = SessionCustomer::create(['table_session_id' => $activeSession->id, 'display_name' => 'Budi', 'device_identifier' => 'device-budi-123', 'is_host' => true]);
        $customerB = SessionCustomer::create(['table_session_id' => $activeSession->id, 'display_name' => 'Siti', 'device_identifier' => 'device-siti-456', 'is_host' => false]);

        $order = Order::create([
            'table_session_id' => $activeSession->id,
            'order_number' => 'ORD-' . date('Ymd') . '-001',
            'status' => 'processing',
            'payment_status' => 'unpaid',
            'total_price' => 20000 + 28000 + 5000 + 24000 + 32000
        ]);

        OrderItem::create(['order_id' => $order->id, 'session_customer_id' => $customerA->id, 'product_id' => $productModels[4]->id, 'price_at_order' => 20000, 'cogs_at_order' => $productModels[4]->cogs, 'qty' => 1, 'status' => 'ready_to_serve']);

        $orderNasiGoreng = OrderItem::create(['order_id' => $order->id, 'session_customer_id' => $customerA->id, 'product_id' => $nasiGoreng->id, 'price_at_order' => 28000, 'cogs_at_order' => $nasiGoreng->cogs, 'qty' => 1, 'notes' => 'Tolong pisah kuahnya', 'status' => 'cooking']);
        OrderItemAddon::create(['order_item_id' => $orderNasiGoreng->id, 'product_addon_id' => $addonTelur->id, 'addon_name' => 'Tambahan: Telur Ceplok', 'addon_price' => 5000, 'addon_cogs' => 2000]);

        OrderItem::create(['order_id' => $order->id, 'session_customer_id' => $customerB->id, 'product_id' => $productModels[10]->id, 'price_at_order' => 24000, 'cogs_at_order' => $productModels[10]->cogs, 'qty' => 1, 'status' => 'pending']);
        OrderItem::create(['order_id' => $order->id, 'session_customer_id' => $customerB->id, 'product_id' => $productModels[21]->id, 'price_at_order' => 32000, 'cogs_at_order' => $productModels[21]->cogs, 'qty' => 1, 'status' => 'pending']);

        // ====================================================================
        // 7. SIMULASI TRANSAKSI HISTORIS (LUNAS) UNTUK MENGISI DASBOR
        // ====================================================================
        // Kita membuat 20 pesanan lunas acak dalam 7 hari terakhir
        for ($j = 1; $j <= 20; $j++) {
            $randomDaysAgo = rand(0, 7); // Acak hari (0 = hari ini, 7 = seminggu lalu)
            $simulatedDate = Carbon::now()->subDays($randomDaysAgo);

            // Meja Acak selain Meja 01
            $randomTable = $tables[rand(1, 14)];

            $completedSession = TableSession::create([
                'table_id' => $randomTable->id,
                'status' => 'completed',
                'created_at' => $simulatedDate,
                'updated_at' => $simulatedDate,
            ]);

            $pastCustomer = SessionCustomer::create([
                'table_session_id' => $completedSession->id,
                'display_name' => 'Tamu ' . $j,
                'device_identifier' => 'device-tamu-' . $j,
                'is_host' => true,
                'created_at' => $simulatedDate,
                'updated_at' => $simulatedDate,
            ]);

            $orderPrice = 0;
            $pastOrder = Order::create([
                'table_session_id' => $completedSession->id,
                'order_number' => 'ORD-' . $simulatedDate->format('Ymd') . '-P' . str_pad($j, 3, '0', STR_PAD_LEFT),
                'status' => 'completed',
                'payment_status' => 'paid',
                'total_price' => 0, // Akan diupdate di bawah
                'created_at' => $simulatedDate,
                'updated_at' => $simulatedDate,
            ]);

            // Pilih 2 hingga 4 menu acak untuk pesanan ini
            $randomProducts = collect($productModels)->random(rand(2, 4));

            foreach ($randomProducts as $rp) {
                $qty = rand(1, 3); // Beli 1 sampai 3 porsi per menu
                $orderPrice += ($rp->price * $qty);

                OrderItem::create([
                    'order_id' => $pastOrder->id,
                    'session_customer_id' => $pastCustomer->id,
                    'product_id' => $rp->id,
                    'price_at_order' => $rp->price,
                    'cogs_at_order' => $rp->cogs,
                    'qty' => $qty,
                    'status' => 'served',
                    'created_at' => $simulatedDate,
                    'updated_at' => $simulatedDate,
                ]);
            }

            $pastOrder->update(['total_price' => $orderPrice]);
        }

        // ====================================================================
        // 8. SIMULASI PENGELUARAN (Expenses) UNTUK TES LAPORAN
        // ====================================================================
        Expense::create(['expense_date' => Carbon::now()->toDateString(), 'category' => 'Operasional', 'description' => 'Token Listrik 1 Bulan', 'amount' => 500000, 'user_id' => $admin->id]);
        Expense::create(['expense_date' => Carbon::now()->subDays(2)->toDateString(), 'category' => 'Bahan Baku', 'description' => 'Beli Biji Kopi Arabica 5kg di Pasar', 'amount' => 750000, 'user_id' => $admin->id]);
    }
}