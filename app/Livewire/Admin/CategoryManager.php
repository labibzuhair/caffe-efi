<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Manajemen Kategori - CaffePOS')]
class CategoryManager extends Component
{
    use WithPagination;

    // Variabel untuk Form
    public $name = '';
    public $is_active = true;
    public $categoryId = null; // Menyimpan ID jika sedang mode Edit
    public $isEditMode = false;

    // Aturan Validasi
    protected $rules = [
        'name' => 'required|string|max:255|unique:categories,name',
    ];

    // Reset pesan error setiap kali modal tertutup atau form direset
    public function resetForm()
    {
        $this->reset(['name', 'is_active', 'categoryId', 'isEditMode']);
        $this->resetValidation();
    }

    // Fungsi Tambah / Simpan Kategori
    public function save()
    {
        // Jika sedang edit, kecualikan ID ini dari validasi unique
        $rules = $this->rules;
        if ($this->isEditMode) {
            $rules['name'] = 'required|string|max:255|unique:categories,name,' . $this->categoryId;
        }

        $this->validate($rules);

        Category::updateOrCreate(
            ['id' => $this->categoryId],
            [
                'name' => $this->name,
                'is_active' => $this->is_active
            ]
        );

        session()->flash('message', $this->isEditMode ? 'Kategori berhasil diperbarui!' : 'Kategori baru berhasil ditambahkan!');
        $this->resetForm();
    }

    // Fungsi Siapkan Data untuk Edit
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->is_active = $category->is_active;
        $this->isEditMode = true;
    }

    // Fungsi Toggle Status (Aktif / Non-Aktif langsung dari tabel)
    public function toggleStatus($id)
    {
        $category = Category::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);
    }

    // Fungsi Hapus Kategori
    public function delete($id)
    {
        // Nanti kita bisa tambahkan logika: Jangan hapus jika kategori ini punya produk!
        // Untuk sekarang, kita hapus saja langsung
        Category::findOrFail($id)->delete();
        session()->flash('message', 'Kategori berhasil dihapus!');
    }

    public function render()
    {
        // Mengambil data kategori dengan pagination
        $categories = Category::latest()->paginate(10);

        return view('livewire.admin.category-manager', [
            'categories' => $categories
        ]);
    }
}
