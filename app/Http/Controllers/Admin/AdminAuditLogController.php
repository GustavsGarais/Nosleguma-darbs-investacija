<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = AdminAuditLog::query()
            ->with('admin:id,name,email')
            ->orderByDesc('id')
            ->paginate(40)
            ->withQueryString();

        return view('admin.audit.index', compact('logs'));
    }
}
