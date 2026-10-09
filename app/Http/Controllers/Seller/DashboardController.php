<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $seller = $request->user()->sellerProfile;

        // Balance
        $balanceRecord = $seller->balance;
        $activeBalance = $balanceRecord ? $balanceRecord->balance : 0;
        $pendingBalance = $balanceRecord ? $balanceRecord->pending_balance : 0;

        // Total sales revenue from completed or delivered order items
        $totalSales = OrderItem::where('seller_id', $seller->id)
            ->whereIn('delivery_status', ['delivered', 'completed'])
            ->sum('seller_earning');

        $totalOrdersCount = OrderItem::where('seller_id', $seller->id)->count();

        // Count products by status
        $productCounts = [
            'total' => $seller->products()->count(),
            'published' => $seller->products()->where('status', ProductStatus::PUBLISHED)->count(),
            'pending_review' => $seller->products()->where('status', ProductStatus::PENDING_REVIEW)->count(),
            'draft' => $seller->products()->where('status', ProductStatus::DRAFT)->count(),
            'rejected' => $seller->products()->where('status', ProductStatus::REJECTED)->count(),
        ];

        // Recent 5 order items
        $recentOrders = OrderItem::with(['order.buyer', 'variant.product'])
            ->where('seller_id', $seller->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('seller.dashboard', compact(
            'seller',
            'activeBalance',
            'pendingBalance',
            'totalSales',
            'totalOrdersCount',
            'productCounts',
            'recentOrders'
        ));
    }
}
