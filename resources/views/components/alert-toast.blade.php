{{-- 
    Alert Toast Component
    Menampilkan notifikasi session('success'), session('error'), session('success_cart'), dan $errors secara otomatis.
    Cukup include <x-alert-toast /> di layout, semua halaman otomatis dapat notifikasi.
--}}

@if(session('success') || session('success_cart') || session('error') || (isset($errors) && $errors->any()))
<div x-data="alertToast()" x-init="startAutoHide()" class="fixed top-6 left-1/2 -translate-x-1/2 z-[9999] w-full max-w-md px-4 pointer-events-none">
    <template x-for="(toast, index) in toasts" :key="index">
        <div x-show="toast.visible"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
             class="mb-3 flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-xl border backdrop-blur-sm pointer-events-auto cursor-pointer"
             :class="toast.type === 'success' 
                 ? 'bg-green-50/95 border-green-200 text-green-800' 
                 : 'bg-red-50/95 border-red-200 text-red-800'"
             @click="dismiss(index)">
            
            {{-- Icon --}}
            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"
                 :class="toast.type === 'success' ? 'bg-green-100' : 'bg-red-100'">
                <i class="ph-fill text-lg"
                   :class="toast.type === 'success' ? 'ph-check-circle text-green-600' : 'ph-warning-circle text-red-600'"></i>
            </div>

            {{-- Message --}}
            <p class="text-sm font-semibold flex-1 leading-snug" x-text="toast.message"></p>

            {{-- Close Button --}}
            <button @click.stop="dismiss(index)" class="shrink-0 p-1 rounded-full hover:bg-black/5 transition-colors">
                <i class="ph ph-x text-sm opacity-50"></i>
            </button>
        </div>
    </template>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('alertToast', () => ({
            toasts: [],

            init() {
                @if(session('success'))
                    this.toasts.push({ type: 'success', message: @json(session('success')), visible: true });
                @endif

                @if(session('success_cart'))
                    this.toasts.push({ type: 'success', message: @json(session('success_cart')), visible: true });
                @endif

                @if(session('error'))
                    this.toasts.push({ type: 'error', message: @json(session('error')), visible: true });
                @endif

                @if(isset($errors) && $errors->any())
                    @foreach($errors->all() as $error)
                        this.toasts.push({ type: 'error', message: @json($error), visible: true });
                    @endforeach
                @endif
            },

            startAutoHide() {
                this.toasts.forEach((toast, index) => {
                    setTimeout(() => { this.dismiss(index); }, 5000 + (index * 500));
                });
            },

            dismiss(index) {
                if (this.toasts[index]) {
                    this.toasts[index].visible = false;
                }
            }
        }));
    });
</script>
@endif
