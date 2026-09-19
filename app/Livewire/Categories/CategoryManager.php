<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryManager extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $isModalOpen = false;

    public ?int $categoryId = null;

    public string $name = '';

    public string $slug = '';

    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug,'.$this->categoryId],
            'is_active' => ['boolean'],
        ];
    }

    public function updatedName(string $value): void
    {
        if (! $this->categoryId) {
            $this->slug = Str::slug($value);
        }
    }

    public function openCreateModal(): void
    {
        $this->reset(['categoryId', 'name', 'slug', 'is_active']);
        $this->is_active = true;
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function edit(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->is_active = (bool) $category->is_active;
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->categoryId) {
            $category = Category::findOrFail($this->categoryId);
            $category->update($validated);
            session()->flash('success', "Kategori '{$this->name}' berhasil diperbarui.");
        } else {
            Category::create([
                'uuid' => (string) Str::uuid(),
                ...$validated,
            ]);
            session()->flash('success', "Kategori '{$this->name}' berhasil ditambahkan.");
        }

        $this->isModalOpen = false;
        $this->reset(['categoryId', 'name', 'slug', 'is_active']);
    }

    public function delete(int $id): void
    {
        $category = Category::findOrFail($id);
        $category->delete();
        session()->flash('success', "Kategori '{$category->name}' berhasil dihapus.");
    }

    public function render(): View
    {
        $categories = Category::query()
            ->withCount('products')
            ->when($this->search, function ($query): void {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('slug', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        return view('livewire.categories.category-manager', [
            'categories' => $categories,
        ]);
    }
}
