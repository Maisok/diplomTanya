<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test adding a new staff member without image upload.
     *
     * @return void
     */
    public function testAddStaffMemberWithoutImage()
    {
        // Создаем пользователя и авторизуем его
        $user = User::factory()->create([
            'role' => 'admin', // Предположим, что только пользователи с ролью 'admin' могут добавлять сотрудников
        ]);
        $this->actingAs($user);

        // Подготовка данных для запроса
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'middle_name' => 'Michael',
        ];

        // Отправка POST-запроса на добавление сотрудника
        $response = $this->post(route('admin.staff.store'), $data);

        // Проверка, что сотрудник был успешно добавлен
        $response->assertRedirect(route('admin.staff.index'));
        $response->assertSessionHas('success', 'Персонал успешно добавлен.');

        // Проверка, что сотрудник был добавлен в базу данных
        $this->assertDatabaseHas('staff', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'middle_name' => 'Michael',
        ]);
    }

    /**
     * Test adding a new staff member without authentication.
     *
     * @return void
     */
    public function testAddStaffMemberWithoutAuthentication()
    {
        // Подготовка данных для запроса
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'middle_name' => 'Michael',
        ];

        // Отправка POST-запроса на добавление сотрудника без авторизации
        $response = $this->post(route('admin.staff.store'), $data);

        // Проверка, что запрос был отклонен из-за отсутствия авторизации
        $response->assertRedirect(route('login'));

        // Проверка, что сотрудник не был добавлен в базу данных
        $this->assertDatabaseMissing('staff', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'middle_name' => 'Michael',
        ]);
    }

    /**
     * Test adding a new staff member with non-admin role.
     *
     * @return void
     */
    public function testAddStaffMemberWithNonAdminRole()
    {
        // Создаем пользователя с ролью, отличной от 'admin'
        $user = User::factory()->create([
            'role' => 'user', // Предположим, что пользователи с ролью 'user' не могут добавлять сотрудников
        ]);
        $this->actingAs($user);

        // Подготовка данных для запроса
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'middle_name' => 'Michael',
        ];

        // Отправка POST-запроса на добавление сотрудника
        $response = $this->post(route('admin.staff.store'), $data);

        // Проверка, что запрос был отклонен из-за недостаточных прав
        $response->assertStatus(403); // Предположим, что ответ будет 403 Forbidden

        // Проверка, что сотрудник не был добавлен в базу данных
        $this->assertDatabaseMissing('staff', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'middle_name' => 'Michael',
        ]);
    }
}