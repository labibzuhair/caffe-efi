<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $order->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                background-color: white;
                margin: 0;
                padding: 0;
            }

            .print\:hidden {
                display: none !important;
            }

            @page {
                size: 58mm auto;
                margin: 0mm;
            }

            .page-break-inside-avoid {
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }
    </style>
</head>

<body class="bg-slate-100 text-black min-h-screen p-4 flex justify-center font-mono">

    @php
        $setting = \App\Models\Setting::first();
        $taxPercentage = $setting->tax_percentage ?? 0;

        $subtotal = 0;
        $personSubtotals = [];

        foreach ($order->items as $item) {
            $itemTotal = $item->price_at_order * $item->qty;

            foreach ($item->selectedAddons as $addon) {
                $itemTotal += $addon->addon_price * $item->qty;
            }

            $subtotal += $itemTotal;

            $customerName = $item->customer->display_name ?? 'Tamu';
            if (!isset($personSubtotals[$customerName])) {
                $personSubtotals[$customerName] = 0;
            }
            $personSubtotals[$customerName] += $itemTotal;
        }

        $taxAmount = ($subtotal * $taxPercentage) / 100;
        $total = $subtotal + $taxAmount;

        $personDetails = [];
        foreach ($personSubtotals as $name => $pSubtotal) {
            $pTax = ($pSubtotal * $taxPercentage) / 100;
            $personDetails[$name] = [
                'subtotal' => $pSubtotal,
                'tax' => $pTax,
                'total' => $pSubtotal + $pTax,
            ];
        }
    @endphp

    <div class="w-full max-w-[80mm] bg-white p-4 shadow-sm border border-slate-200">

        <div class="text-center mb-4">
            <h1 class="text-xl font-bold uppercase">{{ $setting->store_name ?? 'CAFFEPOS' }}</h1>
            <p class="text-xs">{{ $setting->store_address ?? 'Alamat Toko Belum Diatur' }}</p>
            <p class="text-xs">{{ $setting->store_phone ?? '' }}</p>
            <div class="border-b border-dashed border-black mt-2"></div>
        </div>

        <div class="text-xs mb-4">
            <div class="flex justify-between">
                <span>Tgl: {{ $order->created_at->format('d/m/Y H:i') }}</span>
                <span>Meja: {{ $order->session->table->table_number ?? 'Takeaway' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Struk: {{ $order->order_number }}</span>
                <span>Sesi: #{{ $order->table_session_id }}</span>
            </div>
            <div class="border-b border-dashed border-black mt-2"></div>
        </div>

        <div class="text-xs mb-4 space-y-2">
            @foreach ($order->items as $item)
                <div class="page-break-inside-avoid">
                    <div class="flex justify-between">
                        <span class="uppercase font-bold">{{ $item->product->name }}</span>

                        @php
                            $itemTotalPrices = $item->price_at_order;
                            foreach ($item->selectedAddons as $addon) {
                                $itemTotalPrices += $addon->addon_price;
                            }
                        @endphp

                        <span>{{ number_format($itemTotalPrices * $item->qty, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-[10px] text-gray-600">
                        <span>{{ $item->qty }}x @ {{ number_format($itemTotalPrices, 0, ',', '.') }}</span>
                        <span>({{ $item->customer->display_name ?? 'Tamu' }})</span>
                    </div>

                    @foreach ($item->selectedAddons as $addon)
                        <div class="flex justify-between text-[10px] text-gray-500 pl-2">
                            <span>- {{ $addon->addon_name }}</span>
                            @if ($addon->addon_price > 0)
                                <span>+{{ number_format($addon->addon_price, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
            <div class="border-b border-dashed border-black mt-2"></div>
        </div>

        <div class="text-xs mb-4 page-break-inside-avoid">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span>{{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            @if ($taxPercentage > 0)
                <div class="flex justify-between">
                    <span>Pajak ({{ $taxPercentage }}%)</span>
                    <span>{{ number_format($taxAmount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between font-bold text-sm mt-1">
                <span>TOTAL</span>
                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>

            <div class="border-t border-dashed border-gray-300 mt-2 pt-2"></div>
            <div class="flex justify-between mt-1">
                <span>Metode Bayar</span>
                <span class="uppercase font-bold">{{ $order->payment_method ?? 'CASH' }}</span>
            </div>
            @if (($order->payment_method ?? 'Cash') === 'Cash' && $order->cash_received > 0)
                <div class="flex justify-between">
                    <span>Tunai (Diterima)</span>
                    <span>Rp {{ number_format($order->cash_received, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold">
                    <span>Kembali</span>
                    <span>Rp {{ number_format($order->change_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="border-b border-dashed border-black mt-2"></div>
        </div>

        @if (count($personDetails) > 0)
            <div class="text-xs mb-4 page-break-inside-avoid">
                <p class="font-bold text-center mb-1">-- SPLIT BILL --</p>

                @foreach ($personDetails as $name => $detail)
                    <div class="mb-1.5">
                        <div class="flex justify-between font-bold">
                            <span>{{ $name }}</span>
                            <span>{{ number_format($detail['total'], 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-end text-[10px] text-gray-600">
                            <span>{{ number_format($detail['subtotal'], 0, ',', '.') }} +
                                {{ number_format($detail['tax'], 0, ',', '.') }} (pjk)</span>
                        </div>
                    </div>
                @endforeach

                <div class="border-b border-dashed border-black mt-2"></div>
            </div>
        @endif

        <div class="text-center text-xs mt-4 page-break-inside-avoid">
            <p>Terima kasih atas kunjungan Anda!</p>
            <p class="mt-1 font-bold">STATUS: LUNAS</p>
        </div>

        <div class="mt-8 flex flex-col gap-2 print:hidden">
            <button onclick="window.print()"
                class="w-full bg-slate-800 hover:bg-slate-700 text-white py-2 rounded-lg font-bold transition-colors">
                🖨️ Cetak Ulang
            </button>
            <button onclick="window.close()"
                class="w-full text-center bg-slate-200 hover:bg-slate-300 text-slate-800 py-2 rounded-lg font-bold transition-colors">
                Tutup Tab Ini
            </button>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        }
    </script>
</body>

</html>
