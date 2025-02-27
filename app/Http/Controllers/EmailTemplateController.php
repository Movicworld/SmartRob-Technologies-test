<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailTemplateController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'templates' => Auth::user()->emailTemplates
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $template = Auth::user()->emailTemplates()->create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Template saved successfully',
            'template' => $template
        ]);
    }

    public function destroy(EmailTemplate $template)
    {
        if ($template->user_id !== Auth::id()) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $template->delete();

        return response()->json(['status' => true, 'message' => 'Template deleted successfully']);
    }
}
