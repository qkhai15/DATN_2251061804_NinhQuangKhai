<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - BoardingHouse</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="bg-[#f8fafc] min-h-screen flex items-center justify-center p-6 relative overflow-hidden">
    <!-- Decorative elements -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-100 rounded-full blur-[120px] opacity-50"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-rose-100 rounded-full blur-[120px] opacity-50"></div>

    <div class="max-w-[480px] w-full relative z-10">
        <div class="glass rounded-[3rem] shadow-2xl shadow-indigo-100/50 overflow-hidden border border-white">
            <div class="bg-gray-900 p-12 text-center text-white relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
                <div class="relative z-10">
                    <div class="w-20 h-20 bg-white/10 backdrop-blur-md rounded-3xl flex items-center justify-center mx-auto mb-6 border border-white/20">
                        <i class="fas fa-house-chimney-window text-3xl text-indigo-400"></i>
                    </div>
                    <h1 class="text-4xl font-black tracking-tight mb-2 uppercase">BoardingHouse</h1>
                    <p class="text-gray-400 font-medium text-sm">Chào mừng bạn quay lại! Vui lòng đăng nhập.</p>
                </div>
            </div>
            
            <div class="p-12">
                @if($errors->any())
                <div class="mb-8 bg-rose-50 border-l-4 border-rose-500 p-5 text-rose-700 rounded-2xl animate-shake">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span class="font-bold text-sm">Có lỗi xảy ra:</span>
                    </div>
                    <ul class="text-xs space-y-1 font-medium opacity-90">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-8">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Địa chỉ Email</label>
                        <div class="relative group">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-600 transition-colors">
                                <i class="far fa-envelope"></i>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full pl-14 pr-6 py-5 bg-gray-50/50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none text-sm transition-all font-bold text-gray-800" placeholder="TenCuaBan@email.com">
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Mật khẩu</label>
                            <a href="#" class="text-[10px] font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-widest">Quên mật khẩu?</a>
                        </div>
                        <div class="relative group">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-600 transition-colors">
                                <i class="fas fa-lock text-sm"></i>
                            </span>
                            <input type="password" name="password" required class="w-full pl-14 pr-6 py-5 bg-gray-50/50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none text-sm transition-all font-bold text-gray-800" placeholder="••••••••">
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" name="remember" class="sr-only peer">
                                <div class="w-5 h-5 bg-gray-100 border border-gray-200 rounded-md peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all"></div>
                                <i class="fas fa-check absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-[10px] text-white opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                            </div>
                            <span class="ml-3 text-sm font-bold text-gray-500 group-hover:text-gray-700 transition-colors">Ghi nhớ đăng nhập</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-gray-900 text-white font-black py-6 rounded-3xl hover:bg-indigo-600 transition-all shadow-2xl shadow-indigo-200/50 flex items-center justify-center gap-3 group">
                        <span class="uppercase tracking-[0.2em]">Đăng nhập ngay</span>
                        <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </button>
                    
                    <div class="text-center pt-8 border-t border-gray-50 mt-10">
                        <p class="text-gray-400 font-bold text-sm">
                            Chưa có tài khoản? 
                            <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-800 ml-1 transition-colors">Đăng ký thành viên</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
        
        <p class="text-center text-gray-400 text-[10px] mt-10 font-bold uppercase tracking-[0.3em]">© 2026 BOARDINGHUB MANAGEMENT SYSTEM</p>
    </div>
</body>
</html>
