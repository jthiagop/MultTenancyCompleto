<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use GuzzleHttp\Client;


class UpdateLastLogin
{

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
        /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $event->user->last_login = now(); //Ultimo login

        $user = $event->user; //pegar a localizão
        $user->last_login = now();

        // Obter o endereço IP do usuário
        $ip = request()->ip();
        $user->login_ip = $ip;

        // Obter a localização usando IPStack
        // $client = new Client();
        // $response = $client->get("http://api.ipstack.com/{$ip}?access_key=YOUR_ACCESS_KEY");

        // $locationData = json_decode($response->getBody(), true);

        // if ($locationData) {
        //     $user->login_latitude = $locationData['latitude'];
        //     $user->login_longitude = $locationData['longitude'];
        //     $user->login_city = $locationData['city'];
        // }

        $user->save();
    }
}
