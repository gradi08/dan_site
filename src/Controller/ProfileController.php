<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Validator;
use App\Http\Request;
use App\Http\Response;
use App\Support\Phone;

final class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        return $this->view('pages/dashboard/profile', ['user' => $this->app->auth()->user()]);
    }

    public function update(Request $request): Response
    {
        $data = $request->only(['name', 'email', 'phone']);
        $validator = Validator::make($data, [
            'name'  => 'required|min:2|max:100',
            'email' => 'required|email|max:190',
            'phone' => 'required|phone',
        ], ['name' => 'Nom', 'email' => 'E-mail', 'phone' => 'Téléphone']);

        $users = $this->app->users();
        if (!isset($validator->errors()['email']) && $users->emailExists($data['email'], $this->userId())) {
            $validator->addError('email', 'Cet e-mail est déjà utilisé par un autre compte.');
        }
        if ($validator->fails()) {
            return $this->backWithErrors('profil', $validator->errors(), $data);
        }

        $phone = Phone::normalize($data['phone'], (string) $this->app->config('phone.country_code'));
        $users->updateProfile($this->userId(), $data['name'], $data['email'], $phone);
        $this->app->auth()->refresh();
        $this->success('Profil mis à jour.');

        return $this->redirect('profil');
    }

    public function password(Request $request): Response
    {
        $data = $request->only(['current_password', 'password', 'password_confirmation']);
        $validator = Validator::make($data, [
            'current_password' => 'required',
            'password'         => 'required|min:8|max:72|same:password_confirmation',
        ], ['current_password' => 'Mot de passe actuel', 'password' => 'Nouveau mot de passe']);

        $users = $this->app->users();
        $hash = $users->passwordHash($this->userId());
        if (!isset($validator->errors()['current_password']) && !password_verify($data['current_password'], (string) $hash)) {
            $validator->addError('current_password', 'Mot de passe actuel incorrect.');
        }
        if ($validator->fails()) {
            return $this->backWithErrors('profil', $validator->errors());
        }

        $users->updatePassword($this->userId(), $data['password']);
        $this->app->session()->regenerate();
        $this->success('Mot de passe modifié.');

        return $this->redirect('profil');
    }
}
