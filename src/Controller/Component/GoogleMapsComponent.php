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
use Cake\Network\Exception\NotFoundException;

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

    public function getLatLngFromAddress($address){
        $client = new Client([
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);
        $result = json_decode($client->get(
            'https://maps.googleapis.com/maps/api/geocode/json',
            [
                'address' => $address,
                'key' => $this->key
            ]
        )->body());
        $r = 6371.0;
        if($result->status == "ZERO_RESULTS"){
            throw new NotFoundException('Address not found');
        }
        $geometry = $result->results[0]->geometry;
        $lat1 = deg2rad($geometry->location->lat);
        $lon1 = deg2rad($geometry->location->lng);
        $lat2 = deg2rad($geometry->viewport->northeast->lat);
        $lon2 = deg2rad($geometry->viewport->northeast->lng);

        $dis = $r * acos(sin($lat1) * sin($lat2) +
                cos($lat1) * cos($lat2) * cos($lon2 - $lon1));

        return [
            'location' =>$geometry->location,
            'radius' => $dis
        ];
    }
}