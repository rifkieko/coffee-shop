<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        if ($user?->role === UserRole::Admin) {
            return view('admin.dashboard', [
                'menuCount' => MenuItem::count(),
                'categoryCount' => Category::count(),
                'pendingOrdersCount' => Order::where('status', OrderStatus::Pending)->count(),
                'latestOrders' => Order::with(['user'])
                    ->latest()
                    ->limit(5)
                    ->get(),
                'notifications' => $user->notifications()->latest()->limit(8)->get(),
                'unreadNotificationCount' => $user->unreadNotifications()->count(),
            ]);
        }

        return redirect()->route('home');
    }
}
