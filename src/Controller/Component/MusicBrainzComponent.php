<?php
/**
 * User: Marco Lopes (Matoran)
 * Date: 11/28/17
 * Time: 7:53 PM
 */

namespace App\Controller\Component;


use Cake\Controller\Component;
use Cake\Http\Client;

class MusicBrainzComponent extends Component
{
    private $base = 'http://musicbrainz.org/ws/2';

    public function getArtistInformations($name){
        $client = new Client();
        $results = json_decode($client->get(
            $this->base . '/artist',
            [
                'query' => 'artist:' . $name,
                'fmt' => 'json',
                'inc'
            ]
        )->body());
        debug($results);
    }
}