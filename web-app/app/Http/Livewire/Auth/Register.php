<?php

namespace App\Http\Livewire\Auth;

use App\Models\Sponsor;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component implements HasForms
{
    use InteractsWithForms;

    public $name = '';
    public $email = '';
    public $phone = '';
    public $password = '';
    public $password_confirmation = '';

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('الاسم الكامل')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label('البريد الإلكتروني')
                ->email()
                ->required()
                ->unique(User::class, 'email'),
            TextInput::make('phone')
                ->label('رقم الهاتف')
                ->required()
                ->maxLength(20)
                ->tel(),
            TextInput::make('password')
                ->label('كلمة المرور')
                ->password()
                ->required()
                ->minLength(8)
                ->confirmed(),
            TextInput::make('password_confirmation')
                ->label('تأكيد كلمة المرور')
                ->password()
                ->required(),
        ];
    }

    public function register()
    {
        $data = $this->form->getState();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role' => 'sponsor',
        ]);

        $sponsor = Sponsor::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);

        $user->sponsor_id = $sponsor->id;
        $user->save();

        session()->flash('register_success', true);

        return redirect()->route('filament.auth.login');
    }

    public function render(): View
    {
        return view('livewire.auth.register')
            ->layout('filament::components.layouts.card', [
                'title' => 'إنشاء حساب كفيل جديد',
            ]);
    }
}
