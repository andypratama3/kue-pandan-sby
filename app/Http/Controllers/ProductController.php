<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $user = Auth::user();
        $userRegionId = $user->region_id;

        $categories = Category::whereHas('products', function ($query) use ($userRegionId) {
            $query->where('region_id', $userRegionId);
        })->with(['products' => function ($query) use ($userRegionId) {
            $query->where('region_id', $userRegionId)
                ->with(['variants' => function ($q) {
                    $q->where('is_active', true);
                }]);
            // ->where('is_active', true);
        }])->get();

        $all_categories = Category::all();

        // Ambil nama region dari relasi
        $regionName = $user->region->name;

        // Kirim variabel $regionName ke view
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

        DB::transaction(function () use ($request) {
            // HAPUS BARIS-BARIS INI:
            // $image = $request->file('image');
            // $imageName = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
            // $image->move(public_path('storage/products'), $imageName);

            // CUKUP GUNAKAN SATU BARIS INI
            $imagePath = $request->file('image')->store('products', 'public');

            $product = Product::create([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'region_id' => Auth::user()->region_id,
                'description' => $request->description,
                'image_path' => $imagePath, // Path yang disimpan sudah benar: "products/namafile.jpg"
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
        if ($product->region_id !== Auth::user()->region_id) {
            abort(403);
        }

        // Mengirim data produk yang spesifik ke view 'show'
        return view('dashboard.admin.products.show', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        if ($product->region_id !== Auth::user()->region_id) {
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
                if (!empty($variantData['id'])) {
                    // Ini adalah varian LAMA -> UPDATE
                    $variant = ProductVariant::find($variantData['id']);
                    if ($variant) {
                        $variant->update([
                            'name' => $variantData['name'],
                            'price' => $variantData['price'],
                            'is_active' => true,
                        ]);
                        $submittedVariantIds[] = (int)$variant->id;
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
                    if ($e->getCode() === '23000') $variantToDelete->update(['is_active' => false]);
                    else throw $e;
                }
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->region_id !== Auth::user()->region_id) {
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
