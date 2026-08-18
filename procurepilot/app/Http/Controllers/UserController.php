<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('organization_id', currentOrganization()->id)->latest()->get();
        $roles = User::roleOptions();

        return view('users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role'     => ['required', 'in:' . implode(',', array_keys(User::roleOptions()))],
            'title'    => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        abort_unless($request->role !== 'superadmin' || auth()->user()->isSuperAdmin(), 403, 'Only a Super Admin can grant the Super Admin role.');

        $user = User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'role'            => $request->role,
            'title'           => $request->title,
            'phone'           => $request->phone,
            'organization_id' => currentOrganization()->id,
        ]);

        AuditLog::record('user_created', $user, [], $user->toArray());

        return back()->with('success', 'Team member added.');
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->organization_id === currentOrganization()->id, 403);

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role'  => ['required', 'in:' . implode(',', array_keys(User::roleOptions()))],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        abort_unless($request->role !== 'superadmin' || auth()->user()->isSuperAdmin(), 403, 'Only a Super Admin can grant the Super Admin role.');

        $before = $user->toArray();

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
            'title' => $request->title,
            'phone' => $request->phone,
        ]);

        AuditLog::record('user_updated', $user, $before, $user->toArray());

        return back()->with('success', 'Team member updated.');
    }

    public function toggleActive(User $user)
    {
        abort_unless($user->organization_id === currentOrganization()->id, 403);
        abort_unless($user->id !== auth()->id(), 403, 'You cannot deactivate your own account.');

        $before = $user->toArray();
        $user->update(['is_active' => ! $user->is_active]);

        AuditLog::record('user_status_toggled', $user, $before, $user->toArray());

        return back()->with('success', 'Team member status updated.');
    }

    public function destroy(User $user)
    {
        abort_unless($user->organization_id === currentOrganization()->id, 403);
        abort_unless($user->id !== auth()->id(), 403, 'You cannot remove your own account.');

        AuditLog::record('user_removed', $user, $user->toArray(), []);
        $user->delete();

        return back()->with('success', 'Team member removed.');
    }
}
