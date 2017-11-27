<?php
/**
 * User: Marco Lopes (Matoran)
 * Date: 11/27/17
 * Time: 8:16 PM
 */

namespace App\Controller\Component;


use Cake\Controller\Component;
use Cake\Http\Client;

class EventfulComponent extends Component
{

    private $base = 'http://api.eventful.com/rest';
    private $key = '686MVP7ZrCfbMMHc';

    public function around($address){
        $client = new Client([
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);
        return simplexml_load_string($client->get(
            $this->base . '/events/search',
            [
                'app_key' => $this->key,
                'location' => $address,
                'category' => 'music',
                'date' => 'Future',
            ]
        )->body());
    }



}