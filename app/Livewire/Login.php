<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class Login extends Component
{
    public $email;
    public $password;

    public function render()
    {
        return view('livewire.login');
    }

    public function login()
    {
        $user = User::where('email', $this->email)->first();

        if ($user && password_verify($this->password, $user->password)) {
            // Autenticación exitosa
            // Aquí puedes realizar las acciones necesarias, como establecer una sesión o redirigir al usuario a una página específica
            return redirect()->route('tpv.blade.php');
        } else {
            // Autenticación fallida
            // Aquí puedes mostrar un mensaje de error o realizar otras acciones necesarias
            session()->flash('error', 'Usuario o contraseña incorrectos');
            return redirect()->back();
        }
    }
}