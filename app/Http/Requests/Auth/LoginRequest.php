<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            // Registra hit nos DOIS rate limiters:
            // 1) email+ip: protege contra abuso por uma origem (limite mais baixo)
            // 2) email: protege a conta mesmo contra ataques distribuídos (limite maior)
            RateLimiter::hit($this->throttleKey(), 60 * 15);       // 15 min
            RateLimiter::hit($this->emailThrottleKey(), 60 * 60); // 1 h

            throw ValidationException::withMessages([
                'email' => 'E-mail ou senha incorretos.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        RateLimiter::clear($this->emailThrottleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        $ipLimited    = RateLimiter::tooManyAttempts($this->throttleKey(), 5);
        $emailLimited = RateLimiter::tooManyAttempts($this->emailThrottleKey(), 20);

        if (! $ipLimited && ! $emailLimited) {
            return;
        }

        event(new Lockout($this));

        // Mensagem genérica — não expõe segundos restantes nem qual limite foi atingido.
        // Isso evita enumeração de usuários e dá menos informação útil pro atacante.
        throw ValidationException::withMessages([
            'email' => 'Muitas tentativas de login. Tente novamente mais tarde.',
        ]);
    }

    /**
     * Rate limit key baseado em e-mail + IP (limite curto/baixo).
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }

    /**
     * Rate limit key baseado SÓ em e-mail — protege a conta mesmo contra
     * ataques distribuídos a partir de múltiplos IPs.
     */
    public function emailThrottleKey(): string
    {
        return 'login_email:'.Str::transliterate(Str::lower($this->string('email')));
    }
}
