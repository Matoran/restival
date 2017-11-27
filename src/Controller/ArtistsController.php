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
use App\Controller\Component\SpotifyComponent;
use Cake\Event\Event;


/**
 * @property bool|SpotifyComponent Spotify
 * @property bool|EventfulComponent Eventful
 * @property bool|BandsInTownComponent BandsInTown
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
     * @apiSuccess {Number} id artist id
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *        "id":1
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
     *     curl -i /artists/1/events
     *
     * @apiSuccess {Object[]} events List of events
     * @apiSuccess {Number} events.id event id
     * @apiSuccess {String} events.name event name
     * @apiSuccess {Date} events.date event date
     * @apiSuccess {String} events.address event address
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *        "events":[
     *           {
     *              "id":1
     *              "name":"Balelec",
     *              "date":"2018-12-12",
     *              "address":"EPFL"
     *           }
     *        ]
     *     }
     *
     * @apiUse ArtistNotFound
     */
    public function events()
    {
        $id = $this->request->getParam('id');

        $name = $this->Spotify->getArtistById($id)->name;
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
        //$this->set('_serialize', ['events']);
    }

    /**
     * @api {GET} /artists/:id/music Get random music from artist
     * @apiName GetArtistMusic
     * @apiGroup Artists
     *
     * @apiParam {Number} id     artist id
     *
     * @apiExample {curl} Example usage:
     *     curl -i /artists/1/music
     *
     * @apiSuccess {Url} url url preview
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *        "url":"http://spotify.com/preview/muse/aweqw"
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
        $id = $this->request->getParam('id');
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
        $id = $this->request->getParam('id');
    }

}