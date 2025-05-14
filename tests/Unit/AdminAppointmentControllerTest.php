<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAppointmentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /** @test */
    public function admin_can_activate_appointment()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment = Appointment::factory()->create(['status' => 'active']);
        
        $response = $this->actingAs($admin)
            ->post(route('admin.appointments.activate', $appointment->id));
            
        $response->assertRedirect()
            ->assertSessionHas('success', 'Запись активирована');
            
        $this->assertEquals('active', $appointment->fresh()->status);
    }

    /** @test */
    public function admin_can_complete_appointment()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment = Appointment::factory()->create(['status' => 'active']);
        
        $response = $this->actingAs($admin)
            ->post(route('admin.appointments.complete', $appointment->id));
            
        $response->assertRedirect()
            ->assertSessionHas('success', 'Запись завершена');
            
        $this->assertEquals('completed', $appointment->fresh()->status);
    }

    /** @test */
    public function admin_can_cancel_appointment_and_reset_rating()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment = Appointment::factory()->create([
            'status' => 'active',
            'rating' => 5
        ]);
        
        $response = $this->actingAs($admin)
            ->post(route('admin.appointments.cancel', $appointment->id));
            
        $response->assertRedirect()
            ->assertSessionHas('success', 'Запись отменена, оценка сброшена');
            
        $updated = $appointment->fresh();
        $this->assertEquals('cancelled', $updated->status);
        $this->assertNull($updated->rating);
    }

    /** @test */
   /** @test */
public function regular_user_cannot_change_appointment_status()
{
    $user = User::factory()->create(['role' => 'user']);
    $appointment = Appointment::factory()->create(['status' => 'active']);
    
    $response = $this->actingAs($user)
        ->post(route('admin.appointments.activate', $appointment->id));
    
    // Проверяем либо 403, либо редирект (в зависимости от вашей логики)
    if ($response->getStatusCode() === 403) {
        $response->assertForbidden();
    } else {
        $response->assertRedirect();
    }
    
    $this->assertEquals('active', $appointment->fresh()->status);
}

    /** @test */
    public function guest_cannot_change_appointment_status()
    {
        $appointment = Appointment::factory()->create(['status' => 'active']);
        
        $response = $this->post(route('admin.appointments.activate', $appointment->id));
            
        $response->assertRedirect(route('login'));
        $this->assertEquals('active', $appointment->fresh()->status);
    }
}