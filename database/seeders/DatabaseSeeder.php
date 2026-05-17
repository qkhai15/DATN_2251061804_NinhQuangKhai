<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Admin
        \App\Models\User::create([
            'name' => 'Quản trị viên',
            'email' => 'admin@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
            'phone' => '0988888888',
        ]);

        // Tenants
        $tenants = [
            ['name' => 'Nguyễn Văn A', 'email' => 'tenant1@example.com', 'phone' => '0912345678'],
            ['name' => 'Trần Thị B', 'email' => 'tenant2@example.com', 'phone' => '0923456789'],
            ['name' => 'Lê Văn C', 'email' => 'tenant3@example.com', 'phone' => '0934567890'],
            ['name' => 'Phạm Minh D', 'email' => 'tenant4@example.com', 'phone' => '0945678901'],
        ];

        $tenantModels = [];
        foreach ($tenants as $t) {
            $tenantModels[] = \App\Models\User::create([
                'name' => $t['name'],
                'email' => $t['email'],
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'tenant',
                'phone' => $t['phone'],
            ]);
        }

        // Buildings
        $buildings = [
            ['name' => 'Nhà trọ Bình Minh', 'address' => '45 Lê Lợi, Phường Bến Nghé, Quận 1, TP.HCM'],
            ['name' => 'Chung cư Mini Hoa Sen', 'address' => '123 Cách Mạng Tháng Tám, Quận 3, TP.HCM'],
        ];

        $buildingModels = [];
        foreach ($buildings as $b) {
            $buildingModels[] = \App\Models\Building::create([
                'name' => $b['name'],
                'address' => $b['address'],
                'description' => 'Khu trọ an ninh, sạch sẽ, đầy đủ tiện nghi.',
            ]);
        }

        // Rooms
        foreach ($buildingModels as $building) {
            for ($i = 1; $i <= 10; $i++) {
                $roomNum = $building->id . '0' . $i;
                \App\Models\Room::create([
                    'building_id' => $building->id,
                    'room_number' => $roomNum,
                    'price' => rand(2500000, 5000000),
                    'max_people' => rand(2, 4),
                    'status' => 'empty',
                    'area' => rand(20, 40),
                ]);
            }
        }

        // Services
        \App\Models\Service::create(['name' => 'Điện', 'unit_price' => 3500, 'unit' => 'kWh', 'description' => 'Tiền điện tính theo số ký']);
        \App\Models\Service::create(['name' => 'Nước', 'unit_price' => 15000, 'unit' => 'm3', 'description' => 'Tiền nước tính theo khối']);
        \App\Models\Service::create(['name' => 'Internet', 'unit_price' => 100000, 'unit' => 'tháng', 'description' => 'Phí internet cáp quang']);
        \App\Models\Service::create(['name' => 'Rác', 'unit_price' => 50000, 'unit' => 'tháng', 'description' => 'Phí thu gom rác']);

        // Contracts
        $contractModels = [];
        for ($i = 0; $i < 4; $i++) {
            $room = \App\Models\Room::where('status', 'empty')->first();
            $room->update(['status' => 'rented']);
            
            $contractModels[] = \App\Models\Contract::create([
                'room_id' => $room->id,
                'tenant_id' => $tenantModels[$i]->id,
                'start_date' => now()->subMonths(rand(1, 6)),
                'end_date' => now()->addMonths(rand(6, 12)),
                'deposit' => $room->price * 2,
                'room_price' => $room->price,
                'status' => 'active',
            ]);
        }

        // Issues
        \App\Models\Issue::create([
            'room_id' => $contractModels[0]->room_id,
            'user_id' => $tenantModels[0]->id,
            'title' => 'Hỏng vòi nước nhà vệ sinh',
            'description' => 'Vòi nước bị rò rỉ liên tục, mong quản lý cho người sửa sớm.',
            'status' => 'pending',
            'priority' => 'medium'
        ]);
        \App\Models\Issue::create([
            'room_id' => $contractModels[1]->room_id,
            'user_id' => $tenantModels[1]->id,
            'title' => 'Mất kết nối Internet',
            'description' => 'Wifi phòng không vào được từ tối qua.',
            'status' => 'fixing',
            'priority' => 'low'
        ]);

        // Meter Readings
        foreach ($contractModels as $contract) {
            \App\Models\MeterReading::create([
                'room_id' => $contract->room_id,
                'type' => 'electricity',
                'old_value' => 0,
                'new_value' => rand(100, 200),
                'read_date' => now()->subMonth(),
            ]);
            \App\Models\MeterReading::create([
                'room_id' => $contract->room_id,
                'type' => 'electricity',
                'old_value' => 200,
                'new_value' => 350,
                'read_date' => now(),
            ]);
            \App\Models\MeterReading::create([
                'room_id' => $contract->room_id,
                'type' => 'water',
                'old_value' => 0,
                'new_value' => rand(10, 20),
                'read_date' => now()->subMonth(),
            ]);
            \App\Models\MeterReading::create([
                'room_id' => $contract->room_id,
                'type' => 'water',
                'old_value' => 20,
                'new_value' => 35,
                'read_date' => now(),
            ]);
        }

        // Notifications
        \App\Models\Notification::create([
            'user_id' => $tenantModels[0]->id,
            'title' => 'Thông báo lịch thu tiền phòng',
            'content' => 'Chào bạn, từ tháng tới tiền phòng sẽ được thu vào mùng 5 hàng tháng qua hình thức chuyển khoản.',
            'is_read' => false
        ]);
        \App\Models\Notification::create([
            'user_id' => $tenantModels[1]->id,
            'title' => 'Lời nhắc bảo trì điện',
            'content' => 'Khu trọ sẽ tạm ngắt điện vào 9h sáng chủ nhật tuần này để bảo trì trạm biến áp.',
            'is_read' => true
        ]);

        // Invoices & Details
        foreach ($contractModels as $contract) {
            $invoice = \App\Models\Invoice::create([
                'contract_id' => $contract->id,
                'month' => date('m'),
                'year' => date('Y'),
                'total_amount' => $contract->room_price + 200000, // Room + some services
                'status' => rand(0, 1) ? 'paid' : 'unpaid',
            ]);

            // Add some details
            $electricity = \App\Models\Service::where('name', 'Điện')->first();
            \App\Models\InvoiceDetail::create([
                'invoice_id' => $invoice->id,
                'service_id' => $electricity->id,
                'name' => $electricity->name,
                'quantity' => 50,
                'unit_price' => $electricity->unit_price,
                'sub_total' => 50 * $electricity->unit_price,
            ]);
        }

        // Parking Cards
        foreach ($tenantModels as $tenant) {
            \App\Models\ParkingCard::create([
                'user_id' => $tenant->id,
                'card_number' => 'PC-' . rand(1000, 9999),
                'license_plate' => '29-' . chr(rand(65, 90)) . rand(100, 999) . '.' . rand(10, 99),
                'vehicle_type' => 'Xe máy',
            ]);
        }
    }
}
