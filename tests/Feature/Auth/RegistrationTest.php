<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test.user@company.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertGuest();
    $response
        ->assertRedirect(route('login', absolute: false))
        ->assertSessionHas('status', 'Registration successful. Awaiting admin approval.');

    expect(User::where('email', 'test.user@company.com')->value('is_approved'))->toBeFalse();
});
