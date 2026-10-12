<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Machine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MachineApiController extends Controller
{
    /**
     * GET /api/machines
     */
    public function index(Request $request): JsonResponse
    {
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
            return [
                'id' => $m->id,
                'name' => $m->name,
                'slug' => $m->slug,
                'model_code' => $m->model_code,
                'category' => $m->category?->name ?? 'Mesin Industri',
                'category_id' => $m->category_id,
                'price' => (float) ($m->price ?? 145000000),
                'function' => $m->function,
                'description' => $m->description,
                'capacity' => $m->capacity,
                'power' => $m->power,
                'dimension' => $m->dimension,
                'weight' => $m->weight,
                'specifications' => $m->specifications,
                'image_url' => $m->primaryImageUrl() ?? 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?w=800&auto=format&fit=crop&q=80',
                'images' => $m->images->map(fn($img) => $img->imageUrl())->toArray(),
                'rating' => 4.9,
                'is_ready_stock' => true,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $machines,
        ]);
    }

    /**
     * GET /api/machines/{id}
     */
    public function show(string $id): JsonResponse
    {
        $machine = Machine::with(['category', 'images'])
            ->where('id', $id)
            ->orWhere('slug', $id)
            ->firstOrFail();

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
                'image_url' => $machine->primaryImageUrl() ?? 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?w=800&auto=format&fit=crop&q=80',
                'images' => $machine->images->map(fn($img) => $img->imageUrl())->toArray(),
            ],
        ]);
    }

    /**
     * GET /api/categories
     */
    public function categories(): JsonResponse
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}
