<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScheduledEmail;
use App\Jobs\SendScheduledEmail;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ScheduledEmailController extends Controller
{
    public function __construct()
    {
        // Apply rate limiting middleware
        $this->middleware('throttle:10,1')->only(['scheduleEmail', 'updateScheduledEmail', 'deleteScheduledEmail']);
    }

    // Scheduled email
    public function scheduleEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipient_email' => 'required|email',
            'subject' => 'required|string',
            'body' => 'required|string',
            'send_at' => 'required|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        $existingEmail = ScheduledEmail::where('user_id', $user->id)
            ->where('recipient_email', $request->recipient_email)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();

        if ($existingEmail) {
            return response()->json([
                'status' => false,
                'message' => 'Duplicate email scheduling is not allowed within 5 minutes.'
            ], 400);
        }

        $scheduledEmail = ScheduledEmail::create([
            'user_id' => $user->id,
            'recipient_email' => $request->recipient_email,
            'subject' => $request->subject,
            'body' => $request->body,
            'status' => 'pending',
            'send_at' => $request->send_at,
        ]);

        dispatch(new SendScheduledEmail($scheduledEmail))->delay($scheduledEmail->send_at);

        return response()->json([
            'status' => true,
            'message' => 'Email scheduled successfully',
            'data' => $scheduledEmail
        ], 201);
    }

    public function scheduleEmailUsingTemplate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'recipient_email' => 'required|email',
            'template_id' => 'required|exists:email_templates,id',
            'send_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 422);
        }


        // set subject & body from the template
        $template = EmailTemplate::find($request->template_id);

        $data['recipient_email'] = $request->recipient_email;
        $data['send_at'] = $request->send_at;
        $data['subject'] = $template->subject;
        $data['body'] = $template->body;
        $data['template_id'] = $template->id;
        $data['user_id'] = Auth::id();


        $scheduledEmail = ScheduledEmail::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Email scheduled successfully',
            'data' => $scheduledEmail,
        ]);
    }

    // List scheduled emails with pagination and email status(optional) (pending, sent and failed).
    public function listScheduledEmails(Request $request)
    {
        $status = $request->query('status');

        $emails = ScheduledEmail::where('user_id', Auth::id())
            ->when($status, function ($query) use ($status) {
                if ($status === 'failed') {
                    $query->whereIn('status', ['failed', 'permanent_failed']);
                } else {
                    $query->where('status', $status);
                }
            })
            ->orderBy('send_at', 'asc')
            ->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $emails
        ]);
    }


    // Update scheduled email (only if pending)
    public function updateScheduledEmail(Request $request, $id)
    {
        $email = ScheduledEmail::where(
            'id',
            $id
        )->where(
            'user_id',
            Auth::id()
        )->where(
            'status',
            'pending'
        )->first();
        if (!$email) {
            return response()->json([
                'status' => false,
                'message' => 'Email not found or already sent.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'recipient_email' => 'required|email',
            'subject' => 'required|string',
            'body' => 'required|string',
            'send_at' => 'required|date|after:now'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $email->update($request->only([
            'recipient_email',
            'subject',
            'body',
            'send_at'
        ]));
        return response()->json([
            'status' => true,
            'message' => 'Scheduled email updated successfully.'
        ]);
    }

    // Delete scheduled email (only if pending)
    public function deleteScheduledEmail($id)
    {
        $email = ScheduledEmail::where(
            'id',
            $id
        )->where(
            'user_id',
            Auth::id()
        )->where(
            'status',
            'pending'
        )->first();
        if (!$email) {
            return response()->json([
                'status' => false,
                'message' => 'Email not found or already sent.'
            ], 404);
        }

        $email->delete();
        return response()->json([
            'status' => true,
            'message' => 'Scheduled email deleted successfully.'
        ]);
    }
}
