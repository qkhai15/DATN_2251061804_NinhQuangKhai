<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'bank_name' => SystemSetting::get('bank_name', 'Vietcombank'),
            'bank_account_number' => SystemSetting::get('bank_account_number', '0123456789'),
            'bank_account_holder' => SystemSetting::get('bank_account_holder', 'NGUYEN VAN A'),
            'system_name' => SystemSetting::get('system_name', 'Quản lý Phòng trọ'),
            'contact_phone' => SystemSetting::get('contact_phone', '0987654321'),
            'openrouter_api_key' => SystemSetting::get('openrouter_api_key', ''),
            'openrouter_model' => SystemSetting::get('openrouter_model', 'deepseek/deepseek-r1:free'),
        ];
        
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($data['settings'] as $key => $value) {
            SystemSetting::set($key, $value);
        }

        return redirect()->route('settings.index')->with('success', 'Cập nhật cấu hình hệ thống thành công.');
    }
}
