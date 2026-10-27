<?php

declare(strict_types=1);

namespace App\Http\Auth;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

/**
 * 西北出版用カスタムセッションガード
 */
class CustomSessionGuard extends SessionGuard
{
    /**
     * Set the current user.
     *
     * @return $this
     */
    public function setUser(AuthenticatableContract $user): static
    {
        $this->user = $user;
        $this->loggedOut = false;

        $this->fireAuthenticatedEvent($user);

        return $this;
    }

    /**
     * 西北出版用ログアウト
     * remember_tokenは利用しないので、該当処理を削除します。
     *
     *
     * @see Illuminate\Auth\SessionGuard::logout
     */
    public function logout(): void
    {
        $user = $this->user();

        // If we have an event dispatcher instance, we can fire off the logout event
        // so any further processing can be done. This allows the developer to be
        // listening for anytime a user signs out of this application manually.
        $this->clearUserDataFromStorage();

        if (isset($this->events)) {
            $this->events->dispatch(new Logout($this->name, $user));
        }

        // Once we have fired the logout event we will clear the users out of memory
        // so they are no longer available as the user is no longer considered as
        // being signed into this application and should not be available here.
        $this->user = null;

        $this->loggedOut = true;
    }
}
