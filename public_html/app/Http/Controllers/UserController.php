<?php

    namespace App\Http\Controllers;

    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Validation\Rule;
    use Spatie\Permission\Models\Role;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Log; // For logging errors

    class UserController extends Controller
    {
        /**
         * Display a listing of the resource.
         * Corresponds to GET /users
         *
         * @return \Illuminate\View\View
         */
        public function index()
        {
            // This method is already implicitly handled by SettingController@index for your settings page
            // However, if you had a dedicated 'Users' page, this would be its primary method.
            // It's good practice to keep user listing separate from settings if they are large.
            // For consistency with your settings.index.blade.php, SettingController fetches users.
            // If you were to have a standalone users page, it would look like this:
            $users = User::all();
            return view('backend.users.index', compact('users')); // Assuming a separate users index view
        }

        /**
         * Show the form for creating a new resource.
         * Corresponds to GET /users/create
         *
         * @return \Illuminate\View\View
         */
        public function create()
        {
            $roles = Role::all();
            // This view would contain the form for creating a user.
            // In your current setup, this form is inside a modal on the settings page.
            // So, this method might not be directly called via a dedicated 'create' route in your UI,
            // but it's part of the resource convention.
            return view('backend.users.create', compact('roles'));
        }

        /**
         * Store a newly created resource in storage.
         * Corresponds to POST /users
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\RedirectResponse
         */
        public function store(Request $request)
        {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'role' => ['required', 'string', 'exists:roles,name'], // Validate that role exists
            ]);

            try {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    // 'last_login' will be null initially, updated on first login
                ]);

                // Assign role using Spatie Laravel Permission
                $user->assignRole($request->role);

                return redirect()->back()->with(['msg' => 'User created successfully!', 'type' => 'success']);
            } catch (\Throwable $e) {
                Log::error('Error creating user: ' . $e->getMessage());
                return redirect()->back()->with(['msg' => 'Failed to create user. Please try again.', 'type' => 'error'])->withInput();
            }
        }

        /**
         * Display the specified resource.
         * Corresponds to GET /users/{user}
         *
         * @param  \App\Models\User  $user
         * @return \Illuminate\View\View
         */
        public function show(User $user)
        {
            // This method would display details of a single user.
            return view('backend.users.show', compact('user'));
        }

        /**
         * Show the form for editing the specified resource.
         * Corresponds to GET /users/{user}/edit
         *
         * @param  \App\Models\User  $user
         * @return \Illuminate\View\View
         */
        public function edit(User $user)
        {
            $roles = Role::all();
            return view('backend.users.edit', compact('user', 'roles'));
        }

        /**
         * Update the specified resource in storage.
         * Corresponds to PUT/PATCH /users/{user}
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \App\Models\User  $user
         * @return \Illuminate\Http\RedirectResponse
         */
        public function update(Request $request, User $user)
        {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'], // Password is optional on update
                'role' => ['required', 'string', 'exists:roles,name'],
            ]);

            try {
                $user->name = $request->name;
                $user->email = $request->email;

                if ($request->filled('password')) {
                    $user->password = Hash::make($request->password);
                }
                $user->save();

                // Sync roles using Spatie Laravel Permission
                $user->syncRoles($request->role); // Syncs to the new role, removing old ones

                return redirect()->back()->with(['msg' => 'User updated successfully!', 'type' => 'success']);
            } catch (\Throwable $e) {
                Log::error('Error updating user ' . $user->id . ': ' . $e->getMessage());
                return redirect()->back()->with(['msg' => 'Failed to update user. Please try again.', 'type' => 'error'])->withInput();
            }
        }

        /**
         * Remove the specified resource from storage.
         * Corresponds to DELETE /users/{user}
         *
         * @param  \App\Models\User  $user
         * @return \Illuminate\Http\RedirectResponse
         */
        public function destroy(User $user)
        {
            try {
                $user->delete();
                return redirect()->back()->with(['msg' => 'User deleted successfully!', 'type' => 'success']);
            } catch (\Throwable $e) {
                Log::error('Error deleting user ' . $user->id . ': ' . $e->getMessage());
                return redirect()->back()->with(['msg' => 'Failed to delete user. Please try again.', 'type' => 'error']);
            }
        }
    }
