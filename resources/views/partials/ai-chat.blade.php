<!-- AI Chat Component -->
<div id="ai-chat-wrapper" class="fixed bottom-6 right-6 z-[9999] flex flex-col items-end">
    <!-- Chat Window -->
    <div id="ai-chat-window" class="hidden mb-4 w-[380px] h-[550px] bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 flex flex-col overflow-hidden transition-all duration-500 scale-90 opacity-0 origin-bottom-right">
        <!-- Header -->
        <div class="bg-slate-900 p-6 text-white flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <i class="fas fa-robot text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm leading-tight">BoardingHub AI</h3>
                    <div class="flex items-center space-x-1">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Trực tuyến</span>
                    </div>
                </div>
            </div>
            <button id="close-chat" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-white/10 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Messages Area -->
        <div id="chat-messages" class="flex-1 p-6 overflow-y-auto custom-scrollbar space-y-4 bg-gray-50/50">
            <!-- Bot Welcome Message -->
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 shrink-0">
                    <i class="fas fa-robot text-xs"></i>
                </div>
                <div class="bg-white p-4 rounded-2xl rounded-tl-none shadow-sm border border-gray-100 max-w-[85%]">
                    <p class="text-sm text-gray-700 leading-relaxed">Xin chào! Tôi là trợ lý ảo <strong>BoardingHub</strong>. Tôi có thể giúp gì cho bạn về thông tin phòng trọ?</p>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-4 bg-white border-t border-gray-100">
            <div id="typing-indicator" class="hidden mb-3 px-2">
                <div class="flex items-center space-x-2 text-indigo-500">
                    <div class="flex space-x-1">
                        <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce"></span>
                        <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                        <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.4s"></span>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest">AI đang trả lời...</span>
                </div>
            </div>
            <form id="ai-chat-form" class="relative flex items-center">
                <input type="text" id="chat-input" placeholder="Nhập câu hỏi của bạn..." 
                    class="w-full pl-5 pr-12 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none font-medium text-gray-700 transition-all text-sm">
                <button type="submit" class="absolute right-2 w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center hover:bg-black transition-all shadow-lg shadow-indigo-100 active:scale-95">
                    <i class="fas fa-paper-plane text-xs"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Toggle Button -->
    <button id="toggle-chat" class="w-16 h-16 bg-indigo-600 text-white rounded-2xl shadow-2xl shadow-indigo-200 flex items-center justify-center hover:bg-black transition-all group relative overflow-hidden active:scale-95">
        <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
        <i class="fas fa-comment-dots text-2xl relative z-10 group-hover:hidden"></i>
        <i class="fas fa-times text-2xl relative z-10 hidden group-hover:block"></i>
    </button>
</div>

<style>
    #ai-chat-window.show {
        display: flex;
        transform: scale(1);
        opacity: 1;
    }
    .user-message {
        align-self: flex-end;
        background: #4f46e5;
        color: white;
        padding: 12px 16px;
        border-radius: 20px 20px 0 20px;
        max-width: 85%;
        margin-left: auto;
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.2);
        font-size: 14px;
        line-height: 1.5;
    }
    .bot-message {
        align-self: flex-start;
        background: white;
        color: #374151;
        padding: 12px 16px;
        border-radius: 20px 20px 20px 0;
        max-width: 85%;
        border: 1px solid #f3f4f6;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        font-size: 14px;
        line-height: 1.5;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('toggle-chat');
        const closeBtn = document.getElementById('close-chat');
        const chatWindow = document.getElementById('ai-chat-window');
        const chatForm = document.getElementById('ai-chat-form');
        const chatInput = document.getElementById('chat-input');
        const messagesArea = document.getElementById('chat-messages');
        const typingIndicator = document.getElementById('typing-indicator');

        function toggleChat() {
            if (chatWindow.classList.contains('hidden')) {
                chatWindow.classList.remove('hidden');
                setTimeout(() => chatWindow.classList.add('show'), 10);
            } else {
                chatWindow.classList.remove('show');
                setTimeout(() => chatWindow.classList.add('hidden'), 500);
            }
        }

        toggleBtn.addEventListener('click', toggleChat);
        closeBtn.addEventListener('click', toggleChat);

        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const question = chatInput.value.trim();
            if (!question) return;

            // Add user message
            addMessage(question, 'user');
            chatInput.value = '';
            
            // Show typing indicator
            typingIndicator.classList.remove('hidden');
            messagesArea.scrollTop = messagesArea.scrollHeight;

            try {
                const response = await fetch('{{ route("ai.ask") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ question: question })
                });

                const data = await response.json();
                
                // Hide typing indicator
                typingIndicator.classList.add('hidden');

                if (data.answer) {
                    addMessage(data.answer, 'bot');
                } else if (data.error) {
                    addMessage('Lỗi: ' + data.error, 'bot');
                }
            } catch (error) {
                typingIndicator.classList.add('hidden');
                addMessage('Không thể kết nối tới máy chủ.', 'bot');
            }
        });

        function addMessage(text, type) {
            const msgDiv = document.createElement('div');
            msgDiv.className = type === 'user' ? 'user-message' : 'bot-message';
            
            if (type === 'bot') {
                const wrapper = document.createElement('div');
                wrapper.className = 'flex items-start space-x-3';
                wrapper.innerHTML = `
                    <div class="w-8 h-8 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 shrink-0">
                        <i class="fas fa-robot text-xs"></i>
                    </div>
                    <div class="bg-white p-4 rounded-2xl rounded-tl-none shadow-sm border border-gray-100 max-w-[85%]">
                        <p class="text-sm text-gray-700 leading-relaxed">${text.replace(/\n/g, '<br>')}</p>
                    </div>
                `;
                messagesArea.appendChild(wrapper);
            } else {
                msgDiv.textContent = text;
                messagesArea.appendChild(msgDiv);
            }
            
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }
    });
</script>
