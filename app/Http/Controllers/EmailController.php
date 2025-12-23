<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Email as TestEmail;

class EmailController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        Mail::to($request->input('email'))->send(new TestEmail());

        return response()->json(['message' => 'Email sent!']);
    }
}
