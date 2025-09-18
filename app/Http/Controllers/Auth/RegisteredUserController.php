<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Roles;
use App\Models\User;
use App\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Log::info("validated: ", [$validated]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);


        Log::info("Users: ", [$user]);

        return back()->with([
            'success' => 'User Added Successfully',
            'user' => $user
        ]);
    }

    public function renderAdminUserPanel(Request $request)
    {
        $searchQuery = $request->query('search');
        $users = User::select(
            'users.id',
            'users.name',
            'users.email',
            'users.created_at',
            'roles.name as role_name',
            'roles.id as role_id'
        )
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->where('users.id', '!=', Auth::user()->id) 
            ->when($searchQuery, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('users.name', 'LIKE', "%{$search}%")
                    ->orWhere('users.email', 'LIKE', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        $roles = Roles::select('id', 'name')->get();

        Log::info("users: ", [$users]);

        return Inertia::render('Admin/User', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        // ✅ Validation
        $validated = $request->validated();

        // ✅ Update user fields
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role_id = $validated['role_id'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        return redirect()->back()->with('success', 'User updated successfully.');
    }


    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }
}
