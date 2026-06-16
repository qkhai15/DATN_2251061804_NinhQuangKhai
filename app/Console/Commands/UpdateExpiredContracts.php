<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class UpdateExpiredContracts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'contracts:update-expired';

    /**
     * The console command description.
     */
    protected $description = 'Tự động cập nhật trạng thái hợp đồng đã hết hạn thành "expired" và trả phòng về trống.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today();

        // Tìm tất cả hợp đồng đang active nhưng đã qua ngày end_date
        $expiredContracts = Contract::with(['room', 'tenant'])
            ->where('status', 'active')
            ->where('end_date', '<', $today)
            ->get();

        if ($expiredContracts->isEmpty()) {
            $this->info('✅ Không có hợp đồng nào cần cập nhật.');
            return self::SUCCESS;
        }

        // Lấy tất cả admin để gửi thông báo
        $admins = User::where('role', 'admin')->get();

        $count = 0;
        foreach ($expiredContracts as $contract) {
            // Cập nhật trạng thái hợp đồng
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
                        'Hợp đồng của khách thuê "%s" tại phòng %s đã hết hạn vào ngày %s và được tự động chuyển sang trạng thái hết hạn. Phòng hiện đã trống.',
                        $contract->tenant->name ?? 'N/A',
                        $contract->room->room_number ?? 'N/A',
                        $contract->end_date->format('d/m/Y')
                    ),
                    'is_read' => 0,
                ]);
            }

            $this->line(sprintf(
                '  → Phòng %s | Khách: %s | Hết hạn: %s',
                $contract->room->room_number ?? 'N/A',
                $contract->tenant->name ?? 'N/A',
                $contract->end_date->format('d/m/Y')
            ));

            $count++;
        }

        $this->info("✅ Đã cập nhật {$count} hợp đồng hết hạn và gửi thông báo cho quản trị viên.");
        return self::SUCCESS;
    }
}
