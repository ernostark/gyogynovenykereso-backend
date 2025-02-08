<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000'
        ]);

        $contact = Contact::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Köszönjük megkeresését! Hamarosan válaszolunk.'
        ]);
    }

    public function index()
    {
        $messages = Contact::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    public function markAsRead($id)
    {
        try {
            $message = Contact::findOrFail($id);
            $message->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Az üzenet olvasottként megjelölve'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a státusz módosítása során'
            ], 500);
        }
    }
}
