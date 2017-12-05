<?php
/**
 * User: Marco Lopes (Matoran)
 * Date: 11/27/17
 * Time: 8:16 PM
 */

namespace App\Controller\Component;


use Cake\Controller\Component;
use Cake\Filesystem\File;
use Cake\Http\Client;

class EventfulComponent extends Component
{

    private $base = 'http://api.eventful.com/rest';
    private $key = '686MVP7ZrCfbMMHc';

    public function around($lat, $lng, $radius){
        $file = new File('tmp/eventful/' . $lat . $lng .$radius);
        if($file->exists()){
            return json_decode($file->read());
        }
        $client = new Client([
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);

        $result = $client->get(
            $this->base . '/events/search',
            [
                'app_key' => $this->key,
                'location' => $lat . ',' . $lng,
                'within' => $radius,
                'category' => 'music',
                'date' => 'Future',
                'page_size' => 250,
                'sort_order' => 'date'
            ]
        )->body();
        $file = new File('tmp/eventful/' . $lat . $lng .$radius, true);
        $result = json_encode(simplexml_load_string($result));
        $file->write($result);
        $file->close();
        return json_decode($result);
    }

    public function data($id){
        $file = new File('tmp/eventful/' . $id);
        if($file->exists()){
            return json_decode($file->read());
        }

        $client = new Client([
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);
        $result = $client->get(
            $this->base . '/events/get',
            [
                'app_key' => $this->key,
                'id' => $id
            ]
        )->body();
        $file = new File('tmp/eventful/' . $id, true);
        $result = json_encode(simplexml_load_string($result));
        $file->write($result);
        $file->close();
        return json_decode($result);
    }



}