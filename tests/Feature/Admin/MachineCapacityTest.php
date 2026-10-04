<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Machine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MachineCapacityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        config(['app.url' => 'http://localhost']);
        url()->forceRootUrl('http://localhost');

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->category = Category::create([
            'name' => 'Mesin Pengolah',
            'slug' => 'mesin-pengolah',
        ]);
    }

    public function test_machine_capacity_accessors_parse_correctly(): void
    {
        $machine1 = new Machine([
            'capacity' => '500 kg/jam',
            'power' => '5,5 kW / 3 phase',
            'dimension' => '180 x 90 x 140 cm',
            'weight' => '320 kg',
        ]);

        $this->assertEquals('500', $machine1->capacity_amount);
        $this->assertEquals('1', $machine1->capacity_time);
        $this->assertEquals('5.5', $machine1->power_kw);
        $this->assertEquals('3', $machine1->power_phase);
        $this->assertEquals('180', $machine1->dimension_length);
        $this->assertEquals('90', $machine1->dimension_width);
        $this->assertEquals('140', $machine1->dimension_height);
        $this->assertEquals('320', $machine1->weight_amount);
    }

    public function test_machine_store_combines_capacity_power_dimension_and_weight(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/machines', [
            'category_id' => $this->category->id,
            'name' => 'Mesin Roaster Kopi Super',
            'model_code' => 'MDI-R100S',
            'capacity_amount' => 500,
            'capacity_time' => 1,
            'power_kw' => 5.5,
            'power_phase' => 3,
            'dimension_length' => 180,
            'dimension_width' => 90,
            'dimension_height' => 140,
            'weight_amount' => 320,
        ]);

        $response->assertRedirect('/admin/machines');
        $this->assertDatabaseHas('machines', [
            'name' => 'Mesin Roaster Kopi Super',
            'capacity' => '500 kg/jam',
            'power' => '5.5 kW / 3 phase',
            'dimension' => '180 x 90 x 140 cm',
            'weight' => '320 kg',
        ]);
    }

    public function test_machine_store_combines_custom_capacity_time(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/machines', [
            'category_id' => $this->category->id,
            'name' => 'Mesin Drying Kakao',
            'model_code' => 'MDI-D200',
            'capacity_amount' => 750,
            'capacity_time' => 3,
        ]);

        $response->assertRedirect('/admin/machines');
        $this->assertDatabaseHas('machines', [
            'name' => 'Mesin Drying Kakao',
            'capacity' => '750 kg/3 jam',
        ]);
    }

    public function test_machine_update_combines_capacity_amount_and_time(): void
    {
        $machine = Machine::create([
            'category_id' => $this->category->id,
            'name' => 'Mesin Pulper Utama',
            'slug' => 'mesin-pulper-utama',
            'capacity' => '200 kg/jam',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put('/admin/machines/'.$machine->slug, [
            'category_id' => $this->category->id,
            'name' => 'Mesin Pulper Utama Modified',
            'capacity_amount' => 350,
            'capacity_time' => 2,
            'power_kw' => 7.5,
            'power_phase' => 3,
            'dimension_length' => 200,
            'dimension_width' => 100,
            'dimension_height' => 150,
            'weight_amount' => 450,
        ]);

        $response->assertRedirect('/admin/machines');
        $this->assertDatabaseHas('machines', [
            'id' => $machine->id,
            'capacity' => '350 kg/2 jam',
            'power' => '7.5 kW / 3 phase',
            'dimension' => '200 x 100 x 150 cm',
            'weight' => '450 kg',
        ]);
    }
}
