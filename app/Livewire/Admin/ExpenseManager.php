<?php

namespace App\Livewire\Admin;

use App\Models\Expense;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
#[Title('Pengeluaran Kafe - CaffePOS')]
class ExpenseManager extends Component
{
    use WithPagination;

    public $expense_date;
    public $category = '';
    public $description = '';
    public $amount = '';

    public $expenseId = null;
    public $isEditMode = false;

    // Filter
    public $filterMonth;
    public $filterYear;

    public function mount()
    {
        $this->expense_date = Carbon::now()->format('Y-m-d');
        $this->filterMonth = Carbon::now()->format('m');
        $this->filterYear = Carbon::now()->format('Y');
    }

    public function updatingFilterMonth()
    {
        $this->resetPage();
    }
    public function updatingFilterYear()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->reset(['category', 'description', 'amount', 'expenseId', 'isEditMode']);
        $this->expense_date = Carbon::now()->format('Y-m-d');
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
        ]);

        Expense::updateOrCreate(
            ['id' => $this->expenseId],
            [
                'expense_date' => $this->expense_date,
                'category' => $this->category,
                'description' => $this->description,
                'amount' => $this->amount,
                'user_id' => Auth::id(), 
            ]
        );

        session()->flash('message', $this->isEditMode ? 'Pengeluaran diperbarui!' : 'Pengeluaran berhasil dicatat!');
        $this->resetForm();
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $this->expenseId = $expense->id;
        $this->expense_date = $expense->expense_date;
        $this->category = $expense->category;
        $this->description = $expense->description;
        $this->amount = $expense->amount;
        $this->isEditMode = true;
    }

    public function delete($id)
    {
        Expense::findOrFail($id)->delete();
        session()->flash('message', 'Data pengeluaran dihapus!');
    }

    public function render()
    {
        $expenses = Expense::with('user')
            ->whereMonth('expense_date', $this->filterMonth)
            ->whereYear('expense_date', $this->filterYear)
            ->orderBy('expense_date', 'desc')
            ->paginate(10);

        $totalExpenses = Expense::whereMonth('expense_date', $this->filterMonth)
            ->whereYear('expense_date', $this->filterYear)
            ->sum('amount');

        return view('livewire.admin.expense-manager', [
            'expenses' => $expenses,
            'totalExpenses' => $totalExpenses
        ]);
    }
}
