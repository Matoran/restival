<?php
/**
 * @apiDefine ArtistNotFound
 *
 * @apiError ArtistNotFound The artist was not found.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "ArtistNotFound"
 *     }
 */

namespace App\Controller;

use App\Controller\Component\BandsInTownComponent;
use App\Controller\Component\EventfulComponent;
use App\Controller\Component\MusicBrainzComponent;
use App\Controller\Component\SpotifyComponent;
use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;
use Cake\Utility\Hash;


/**
 * @property bool|SpotifyComponent Spotify
 * @property bool|EventfulComponent Eventful
 * @property bool|BandsInTownComponent BandsInTown
 * @property bool|MusicBrainzComponent MusicBrainz
 */
class ArtistsController extends AppController
{

    /**
     * @api {GET} /artists/:name/id Get artist id
     * @apiName GetArtistId
     * @apiGroup Artists
     *
     * @apiParam {String} name     artist name
     *
     * @apiExample {curl} Example usage:
     *     curl -i /artists/muse/id
     *
     * @apiSuccess {String} id artist id
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *        "id":"12Chz98pHFMPJEknJQMWvI"
     *     }
     *
     * @apiUse ArtistNotFound
     */
    public function id()
    {
        $name = $this->request->getParam('name');
        $id = $this->Spotify->getArtistByName($name)->id;
        $this->set(compact('id'));
        $this->set('_serialize', ['id']);
    }

    /**
     * @api {GET} /artists/:id/events Get all events from artist
     * @apiName GetArtistsEvents
     * @apiGroup Artists
     *
     * @apiParam {Number} id          artist id
     *
     * @apiExample {curl} Example usage:
     *     curl -i /artists/12Chz98pHFMPJEknJQMWvI/events
     *
     * @apiSuccess {Object[]} events List of events
     * @apiSuccess {Number} events.id event id
     * @apiSuccess {String} events.name event name
     * @apiSuccess {Date} events.date event date
     * @apiSuccess {String} events.address event address
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *
     *     {
     *         "events": [
     *             {
     *                 "id": "20150946",
     *                 "name": "The Forum",
     *                 "date": "2017-12-09T19:00:00",
     *                 "address": "The Forum, Inglewood, CA, United States"
     *             },
     *             {
     *                 "id": "19392364",
     *                 "name": "Qudos Bank Arena",
     *                 "date": "2017-12-16T20:00:00",
     *                 "address": "Qudos Bank Arena, Sydney Olympic Park, 02, Australia"
     *             },
     *             {
     *                 "id": "19392366",
     *                 "name": "Rod Laver Arena",
     *                 "date": "2017-12-18T20:00:00",
     *                 "address": "Rod Laver Arena, Melbourne, Vic, Australia"
     *             }
     *         ]
     *     }
     *
     * @apiUse ArtistNotFound
     */
    public function events()
    {
        $name = $this->request->getParam('name');
        $name = $this->Spotify->getArtistByName($name)->name;
        $result = $this->BandsInTown->getEventsByArtist($name);
        $events = [];
        foreach ($result as $event){
            $events[] = [
                'id' => $event->id,
                'name' => $event->venue->name,
                'date' => $event->datetime,
                'address' => $event->venue->name . ', ' . $event->venue->city . ', ' . $event->venue->region . ', ' . $event->venue->country
                ];
        }
        $this->set(compact('events'));
        $this->set('_serialize', ['events']);
    }

    /**
     * @api {GET} /artists/:id/music Get random music from artist
     * @apiName GetArtistMusic
     * @apiGroup Artists
     *
     * @apiParam {Number} id     artist id
     *
     * @apiExample {curl} Example usage:
     *     curl -i /artists/43ZHCT0cAZBISjO8DG9PnE/music
     *
     * @apiSuccess {Url} url url preview
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *        "url":"https://p.scdn.co/mp3-preview/29990f669b5328b6c40320596a2b14d8660cdb54?cid=919405e7e15a4ecd9d4e4e55c178ce91"
     *     }
     *
     * @apiUse ArtistNotFound
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "error": "PreviewNotFound"
     *     }
     */
    public function music()
    {
        $name = $this->request->getParam('name');
        $id = $this->Spotify->getArtistByName($name)->id;
        $previews = Hash::filter(Hash::extract($this->Spotify->getTopTracksById($id), '{n}.preview_url'));
        shuffle($previews);
        if(!empty($previews)){
            $url = $previews[0];
            $this->set(compact('url'));
            $this->set('_serialize', ['url']);
        }else{
            throw new NotFoundException('Preview not found');
        }
    }


    /**
     * @api {GET} /artists/:id/data Get artist data
     * @apiName GetArtistData
     * @apiGroup Artists
     *
     * @apiParam {Number} id     artist id
     *
     * @apiExample {curl} Example usage:
     *     curl -i /artists/1/data
     *
     * @apiSuccess {String} name name of artist
     * @apiSuccess {Url} artists.picture picture of artist
     * @apiSuccess {Object[]} artists.socialNetworks social networks
     * @apiSuccess {String} artists.socialNetworks.name social network name
     * @apiSuccess {Url} artists.socialNetworks.url social network url
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *        "name":"Muse",
     *        "created":"2000-12-12",
     *        "picture":"http://spotify.com/picture/weqweziu",
     *        "socialNetworks":[
     *           {
     *             "name":"facebook",
     *             "url":"https://facebook.com/muse"
     *           },
     *           {
     *             "name":"twitter",
     *             "url":"https://twitter.com/muse"
     *           },
     *           {
     *             "name":"instagram",
     *             "url":"https://instagram.com/muse"
     *           }
     *     }
     *
     * @apiUse ArtistNotFound
     */
    public function data(){
        $name = $this->request->getParam('name');
        $artist = $this->Spotify->getArtistByName($name);
        $name =  $artist->name;
        $picture = $artist->images[0]->url;
        $socialNetworks = [
                'spotify' => $artist->external_urls->spotify
        ];
        $this->set(compact('name', 'picture', 'socialNetworks'));
        $this->set('_serialize', ['name', 'picture', 'socialNetworks']);
    }

}