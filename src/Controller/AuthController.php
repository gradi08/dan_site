<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Validator;
use App\Http\Request;
use App\Http\Response;
use App\Support\Phone;

final class AuthController extends Controller
{
    public function loginForm(Request $request): Response
    {
        return $this->view('pages/auth/login');
    }

    public function login(Request $request): Response
    {
        $data = $request->only(['email', 'password']);
        $validator = Validator::make($data, [
            'email'    => 'required|email',
            'password' => 'required',
        ], ['email' => 'E-mail', 'password' => 'Mot de passe']);

        if ($validator->fails()) {
            return $this->backWithErrors('connexion', $validator->errors(), $data);
        }

        $throttle = $this->app->throttle();
        $ip = $request->ip();

        if ($throttle->tooManyAttempts($data['email'], $ip)) {
            return $this->backWithErrors('connexion', [
                'email' => sprintf('Trop de tentatives. Réessayez dans %d minutes.', $throttle->minutesRemaining()),
            ], $data);
        }

        $user = $this->app->auth()->attempt($data['email'], $data['password']);
        if ($user === null) {
            $throttle->hit($data['email'], $ip);

            return $this->backWithErrors('connexion', [
                'email' => 'E-mail ou mot de passe incorrect.',
            ], $data);
        }

        $throttle->clear($data['email'], $ip);
        $this->app->auth()->login((int) $user['id']);

        $intended = $this->app->session()->get('intended');
        $this->app->session()->forget('intended');

        return $this->redirect(is_string($intended) ? $intended : 'mes-annonces');
    }

    public function registerForm(Request $request): Response
    {
        return $this->view('pages/auth/register');
    }

    public function register(Request $request): Response
    {
        $data = $request->only(['name', 'email', 'phone', 'password', 'password_confirmation']);
        $validator = Validator::make($data, [
            'name'     => 'required|min:2|max:100',
            'email'    => 'required|email|max:190',
            'phone'    => 'required|phone',
            'password' => 'required|min:8|max:72|same:password_confirmation',
        ], ['name' => 'Nom', 'email' => 'E-mail', 'phone' => 'Téléphone', 'password' => 'Mot de passe']);

        $users = $this->app->users();
        if (!isset($validator->errors()['email']) && $users->emailExists($data['email'])) {
            $validator->addError('email', 'Un compte existe déjà avec cet e-mail. Connectez-vous plutôt.');
        }

        if ($validator->fails()) {
            return $this->backWithErrors('inscription', $validator->errors(), $data);
        }

        $phone = Phone::normalize($data['phone'], (string) $this->app->config('phone.country_code'));
        $id = $users->create($data['name'], $data['email'], $phone, $data['password']);

        $this->app->auth()->login($id);
        $this->success('Bienvenue ' . $data['name'] . ' ! Votre compte est prêt. Publiez votre premier objet.');

        return $this->redirect('mes-annonces/nouvelle');
    }

    public function logout(Request $request): Response
    {
        $this->app->auth()->logout();

        return $this->redirect('/');
    }
}
