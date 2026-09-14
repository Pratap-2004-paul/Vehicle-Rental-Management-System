<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    // POST /api/contact
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        ContactMessage::create($validator->validated());

        return response()->json(['message' => 'Thanks! Your message has been received.'], 201);
    }

    // GET /api/admin/contact-messages (admin only)
    public function index()
    {
        return response()->json(ContactMessage::orderBy('created_at', 'desc')->get());
    }
}