<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', 'like', "%{$request->action}%");
        }
        if ($request->filled('module')) {
            $query->where('module', 'like', "%{$request->module}%");
        }

        $logs = $query->latest()->paginate(5);
        $users = User::orderBy('first_name')->get();

        return view('admin.logs', compact('logs', 'users'));
    }
}