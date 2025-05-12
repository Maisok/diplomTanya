<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration without reCAPTCHA verification.
     *
     * @return void
     */
    public function testUserRegistrationWithoutRecaptcha()
    {
        // Подготовка данных для запроса
        $data = [
            'name' => 'John Doe',
            'phone' => '8 123 456 78 90',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'g-recaptcha-response' => 'dummy-response', // Добавляем фиктивный ответ reCAPTCHA
        ];

        // Отправка POST-запроса на регистрацию пользователя
        $response = $this->post(route('register'), $data);

        // Проверка, что пользователь был успешно зарегистрирован
        $response->assertRedirect('/');

        // Проверка, что пользователь был добавлен в базу данных
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'phone' => '8 123 456 78 90',
        ]);

        // Проверка, что пользователь был авторизован
        $this->assertAuthenticated();
    }

    /**
     * Test user registration with invalid phone number format.
     *
     * @return void
     */
    public function testUserRegistrationWithInvalidPhoneFormat()
    {
        // Подготовка данных для запроса с неправильным форматом телефона
        $data = [
            'name' => 'John Doe',
            'phone' => '1234567890', // Неправильный формат
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'g-recaptcha-response' => 'dummy-response',
        ];

        // Отправка POST-запроса на регистрацию пользователя
        $response = $this->post(route('register'), $data);

        // Проверка, что регистрация не прошла из-за неправильного формата телефона
        $response->assertSessionHasErrors(['phone']);

        // Проверка, что пользователь не был добавлен в базу данных
        $this->assertDatabaseMissing('users', [
            'name' => 'John Doe',
            'phone' => '1234567890',
        ]);

        // Проверка, что пользователь не был авторизован
        $this->assertGuest();
    }

    /**
     * Test user registration with missing required fields.
     *
     * @return void
     */
    public function testUserRegistrationWithMissingRequiredFields()
    {
        // Подготовка данных для запроса с отсутствующими обязательными полями
        $data = [
            'name' => '',
            'phone' => '',
            'password' => '',
            'password_confirmation' => '',
            
        ];

        // Отправка POST-запроса на регистрацию пользователя
        $response = $this->post(route('register'), $data);

        // Проверка, что регистрация не прошла из-за отсутствия обязательных полей
        $response->assertSessionHasErrors(['name', 'phone', 'password'ы]);

        // Проверка, что пользователь не был добавлен в базу данных
        $this->assertDatabaseCount('users', 0);

        // Проверка, что пользователь не был авторизован
        $this->assertGuest();
    }
}