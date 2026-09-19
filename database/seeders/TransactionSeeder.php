<?php

namespace Database\Seeders;

use App\Enums\PaymentStatus;
use App\Enums\ShiftStatus;
use App\Enums\TransactionStatus;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Outlet;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlet = Outlet::first();
        $cashier = User::where('email', 'kasir@posputri.test')->first();
        $device = Device::where('outlet_id', $outlet?->id)->first();

        if (! $outlet || ! $cashier) {
            return;
        }

        $todayShift = Shift::where('status', ShiftStatus::Open)->latest('opened_at')->first();
        $yesterdayShift = Shift::where('status', ShiftStatus::Closed)->latest('opened_at')->first();

        $pmCash = PaymentMethod::where('code', 'cash')->first();
        $pmDebit = PaymentMethod::where('code', 'debit')->first();
        $pmQris = PaymentMethod::where('code', 'qris')->first();

        $customer1 = Customer::where('phone', '081234567890')->first();
        $customer2 = Customer::where('phone', '081298765432')->first();
        $customer3 = Customer::where('phone', '081311223344')->first();

        $prods = Product::all()->keyBy('name');
        $defaultProd = Product::first();

        // Helper to find product safely
        $getProd = function (string $name) use ($prods, $defaultProd) {
            return $prods->get($name) ?? $defaultProd;
        };

        $transactionsData = [
            // 1. Today Completed (Cash)
            [
                'number' => 'TRX-'.Carbon::today()->format('Ymd').'-0001',
                'shift_id' => $todayShift?->id,
                'customer_id' => $customer1?->id,
                'status' => TransactionStatus::Completed,
                'time' => Carbon::today()->setHour(9)->setMinute(15),
                'items' => [
                    ['product' => $getProd('Nasi Goreng Spesial'), 'qty' => 2, 'price' => 25000],
                    ['product' => $getProd('Kopi Susu Gula Aren'), 'qty' => 2, 'price' => 18000],
                ],
                'payment' => [
                    'method' => $pmCash,
                    'code' => 'cash',
                    'paid_cash' => 100000,
                    'ref' => 'CASH-POS1-001',
                ],
            ],
            // 2. Today Completed (QRIS)
            [
                'number' => 'TRX-'.Carbon::today()->format('Ymd').'-0002',
                'shift_id' => $todayShift?->id,
                'customer_id' => $customer2?->id,
                'status' => TransactionStatus::Completed,
                'time' => Carbon::today()->setHour(10)->setMinute(30),
                'items' => [
                    ['product' => $getProd('Ayam Geprek Sambal Bawang'), 'qty' => 1, 'price' => 22000],
                    ['product' => $getProd('Americano Iced'), 'qty' => 1, 'price' => 15000],
                ],
                'payment' => [
                    'method' => $pmQris,
                    'code' => 'qris',
                    'paid_cash' => null,
                    'ref' => 'QRIS-ID-892110',
                ],
            ],
            // 3. Today Completed (Debit)
            [
                'number' => 'TRX-'.Carbon::today()->format('Ymd').'-0003',
                'shift_id' => $todayShift?->id,
                'customer_id' => $customer3?->id,
                'status' => TransactionStatus::Completed,
                'time' => Carbon::today()->setHour(11)->setMinute(45),
                'items' => [
                    ['product' => $getProd('Kopi Susu Gula Aren'), 'qty' => 3, 'price' => 18000],
                    ['product' => $getProd('Pisang Goreng Keju'), 'qty' => 2, 'price' => 15000],
                ],
                'payment' => [
                    'method' => $pmDebit,
                    'code' => 'debit',
                    'paid_cash' => null,
                    'ref' => 'DEBIT-BCA-7712',
                ],
            ],
            // 4. Today Completed (Cash - Guest)
            [
                'number' => 'TRX-'.Carbon::today()->format('Ymd').'-0004',
                'shift_id' => $todayShift?->id,
                'customer_id' => null,
                'status' => TransactionStatus::Completed,
                'time' => Carbon::today()->setHour(12)->setMinute(20),
                'items' => [
                    ['product' => $getProd('Rawon Daging Sapi'), 'qty' => 1, 'price' => 32000],
                    ['product' => $getProd('Lemon Tea Iced Segar'), 'qty' => 1, 'price' => 12000],
                ],
                'payment' => [
                    'method' => $pmCash,
                    'code' => 'cash',
                    'paid_cash' => 50000,
                    'ref' => 'CASH-POS1-002',
                ],
            ],
            // 5. Today Void (Order Canceled)
            [
                'number' => 'TRX-'.Carbon::today()->format('Ymd').'-0005',
                'shift_id' => $todayShift?->id,
                'customer_id' => null,
                'status' => TransactionStatus::Void,
                'time' => Carbon::today()->setHour(12)->setMinute(35),
                'notes' => 'Dibatalkan oleh kasir: pelanggan salah memesan varian menu.',
                'items' => [
                    ['product' => $getProd('Mie Goreng Jawa'), 'qty' => 1, 'price' => 20000],
                ],
                'payment' => null,
            ],
            // 6. Yesterday Completed (Cash)
            [
                'number' => 'TRX-'.Carbon::yesterday()->format('Ymd').'-0001',
                'shift_id' => $yesterdayShift?->id,
                'customer_id' => $customer1?->id,
                'status' => TransactionStatus::Completed,
                'time' => Carbon::yesterday()->setHour(10)->setMinute(0),
                'items' => [
                    ['product' => $getProd('Soto Ayam Lamongan'), 'qty' => 2, 'price' => 18000],
                    ['product' => $getProd('Kopi Susu Gula Aren'), 'qty' => 2, 'price' => 18000],
                ],
                'payment' => [
                    'method' => $pmCash,
                    'code' => 'cash',
                    'paid_cash' => 100000,
                    'ref' => 'CASH-YEST-001',
                ],
            ],
            // 7. Yesterday Completed (QRIS)
            [
                'number' => 'TRX-'.Carbon::yesterday()->format('Ymd').'-0002',
                'shift_id' => $yesterdayShift?->id,
                'customer_id' => null,
                'status' => TransactionStatus::Completed,
                'time' => Carbon::yesterday()->setHour(14)->setMinute(15),
                'items' => [
                    ['product' => $getProd('Bebek Goreng Crispy'), 'qty' => 1, 'price' => 35000],
                    ['product' => $getProd('Lychee Tea with Jelly'), 'qty' => 1, 'price' => 16000],
                ],
                'payment' => [
                    'method' => $pmQris,
                    'code' => 'qris',
                    'paid_cash' => null,
                    'ref' => 'QRIS-ID-782103',
                ],
            ],
        ];

        foreach ($transactionsData as $tx) {
            $existing = Transaction::where('transaction_number', $tx['number'])->first();
            if ($existing) {
                continue;
            }

            $subtotal = 0;
            foreach ($tx['items'] as $item) {
                $subtotal += ($item['price'] * $item['qty']);
            }

            $tax = round($subtotal * 0.11);
            $total = $subtotal + $tax;

            $paidAmount = $tx['payment']['paid_cash'] ?? $total;
            $changeAmount = max(0, $paidAmount - $total);

            if ($tx['status'] === TransactionStatus::Void) {
                $paidAmount = 0;
                $changeAmount = 0;
            }

            $transaction = Transaction::create([
                'transaction_number' => $tx['number'],
                'outlet_id' => $outlet->id,
                'device_id' => $device?->id,
                'device_uuid' => $device?->uuid,
                'cashier_id' => $cashier->id,
                'shift_id' => $tx['shift_id'],
                'customer_id' => $tx['customer_id'],
                'subtotal' => $subtotal,
                'discount' => 0,
                'tax' => $tax,
                'total' => $total,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'status' => $tx['status'],
                'transaction_at' => $tx['time'],
                'synced_at' => $tx['time'],
                'notes' => $tx['notes'] ?? null,
            ]);

            foreach ($tx['items'] as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product']->id,
                    'sku' => $item['product']->sku ?? 'SKU-'.$item['product']->id,
                    'product_name' => $item['product']->name,
                    'price' => $item['price'],
                    'quantity' => $item['qty'],
                    'discount' => 0,
                    'tax' => round(($item['price'] * $item['qty']) * 0.11),
                    'subtotal' => $item['price'] * $item['qty'],
                ]);
            }

            if ($tx['payment'] && $tx['status'] === TransactionStatus::Completed) {
                Payment::create([
                    'transaction_id' => $transaction->id,
                    'payment_method_id' => $tx['payment']['method']?->id,
                    'payment_method' => $tx['payment']['code'],
                    'amount' => $total,
                    'reference' => $tx['payment']['ref'],
                    'status' => PaymentStatus::Paid,
                ]);
            }
        }
    }
}
