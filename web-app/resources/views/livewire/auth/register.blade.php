<form wire:submit.prevent="register" class="space-y-8">
    {{ $this->form }}

    <x-filament::button type="submit" form="register" class="w-full">
        إنشاء حساب
    </x-filament::button>

    <div class="text-center">
        <a href="{{ route('filament.auth.login') }}" class="text-sm text-primary-600 hover:text-primary-500 transition-colors duration-200">
            لديك حساب بالفعل؟ تسجيل الدخول
        </a>
    </div>
</form>
