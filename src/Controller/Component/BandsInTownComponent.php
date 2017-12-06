<?php
/**
 * User: Marco Lopes (Matoran)
 * Date: 11/27/17
 * Time: 7:49 PM
 */

namespace App\Controller\Component;


use Cake\Controller\Component;
use Cake\Filesystem\File;
use Cake\Http\Client;

class BandsInTownComponent extends Component
{
    private $base = 'https://rest.bandsintown.com';

    public function getArtistByName($name)
    {
        $file = new File('tmp/bandsintown/' . $name);
        if($file->exists()){
            return json_decode($file->read());
        }
        $client = new Client([
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);
        $data = $client->get(
            $this->base . '/artists/' . $name,
            [
                'app_id' => 'Restival',
            ]
        )->body();
        $file = new File('tmp/bandsintown/' . $name, true);
        $file->write($data);
        $file->close();
        return json_decode($data);
    }

    public function getEventsByArtist($name)
    {
        $file = new File('tmp/bandsintown/events' . $name);
        if($file->exists()){
            return json_decode($file->read());
        }
        $client = new Client([
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]);
        $data = $client->get(
            $this->base . '/artists/' . $name . '/events',
            [
                'app_id' => 'Restival',
            ]
        )->body();
        $file = new File('tmp/bandsintown/events' . $name, true);
        $file->write($data);
        $file->close();
        return json_decode($data);
    }

}