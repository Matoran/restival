<?php
/**
 * Created by IntelliJ IDEA.
 * User: cyril
 * Date: 11/27/17
 * Time: 10:33 PM
 */

namespace App\Controller\Component;


use Cake\Controller\Component;
use Cake\Http\Client;

class GoogleMapsComponent extends Component
{
    private $key = 'AIzaSyC0ZGHbpcTrIYvkRxDIMRM_OyXbI5q9u00';
    public function latLngToAddress($lat, $lng){
        $client = new Client([
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);
        return json_decode($client->get(
            'https://maps.googleapis.com/maps/api/geocode/json',
            [
                'latlng' => $lat .','.$lng,
                'key' => $this->key
            ]
        )->body());
    }
}