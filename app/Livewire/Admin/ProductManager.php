<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
#[Title('Manajemen Produk - CaffePOS')]
class ProductManager extends Component
{
    use WithPagination, WithFileUploads;

    public $name = '';
    public $category_id = '';
    public $price = '';
    public $cogs = '';
    public $description = '';
    public $image;
    public $oldImage;
    public $is_active = true;

    public $addons = [];

    public $productId = null;
    public $isEditMode = false;

    public $search = '';
    public $filterCategory = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterCategory()
    {
        $this->resetPage();
    }

    public function addAddon()
    {
        $this->addons[] = [
            'category' => '',
            'name' => '',
            'additional_price' => 0,
            'additional_cogs' => 0
        ];
    }

    public function removeAddon($index)
    {
        unset($this->addons[$index]);
        $this->addons = array_values($this->addons);
    }

    public function resetForm()
    {
        $this->reset(['name', 'category_id', 'price', 'cogs', 'description', 'image', 'oldImage', 'is_active', 'productId', 'isEditMode', 'addons']);
        $this->resetValidation();
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255|unique:products,name,' . $this->productId,
            'category_id' => 'required|exists:categories,id',
            'cogs' => 'required|numeric|min:0',
            'price' => 'required|numeric|gte:cogs',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:2048',

            'addons.*.category' => 'required|string|max:100',
            'addons.*.name' => 'required|string|max:100',
            'addons.*.additional_price' => 'required|numeric|min:0',
            'addons.*.additional_cogs' => 'required|numeric|min:0',
        ];

        $messages = [
            'price.gte' => 'Harga jual tidak boleh lebih murah dari Modal (HPP)!',
            'addons.*.category.required' => 'Kategori varian wajib diisi.',
            'addons.*.name.required' => 'Nama varian wajib diisi.',
        ];

        $this->validate($rules, $messages);

        $imagePath = $this->oldImage;

        if ($this->image) {
            if ($this->oldImage) {
                Storage::disk('public')->delete($this->oldImage);
            }
            $imagePath = $this->image->store('products', 'public');
        }

        $product = Product::updateOrCreate(
            ['id' => $this->productId],
            [
                'category_id' => $this->category_id,
                'name' => $this->name,
                'price' => $this->price,
                'cogs' => $this->cogs, // Simpan HPP
                'description' => $this->description,
                'image' => $imagePath,
                'is_active' => $this->is_active
            ]
        );


        $product->addons()->delete();
        foreach ($this->addons as $addonData) {
            $product->addons()->create([
                'category' => $addonData['category'],
                'name' => $addonData['name'],
                'additional_price' => $addonData['additional_price'] ?: 0,
                'additional_cogs' => $addonData['additional_cogs'] ?: 0,
            ]);
        }

        session()->flash('message', $this->isEditMode ? 'Produk & Varian berhasil diperbarui!' : 'Produk baru berhasil ditambahkan!');
        $this->resetForm();
    }

    public function edit($id)
    {
        $product = Product::with('addons')->findOrFail($id);

        $this->productId = $product->id;
        $this->category_id = $product->category_id;
        $this->name = $product->name;
        $this->price = $product->price;
        $this->cogs = $product->cogs;
        $this->description = $product->description;
        $this->oldImage = $product->image;
        $this->is_active = $product->is_active;
        $this->isEditMode = true;

        $this->addons = $product->addons->map(function ($addon) {
            return [
                'category' => $addon->category,
                'name' => $addon->name,
                'additional_price' => $addon->additional_price,
                'additional_cogs' => $addon->additional_cogs,
            ];
        })->toArray();
    }

    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete(); 
        session()->flash('message', 'Produk beserta variannya berhasil dihapus!');
    }

    public function render()
    {
        $categories = Category::orderBy('name')->get();

        $products = Product::with(['category', 'addons'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterCategory, function ($query) {
                $query->where('category_id', $this->filterCategory);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.product-manager', [
            'categories' => $categories,
            'products' => $products
        ]);
    }
}
