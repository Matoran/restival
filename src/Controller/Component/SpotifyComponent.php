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
    private $base = "https://api.spotify.com";

    public function getArtistFromName($name)
    {
        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer BQCxgY3s4B5KWSx709Y2TZ-mZAF8OtbOyYUEnj-yyBBMKPdAI0tB5pRE9grosCqOqsi5qpzWFNc4Y2Rf3N8sYFuZdM8BkVb6rX_UMNUKHVGpxdT1E9J7ZBcq-zaRATdfHrYDC64FBRM',
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