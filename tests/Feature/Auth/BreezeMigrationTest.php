<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('registers users through breeze flow and marks them unapproved', function (): void {
    $response = $this->post('/register', [
        'name' => 'New User',
        'email' => 'new.user@company.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response
        ->assertRedirect('/login')
        ->assertSessionHas('status', 'Registration successful. Awaiting admin approval.');

    $this->assertDatabaseHas('users', [
        'email' => 'new.user@company.com',
        'is_approved' => 0,
    ]);
});

it('allows approved users to login and redirects to dashboard', function (): void {
    $user = User::factory()->create([
        'email' => 'approved.user@company.com',
        'password' => Hash::make('password123'),
        'is_approved' => true,
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

it('rejects unapproved users during login', function (): void {
    $user = User::factory()->create([
        'email' => 'pending.user@company.com',
        'password' => Hash::make('password123'),
        'is_approved' => false,
    ]);

    $response = $this->from('/login')->post('/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response
        ->assertRedirect('/login')
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('does not use legacy custom auth controller anymore', function (): void {
    expect(class_exists(\App\Http\Controllers\AuthController::class))->toBeFalse();
});
