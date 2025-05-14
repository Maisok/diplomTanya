<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BranchControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    #[Test]
    public function admin_can_create_branch_with_valid_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        
        Storage::fake('public');
        
        $response = $this->post(route('admin.branches.store'), [
            'address' => 'г.Москва, ул.Ленина, д.10',
            'image' => UploadedFile::fake()->image('branch.jpg'),
            'monday_open' => '09:00',
            'monday_close' => '18:00',
        ]);
        
        $response->assertRedirect(route('admin.branches.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('branches', [
            'address' => 'г.Москва, ул.Ленина, д.10'
        ]);
    }

    #[Test]
    public function regular_user_cannot_create_branch(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'phone' => '+79007654321' // Явно задаем номер
        ]);
        
        $this->actingAs($user);
        
        $response = $this->post(route('admin.branches.store'), [
            'address' => 'г.Москва, ул.Ленина, д.10',
            'image' => UploadedFile::fake()->image('branch.jpg')
        ]);
        
        // Проверяем либо 403, либо редирект
        $response->assertStatus($response->getStatusCode() === 403 ? 403 : 302);
        $this->assertDatabaseCount('branches', 0);
    }

    #[Test]
    public function guest_cannot_create_branch(): void
    {
        $response = $this->post(route('admin.branches.store'), [
            'address' => 'г.Москва, ул.Ленина, д.10',
            'image' => UploadedFile::fake()->image('branch.jpg')
        ]);
        
        $response->assertRedirect(route('login'));
    }
}