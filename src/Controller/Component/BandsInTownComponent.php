<?php
/**
 * User: Marco Lopes (Matoran)
 * Date: 11/27/17
 * Time: 7:49 PM
 */

namespace App\Controller\Component;


use Cake\Controller\Component;
use Cake\Http\Client;

class BandsInTownComponent extends Component
{
    private $base = 'https://rest.bandsintown.com';

    public function getEventsByArtist($name)
    {
        $client = new Client([
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]);
        return json_decode($client->get(
            $this->base . '/artists/' . $name . '/events',
            [
                'app_id' => 'Restival',
            ]
        )->body());
    }

}