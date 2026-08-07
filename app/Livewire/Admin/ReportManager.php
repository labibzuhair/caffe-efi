<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Expense;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;

#[Layout('layouts.app')]
#[Title('Laporan Detail - CaffePOS')]
class ReportManager extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function filterReport()
    {
        $this->resetPage();
    }

    public function render()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        // 1. DATA PEMASUKAN (ORDERS)
        $query = Order::with(['items.selectedAddons', 'session.table'])
            ->whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'paid')
            ->orderBy('created_at', 'desc');

        $allFilteredOrders = $query->get();

        $totalOrders = $allFilteredOrders->count();
        $totalRevenue = $allFilteredOrders->sum('total_price');

        $totalCogs = 0;
        foreach ($allFilteredOrders as $order) {
            foreach ($order->items as $item) {
                $itemCogs = $item->cogs_at_order;
                foreach ($item->selectedAddons as $addon) {
                    $itemCogs += $addon->addon_cogs;
                }
                $totalCogs += ($itemCogs * $item->qty);
            }
        }

        $grossProfit = $totalRevenue - $totalCogs;

        // 2. DATA PENGELUARAN (EXPENSES)
        // Ambil rincian pengeluaran untuk ditampilkan di tabel kedua
        $expensesList = Expense::whereBetween('expense_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->orderBy('expense_date', 'desc')
            ->get();

        $totalExpenses = $expensesList->sum('amount');

        // 3. LABA BERSIH
        $netProfit = $grossProfit - $totalExpenses;

        $orders = $query->paginate(15);

        return view('livewire.admin.report-manager', [
            'orders' => $orders,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'totalCogs' => $totalCogs,
            'grossProfit' => $grossProfit,
            'expensesList' => $expensesList, // Kirim rincian pengeluaran ke Blade
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
        ]);
    }
}
