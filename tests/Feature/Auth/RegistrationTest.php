<?php

use Illuminate\Support\Facades\Auth;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('admin adds new user', function () {
    $response = $this->post(route('admin.user.add'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'role_id' => 1,
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();

    $authenticatedUserRoleID = auth()->user()->role_id;
    $response->assertRedirect(Auth::user()->getIndexRoute($authenticatedUserRoleID));
});
