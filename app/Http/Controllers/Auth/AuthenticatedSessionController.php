<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Traits\token;

class AuthenticatedSessionController extends Controller
{
    use token;
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post('http://apicoders.test/v1/login', [
            'email' => $request->email,
            'password' => $request->password
        ]);

        if ($response->status() == 404) {
            return back()->withErrors('These credentials do not match our records.');

        }

        //return $response['data'];
        $service = $response->json();

        $user = User::updateOrCreate([
            'email' => $request->email
        ],$service['data']);

        if (!$user->accessToken) {

            $this->getAcessToken($user, $service);

        }


        Auth::login($user, $request->remember);

        return redirect()->intended(RouteServiceProvider::HOME);
        //return $user;

        //return $response->json();

        /*
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
        */

    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
