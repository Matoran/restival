<?php
/**
 * User: Marco Lopes (Matoran)
 * Date: 11/28/17
 * Time: 7:53 PM
 */

namespace App\Controller\Component;


use Cake\Controller\Component;
use Cake\Filesystem\File;
use Cake\Http\Client;

class MusicBrainzComponent extends Component
{
    private $base = 'http://musicbrainz.org/ws/2';

    public function getArtistInformations($mbid){
        $file = new File('tmp/musicbrainz/' . $mbid);
        if($file->exists()){
            return json_decode($file->read());
        }
        $client = new Client();
        $results = $client->get(
            $this->base . '/artist/' . $mbid,
            [
                'fmt' => 'json',
                'inc'
            ]
        )->body();
        $file = new File('tmp/musicbrainz/' . $mbid, true);
        $file->write($results);
        $file->close();
        return json_decode($results);

    }
}