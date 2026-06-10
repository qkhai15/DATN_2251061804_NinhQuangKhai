<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Room;
use App\Models\SystemSetting;
use App\Models\Service;
use App\Models\Contract;
use App\Models\Invoice;
use Carbon\Carbon;

class AIController extends Controller
{
    public function ask(Request $request)
    {
        $question = $request->input('question');
        $user = auth()->user();

        if (!$question) {
            return response()->json(['error' => 'Vui lòng nhập câu hỏi'], 400);
        }

        // Lấy thông tin cấu hình AI từ database
        $apiKey = SystemSetting::get('openrouter_api_key');
        $model = SystemSetting::get('openrouter_model', 'deepseek/deepseek-r1:free');

        if (!$apiKey) {
            return response()->json(['error' => 'Chưa cấu hình API Key cho AI'], 500);
        }

        // 1. Khởi tạo prompt cơ bản
        $context = "Bạn là trợ lý ảo của BoardingHub. Trả lời ngắn gọn, thân thiện.\n";
        $context .= "Thời gian hiện tại: " . Carbon::now()->format('d/m/Y H:i') . ".\n";
        $context .= "Người đang hỏi: {$user->name} (Vai trò: " . ($user->role == 'admin' ? 'Quản trị viên' : 'Khách thuê') . ").\n";

        // 2. Thông tin chung cho tất cả (Phòng trống & Dịch vụ)
        $availableRooms = Room::with('building')->where('status', 'empty')->get();
        if ($availableRooms->count() > 0) {
            $context .= "Danh sách phòng trống: ";
            foreach ($availableRooms as $r) {
                $context .= "Phòng {$r->room_number} ({$r->building->name}, " . number_format($r->price) . "đ, {$r->area}m2); ";
            }
            $context .= "\n";
        } else {
            $context .= "Hiện tại hệ thống không còn phòng trống.\n";
        }

        $services = Service::all();
        $context .= "Thông tin dịch vụ: ";
        foreach ($services as $s) {
            $context .= "{$s->name} (" . number_format($s->unit_price) . "đ/{$s->unit}); ";
        }
        $context .= "\n";

        // 3. Thông tin riêng theo vai trò
        if ($user->role == 'admin') {
            // Admin: Lấy tổng quan
            $totalRooms = Room::count();
            $rentedRooms = Room::where('status', 'rented')->count();
            $totalInvoices = Invoice::where('month', Carbon::now()->month)->count();
            $unpaidInvoices = Invoice::where('status', 'unpaid')->count();

            $context .= "--- DÀNH CHO ADMIN ---\n";
            $context .= "Tổng số phòng: {$totalRooms}, Đã thuê: {$rentedRooms}, Đang trống: " . ($totalRooms - $rentedRooms) . ".\n";
            $context .= "Hóa đơn tháng này: {$totalInvoices}. Số hóa đơn chưa thanh toán: {$unpaidInvoices}.\n";
        } else {
            // Tenant: Lấy thông tin hợp đồng và hóa đơn cá nhân
            $activeContract = Contract::with('room.building')
                ->where('tenant_id', $user->id)
                ->where('status', 'active')
                ->first();

            $context .= "--- DÀNH CHO KHÁCH THUÊ ---\n";
            if ($activeContract) {
                $room = $activeContract->room;
                $context .= "Bạn đang thuê: Phòng {$room->room_number} tại {$room->building->name}.\n";
                $context .= "Hợp đồng: Bắt đầu từ {$activeContract->start_date->format('d/m/Y')}, Hết hạn {$activeContract->end_date->format('d/m/Y')}. Giá thuê: " . number_format($activeContract->room_price) . "đ.\n";

                // Hóa đơn gần đây
                $recentInvoices = Invoice::where('contract_id', $activeContract->id)
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
                    ->limit(3)
                    ->get();

                if ($recentInvoices->count() > 0) {
                    $context .= "Lịch sử hóa đơn gần đây: ";
                    foreach ($recentInvoices as $inv) {
                        $status = $inv->status == 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán';
                        $context .= "Tháng {$inv->month}/{$inv->year} (" . number_format($inv->total_amount) . "đ, {$status}); ";
                    }
                    $context .= "\n";
                }
            } else {
                $context .= "Bạn hiện chưa có hợp đồng thuê phòng nào đang hoạt động.\n";
            }
        }

        $prompt = $context . "\nCâu hỏi: " . $question;

        // Gọi OpenRouter API
        $endpoint = 'https://openrouter.ai/api/v1/chat/completions';

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer $apiKey",
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'),
                'X-Title' => 'BoardingHub AI Chatbot'
            ])->timeout(45)->post($endpoint, [
                        'model' => $model,
                        'messages' => [
                            ['role' => 'user', 'content' => $prompt],
                        ],
                        'temperature' => 0.7,
                        'max_tokens' => 1500,
                    ]);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Không thể kết nối tới AI service',
                    'detail' => $response->body()
                ], 500);
            }

            $data = $response->json();
            $answer = $data['choices'][0]['message']['content'] ?? 'Xin lỗi, tôi không hiểu câu hỏi của bạn.';

            // Clean up DeepSeek reasoning tags
            $answer = preg_replace('/<think>.*?<\/think>/s', '', $answer);
            $answer = trim($answer);

            return response()->json(['answer' => $answer]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Có lỗi xảy ra khi kết nối AI',
                'detail' => $e->getMessage()
            ], 500);
        }
    }
}
