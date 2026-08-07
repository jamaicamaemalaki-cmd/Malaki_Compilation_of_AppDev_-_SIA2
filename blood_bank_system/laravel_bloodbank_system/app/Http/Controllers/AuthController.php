<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\MedicalFacility;
use App\Models\User;
use App\Services\FacilityOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login', [
            'on_login_page' => true,
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        $request->session()->regenerate();
        $request->session()->put('user_id', $user->id);
        $request->session()->put('role', $user->role);
        $request->session()->put('name', $user->name);

        return redirect()->route('dashboard')->with('success', 'Welcome back, '.$user->name.'.');
    }

    public function chooseRole()
    {
        return view('auth.choose-role');
    }

    public function showRegister(string $role)
    {
        abort_unless(in_array($role, ['donor', 'facility', 'admin']), 404);

        return view('auth.register', [
            'role' => $role,
            'facilities' => FacilityOptions::all(),
        ]);
    }

    public function register(Request $request, string $role)
    {
        abort_unless(in_array($role, ['donor', 'facility', 'admin']), 404);

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
        ];

        if ($role === 'donor') {
            $rules += [
                'blood_type' => ['required', 'string', 'max:5'],
                'age' => ['required', 'integer', 'min:18', 'max:65'],
                'gender' => ['required', 'string', 'max:20'],
                'weight' => ['nullable', 'numeric', 'min:40'],
                'declaration_confirmed' => ['accepted'],
            ];
        }

        if ($role === 'facility') {
            $rules += [
                'facility_category' => ['required', 'in:Hospital,Rural Health Unit,Red Cross'],
                'facility_name' => ['required', 'in:'.implode(',', FacilityOptions::names())],
                'license_number' => ['nullable', 'string', 'max:80'],
                'contact_person' => ['nullable', 'string', 'max:120'],
            ];
        }

        $data = $request->validate($rules);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $role,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        if ($role === 'donor') {
            Donor::create([
                'user_id' => $user->id,
                'blood_type' => $data['blood_type'],
                'age' => $data['age'],
                'gender' => $data['gender'],
                'weight' => $data['weight'] ?? null,
                'declaration_confirmed' => true,
                'declaration_confirmed_at' => now(),
            ]);
        }

        if ($role === 'facility') {
            MedicalFacility::create([
                'user_id' => $user->id,
                'facility_category' => $data['facility_category'],
                'facility_name' => $data['facility_name'],
                'license_number' => $data['license_number'] ?? null,
                'contact_person' => $data['contact_person'] ?? null,
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put('user_id', $user->id);
        $request->session()->put('role', $user->role);
        $request->session()->put('name', $user->name);

        return redirect()->route('dashboard')->with('success', 'Account created successfully.');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}
