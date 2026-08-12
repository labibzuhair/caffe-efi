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

    public $name = '';
    public $is_active = true;
    public $categoryId = null;
    public $isEditMode = false;

    protected $rules = [
        'name' => 'required|string|max:255|unique:categories,name',
    ];

    public function resetForm()
    {
        $this->reset(['name', 'is_active', 'categoryId', 'isEditMode']);
        $this->resetValidation();
    }

    public function save()
    {
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

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->is_active = $category->is_active;
        $this->isEditMode = true;
    }

    public function toggleStatus($id)
    {
        $category = Category::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);
    }

    public function delete($id)
    {

        Category::findOrFail($id)->delete();
        session()->flash('message', 'Kategori berhasil dihapus!');
    }

    public function render()
    {
        $categories = Category::latest()->paginate(10);

        return view('livewire.admin.category-manager', [
            'categories' => $categories
        ]);
    }
}
