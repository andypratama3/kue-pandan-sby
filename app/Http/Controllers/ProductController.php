<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\RegionContext;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        // index() terbuka untuk semua role yang login (kurir butuh melihat katalog).
        // Operasi CRUD khusus admin & owner.
        $this->middleware(['auth', 'role:admin|owner'])->except(['index']);
    }

    public function index()
    {
        $user = Auth::user();
        $userRegionId = RegionContext::regionId();

        $categories = Category::whereHas('products', function ($query) use ($userRegionId) {
            $query->where('region_id', $userRegionId);
        })->with(['products' => function ($query) use ($userRegionId) {
            $query->where('region_id', $userRegionId)
                ->with(['variants' => function ($q) {
                    $q->where('is_active', true);
                }]);
        }])->get();

        $all_categories = Category::all();

        // Ambil nama region dari relasi
        $regionName = RegionContext::name() ?? '';

        // Kurir melihat katalog produk (read-only) di view khusus kurir
        if ($user->hasRole('kurir')) {
            return view('dashboard.kurir.products.index', compact('categories', 'all_categories', 'regionName'));
        }

        return view('dashboard.admin.products.index', compact('categories', 'all_categories', 'regionName'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variants' => 'required|array|min:1',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.price' => 'required|integer|min:0',
        ]);

        // Cegah duplikasi nama produk di cabang yang sama
        $existing = Product::where('region_id', RegionContext::regionId())
            ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($request->name))])
            ->exists();
        if ($existing) {
            return back()->withInput()->withErrors(['name' => 'Nama produk sudah digunakan di cabang ini.']);
        }

        DB::transaction(function () use ($request) {
            $imagePath = $request->file('image')->store('products', 'public');

            $product = Product::create([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'region_id' => RegionContext::regionId(),
                'description' => $request->description,
                'image_path' => $imagePath,
                'tag' => $request->tag,
                'is_active' => true,
            ]);

            foreach ($request->variants as $variantData) {
                $product->variants()->create($variantData);
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        // Pastikan admin hanya bisa melihat produk di regionnya sendiri
        if ($product->region_id !== RegionContext::regionId()) {
            abort(403);
        }

        // Mengirim data produk yang spesifik ke view 'show'
        return view('dashboard.admin.products.show', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        if ($product->region_id !== RegionContext::regionId()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variants' => 'required|array|min:1',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.price' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($product, $request) {
            $productData = $request->only(['name', 'category_id', 'description', 'tag']);
            $productData['is_active'] = $request->has('is_active');

            if ($request->hasFile('image')) {
                // PERBAIKAN: Hapus gambar lama dengan Storage
                if ($product->image_path) {
                    Storage::disk('public')->delete($product->image_path);
                }
                // PERBAIKAN: Simpan gambar baru dengan Storage
                $productData['image_path'] = $request->file('image')->store('products', 'public');
            }
            $product->update($productData);

            $existingVariantIds = $product->variants()->pluck('id')->toArray();
            $submittedVariantIds = [];

            foreach ($request->variants as $variantData) {
                if (! empty($variantData['id'])) {
                    // Ini adalah varian LAMA -> UPDATE
                    $variant = ProductVariant::find($variantData['id']);
                    if ($variant) {
                        $variant->update([
                            'name' => $variantData['name'],
                            'price' => $variantData['price'],
                            'is_active' => true,
                        ]);
                        $submittedVariantIds[] = (int) $variant->id;
                    }
                } else {
                    // Ini adalah varian BARU -> CREATE
                    $newVariant = $product->variants()->create([
                        'name' => $variantData['name'],
                        'price' => $variantData['price'],
                    ]);
                    $submittedVariantIds[] = $newVariant->id;
                }
            }

            $variantsToDeleteIds = array_diff($existingVariantIds, $submittedVariantIds);

            foreach (ProductVariant::findMany($variantsToDeleteIds) as $variantToDelete) {
                try {
                    $variantToDelete->delete();
                } catch (QueryException $e) {
                    if ($e->getCode() === '23000') {
                        $variantToDelete->update(['is_active' => false]);
                    } else {
                        throw $e;
                    }
                }
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->region_id !== RegionContext::regionId()) {
            abort(403);
        }

        try {
            DB::transaction(function () use ($product) {
                // PERBAIKAN: Hapus file dari storage dengan cara yang benar
                if ($product->image_path) {
                    Storage::disk('public')->delete($product->image_path);
                }
                $product->delete();
            });
        } catch (QueryException $e) {
            return back()->withErrors('Gagal menghapus produk karena terkait dengan pesanan yang ada.');
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
