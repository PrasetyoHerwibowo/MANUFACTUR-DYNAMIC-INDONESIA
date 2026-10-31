<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Machine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class MachineApiController extends Controller
{
    /**
     * GET /api/machines
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Machine::query()
                ->with(['category', 'images'])
                ->where('is_active', true);

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->input('category_id'));
            }

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ilike', "%{$search}%")
                      ->orWhere('model_code', 'ilike', "%{$search}%")
                      ->orWhere('function', 'ilike', "%{$search}%")
                      ->orWhere('description', 'ilike', "%{$search}%");
                });
            }

            $machines = $query->latest('id')->get()->map(function ($m) {
                $categoryName = $m->category?->name ?? 'Mesin Pengolahan Kopi';
                $isKakao = str_contains(strtolower($m->name . ' ' . $categoryName), 'kakao');
                $isHulu = str_contains(strtolower($m->name . ' ' . $m->function), 'pulper') || str_contains(strtolower($m->name . ' ' . $m->function), 'huller') || str_contains(strtolower($m->name . ' ' . $m->function), 'fermentasi') || str_contains(strtolower($m->name . ' ' . $m->function), 'pengering');

                return [
                    'id' => $m->id,
                    'name' => $m->name,
                    'slug' => $m->slug,
                    'model_code' => $m->model_code,
                    'category' => $categoryName,
                    'category_id' => $m->category_id,
                    'commodity' => $isKakao ? 'Kakao' : 'Kopi',
                    'process_stage' => $isHulu ? 'Hulu' : 'Hilir',
                    'price' => (float) ($m->price ?? ($m->id == 1 ? 145000000 : ($m->id == 2 ? 85000000 : ($m->id == 3 ? 120000000 : 75000000)))),
                    'function' => $m->function,
                    'description' => $m->description,
                    'capacity' => $m->capacity,
                    'power' => $m->power,
                    'dimension' => $m->dimension,
                    'weight' => $m->weight,
                    'specifications' => $m->specifications,
                    'image_url' => $this->resolveMachineImage($m),
                    'images' => $m->images->map(fn($img) => $img->imageUrl())->toArray(),
                    'rating' => 4.9,
                    'is_ready_stock' => true,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $machines,
            ]);
        } catch (Throwable $e) {
            Log::error('MachineApiController index error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat katalog mesin industri.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * GET /api/machines/{id}
     */
    public function show(string $id): JsonResponse
    {
        try {
            $machine = Machine::with(['category', 'images'])
                ->where('id', is_numeric($id) ? (int)$id : 0)
                ->orWhere('slug', $id)
                ->first();

            if (! $machine) {
                return response()->json([
                    'success' => false,
                    'message' => 'Model mesin tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $machine->id,
                    'name' => $machine->name,
                    'slug' => $machine->slug,
                    'model_code' => $machine->model_code,
                    'category' => $machine->category?->name ?? 'Mesin Industri',
                    'category_id' => $machine->category_id,
                    'price' => (float) ($machine->price ?? 145000000),
                    'function' => $machine->function,
                    'description' => $machine->description,
                    'capacity' => $machine->capacity,
                    'power' => $machine->power,
                    'dimension' => $machine->dimension,
                    'weight' => $machine->weight,
                    'specifications' => $machine->specifications,
                    'image_url' => $machine->primaryImageUrl() ?? $machine->photo ?? 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?w=800&auto=format&fit=crop&q=80',
                    'images' => $machine->images->map(fn($img) => $img->imageUrl())->toArray(),
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('MachineApiController show error: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail mesin.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * GET /api/categories
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

            return response()->json([
                'success' => true,
                'data' => $categories,
            ]);
        } catch (Throwable $e) {
            Log::error('MachineApiController categories error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat kategori mesin.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Resolusi URL foto mesin berkualitas tinggi untuk mobile app
     */
    private function resolveMachineImage(Machine $m): string
    {
        $primary = $m->primaryImageUrl();
        if ($primary && !str_contains($primary, 'placeholder.svg')) {
            return $primary;
        }

        $name = strtolower($m->name . ' ' . $m->model_code . ' ' . ($m->category?->name ?? ''));

        if (str_contains($name, 'roast') || str_contains($name, 'sangrai')) {
            return 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=800&auto=format&fit=crop&q=80';
        }
        if (str_contains($name, 'pulper') || str_contains($name, 'basah')) {
            return 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=800&auto=format&fit=crop&q=80';
        }
        if (str_contains($name, 'huller') || str_contains($name, 'kering')) {
            return 'https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=800&auto=format&fit=crop&q=80';
        }
        if (str_contains($name, 'kakao') || str_contains($name, 'fermentasi') || str_contains($name, 'pengering')) {
            return 'https://images.unsplash.com/photo-1549007994-cb92caebd54b?w=800&auto=format&fit=crop&q=80';
        }
        if (str_contains($name, 'grinder') || str_contains($name, 'penepung')) {
            return 'https://images.unsplash.com/photo-1587080413959-06b859fb107d?w=800&auto=format&fit=crop&q=80';
        }
        if (str_contains($name, 'espresso') || str_contains($name, 'craft')) {
            return 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?w=800&auto=format&fit=crop&q=80';
        }

        return 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?w=800&auto=format&fit=crop&q=80';
    }
}
