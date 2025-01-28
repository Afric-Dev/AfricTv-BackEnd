<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewsletterMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class NewsletterController extends Controller
{
    public function index()
    {
        return view('admin.sendmail');
    }


  public function sendMail(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);


        // Get all users from the database
        $users = User::all();

        // Loop through each user and send the email
        foreach ($users as $user) {
            Mail::to($user->email)->send(new NewsletterMail($user, $request->subject, $request->content));
        }

        // Return a success message or redirect as needed
        return redirect()->back()->with('message', 'Email sent to all users.');
    }
}
