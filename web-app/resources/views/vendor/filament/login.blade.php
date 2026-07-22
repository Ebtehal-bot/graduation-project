<form wire:submit.prevent="authenticate" class="space-y-8">
    {{ $this->form }}

    @if (session('register_success'))
        <div class="text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg p-3 text-center">
            تم إنشاء الحساب بنجاح. يمكنك الآن تسجيل الدخول باستخدام بريدك الإلكتروني وكلمة المرور.
        </div>
    @endif

    <x-filament::button type="submit" form="authenticate" class="w-full">
        {{ __('filament::login.buttons.submit.label') }}
    </x-filament::button>

    <div class="text-center">
        <a href="{{ route('filament.register') }}" class="text-sm text-primary-600 hover:text-primary-500 transition-colors duration-200">
            ليس لديك حساب؟ إنشاء حساب جديد
        </a>
    </div>
</form>
