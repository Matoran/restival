<?php
/**
 * Created by PhpStorm.
 * User: matoran
 * Date: 11/15/17
 * Time: 11:16 AM
 */

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Http\Client;

class SpotifyComponent extends Component
{
    private $base = 'https://api.spotify.com';
    private $client = '919405e7e15a4ecd9d4e4e55c178ce91';
    private $secret = '957b7cb9e20a46e1b058c1e4dde2aa03';


    /*
     *
     *
     * const options = {
        url: "https://accounts.spotify.com/api/token",
        method: "POST",
        headers: {
            'Authorization': `Basic ${b64Key}`,
        },
        form: {
            'grant_type': "client_credentials",
        },
    };
     */
    public function token()
    {
        $client = new Client([
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->client . ':' . $this->secret)
            ]
        ]);
        return json_decode($client->post('https://accounts.spotify.com/api/token',
            [
                'grant_type' => 'client_credentials'
            ]
        )->body())->access_token;
    }

    public function getArtistFromName($name)
    {
        debug($name);
        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token(),
                'Accept' => 'application/json'
            ]
        ]);
        return json_decode($client->get(
            $this->base . '/v1/search',
            [
                'q' => $name,
                'type' => 'artist'
            ]
        )->body());
    }


}