<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        $tenants = User::where('role', 'tenant')->get();
        return view('admin.notifications.create', compact('tenants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'recipient_type' => 'required|in:all,specific',
            'user_id' => 'required_if:recipient_type,specific|exists:users,id',
        ]);

        if ($request->recipient_type === 'all') {
            $tenants = User::where('role', 'tenant')->get();
            foreach ($tenants as $tenant) {
                Notification::create([
                    'user_id' => $tenant->id,
                    'title' => $request->title,
                    'content' => $request->content,
                ]);
            }
        } else {
            Notification::create([
                'user_id' => $request->user_id,
                'title' => $request->title,
                'content' => $request->content,
            ]);
        }

        return redirect()->route('admin.notifications.index')->with('success', 'Thông báo đã được gửi thành công.');
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return redirect()->route('admin.notifications.index')->with('success', 'Thông báo đã được xóa.');
    }
}
