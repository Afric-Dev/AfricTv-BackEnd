<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\View\View;


class UsersController extends Controller
{
    public function index(Request $request)
    {
        $users = User::orderBy('created_at', 'desc')
                ->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function show($id) {
        $user = User::findOrFail($id);
        return view('admin.view-users', compact('user'));
    }

    public function updateUniqueId(Request $request, $id)
    {
        // Validate the new unique_id
        $request->validate([
            'unique_id' => 'required|string|unique:users,unique_id',
        ]);

        // Find the user by their ID
        $user = User::findOrFail($id);

        // Update the unique_id
        $user->unique_id = $request->input('unique_id');
        $user->save();

        return redirect()->back()->with('message', 'Great! User unique_id updated successfully.');
    }

    public function updateStatus($id)
    {
        // Find the user by ID
        $user = User::findOrFail($id);

        // Toggle the user's status
        $user->status = ($user->status == 'ACTIVE') ? 'BANNED' : 'ACTIVE';
        $user->save();

        // Return a response (redirect, or JSON response if using AJAX)
        return redirect()->back()->with('message', 'User status updated successfully');
    }
public function updateVerification($id, Request $request)
{
    // Find the user by ID
    $user = User::findOrFail($id);

    // Get the selected subscription status
    $status = $request->input('status');

    // Check if status is valid
    if (!in_array($status, ['FREE', 'MEDIUM', 'PREMIUM'])) {
        return redirect()->back()->with('error', 'Invalid status selection.');
    }

    // Update the user's subscription status
    $user->verification_status  = $status;

    // If the status is 'MEDIUM' or 'PREMIUM', add a verification icon to the name
    if ($status == 'MEDIUM') {
        $user->verification_status = 'VERIFIED';
        // Append verified icon to name
        $user->name = rtrim($user->name, '✔️ ⭐') . ' ✔️';  // Remove existing icon(s) and append the verified icon
    } elseif ($status == 'PREMIUM') {
        $user->verification_status = 'SUPER VERIFIED';
        // Append premium icon to name
        $user->name = rtrim($user->name, '✔️ ⭐') . ' ⭐';  // Remove existing icon(s) and append the premium icon
    } else {
        $user->verification_status = 'NON';
        // Remove icon if status is 'FREE'
        $user->name = rtrim($user->name, '✔️ ⭐');  // Remove any icon if the status is 'FREE'
    }

    // Save the user with the updated name and status
    $user->save();

    // Return success response
    return redirect()->back()->with('message', 'User verification status updated successfully');
}

}
