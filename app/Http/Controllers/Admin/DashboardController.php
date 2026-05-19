<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Issue;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Tự động cập nhật hợp đồng hết hạn mỗi khi admin vào dashboard
        // Cache 1 giờ để không query nặng mỗi lần refresh
        $this->autoUpdateExpiredContracts();

        $totalRooms  = Room::count();
        $rentedRooms = Room::where('status', 'rented')->count();
        $emptyRooms  = Room::where('status', 'empty')->count();

        $stats = [
            'total_rooms'              => $totalRooms,
            'empty_rooms'              => $emptyRooms,
            'rented_rooms'             => $rentedRooms,
            'occupancy_rate'           => $totalRooms > 0 ? ($rentedRooms / $totalRooms) * 100 : 0,
            'pending_revenue'          => Invoice::where('status', 'unpaid')->sum('total_amount'),
            'active_issues'            => Issue::where('status', '!=', 'resolved')->count(),
            'expiring_contracts_count' => Contract::where('status', 'active')
                                            ->where('end_date', '>=', now()->startOfDay())
                                            ->where('end_date', '<=', now()->addDays(30))
                                            ->count(),
            'expired_contracts_count'  => Contract::where('status', 'active')
                                            ->where('end_date', '<', now()->startOfDay())
                                            ->count(),
        ];

        $recentInvoices = Invoice::with(['contract.room', 'contract.tenant'])->latest()->take(5)->get();
        $recentTenants  = Contract::with(['tenant', 'room'])->where('status', 'active')->latest()->take(5)->get();

        $expiringContracts = Contract::with(['tenant', 'room'])
            ->where('status', 'active')
            ->where('end_date', '>=', now()->startOfDay())
            ->where('end_date', '<=', now()->addDays(30))
            ->orderBy('end_date')
            ->get();

        $expiredContracts = Contract::with(['tenant', 'room'])
            ->where('status', 'active')
            ->where('end_date', '<', now()->startOfDay())
            ->orderBy('end_date')
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'recentInvoices', 'recentTenants',
            'expiringContracts', 'expiredContracts'
        ));
    }

    /**
     * Tự động cập nhật hợp đồng hết hạn.
     * Chạy mỗi khi admin vào Dashboard, nhưng cache 1 giờ để tránh query liên tục.
     */
    private function autoUpdateExpiredContracts(): void
    {
        $cacheKey = 'last_expired_contract_check';

        // Nếu đã kiểm tra trong vòng 1 giờ qua thì bỏ qua
        if (Cache::has($cacheKey)) {
            return;
        }

        $today = Carbon::today();

        $expiredContracts = Contract::with(['room', 'tenant'])
            ->where('status', 'active')
            ->where('end_date', '<', $today)
            ->get();

        if ($expiredContracts->isNotEmpty()) {
            $admins = User::where('role', 'admin')->get();

            foreach ($expiredContracts as $contract) {
                // Cập nhật trạng thái hợp đồng → expired
                $contract->update(['status' => 'expired']);

                // Trả phòng về trống
                if ($contract->room) {
                    $contract->room->update(['status' => 'empty']);
                }

                // Gửi thông báo cho từng admin
                foreach ($admins as $admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'title'   => '⚠️ Hợp đồng hết hạn: Phòng ' . ($contract->room->room_number ?? 'N/A'),
                        'content' => sprintf(
                            'Hợp đồng của khách thuê "%s" tại phòng %s đã hết hạn vào ngày %s. Phòng đã được trả về trạng thái trống.',
                            $contract->tenant->name ?? 'N/A',
                            $contract->room->room_number ?? 'N/A',
                            $contract->end_date->format('d/m/Y')
                        ),
                        'is_read' => 0,
                    ]);
                }
            }
        }

        // Đánh dấu đã kiểm tra, cache trong 1 giờ
        Cache::put($cacheKey, true, now()->addHour());
    }
}
