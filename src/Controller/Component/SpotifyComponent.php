<?php
/**
 * Created by PhpStorm.
 * User: matoran
 * Date: 11/15/17
 * Time: 11:16 AM
 */

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Filesystem\File;
use Cake\Http\Client;
use Cake\Network\Exception\NotFoundException;

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

    public function getArtistByName($name, $throwError = true)
    {
        $file = new File('tmp/spotify/' . $name);
        if($file->exists()){
            return json_decode($file->read());
        }
        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token(),
                'Accept' => 'application/json'
            ]
        ]);
        $results = json_decode($client->get(
            $this->base . '/v1/search',
            [
                'q' => $name,
                'type' => 'artist'
            ]
        )->body())->artists;
        if (empty($results->items)) {
            if ($throwError) {
                throw new NotFoundException('Artist not found');
            } else {
                return null;
            }
        } else {
            $file = new File('tmp/spotify/' . $name, true);
            $file->write(json_encode($results->items[0]));
            $file->close();
            return $results->items[0];
        }

    }

    public function getArtistById($id)
    {
        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token(),
                'Accept' => 'application/json'
            ]
        ]);
        return json_decode($client->get(
            $this->base . '/v1/artists/' . $id
        )->body());
    }

    public function getTopTracksById($id)
    {
        $file = new File('tmp/spotify/' . $id);
        if($file->exists()){
            return json_decode($file->read())->tracks;
        }

        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token(),
                'Accept' => 'application/json'
            ]
        ]);
        $result = $client->get(
            $this->base . '/v1/artists/' . $id . '/top-tracks',
            [
                'country' => 'CH'
            ]
        )->body();

        $file = new File('tmp/spotify/' . $id, true);
        $file->write($result);
        $file->close();

        return json_decode($result)->tracks;
    }


}