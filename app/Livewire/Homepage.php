<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Homepage extends Component
{
    public $isMobileMenuOpen = false;

    public function toggleMobileMenu()
    {
        $this->isMobileMenuOpen = !$this->isMobileMenuOpen;
    }

    public static function products(): Collection
    {
        return Product::query()
            ->where('is_active', true)
            ->with(['variants' => fn ($q) => $q->where('is_active', true)->orderBy('price')])
            ->orderByRaw("FIELD(tag, 'Ala Carte', 'Hampers', 'Tumpeng')")
            ->orderBy('name')
            ->get()
            ->map(function (Product $product) {
                return [
                    'name' => $product->name,
                    'tag' => $product->tag,
                    'description' => $product->description,
                    'image' => self::imageUrl($product),
                    'variants' => $product->variants->map(fn ($v) => [
                        'label' => $v->name,
                        'value' => (int) $v->price,
                    ])->all(),
                ];
            })
            ->values();
    }

    public function render()
    {
        return view('livewire.homepage', [
            'products' => static::products(),
        ]);
    }

    protected static function imageUrl(Product $product): string
    {
        $path = $product->image_path;

        if (! $path) {
            return asset('assets/homepage/product/kue-ijo.jpg');
        }

        // Path legacy (assets/...) langsung via asset(); upload baru via Storage.
        return str_starts_with($path, 'assets/')
            ? asset($path)
            : Storage::url($path);
    }
}
