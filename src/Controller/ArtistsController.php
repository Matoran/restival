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
        $artist = $this->Spotify->getArtistByName($name);
        $id = $artist->id;
        $this->set(compact('id'));
        $this->set('_serialize', ['id']);
    }

    /**
     * @api {GET} /artists/:name/events Get all events from artist
     * @apiName GetArtistsEvents
     * @apiGroup Artists
     *
     * @apiParam {Number} name          artist name
     *
     * @apiExample {curl} Example usage:
     *     curl -i /artists/Muse/events
     *
     * @apiSuccess {Object[]} events List of events
     * @apiSuccess {Number} events.id event id
     * @apiSuccess {String} events.name event name
     * @apiSuccess {Date} events.date event date
     * @apiSuccess {String} events.address event address
     * @apiSuccess {Number} events.latitude latitude
     * @apiSuccess {Number} events.longitude longitude
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *
     *     {
     *         "events":[
     *             {
     *                 "id":"20150946",
     *                 "name":"The Forum",
     *                 "date":"2017-12-09T19:00:00",
     *                 "address":"The Forum, Inglewood, CA, United States",
     *                 "place":"The Forum",
     *                 "latitude":"33.9583",
     *                 "longitude":"-118.341868"
     *             },
     *             {
     *                 "id":"19392364",
     *                 "name":"Qudos Bank Arena",
     *                 "date":"2017-12-16T20:00:00",
     *                 "address":"Qudos Bank Arena, Sydney Olympic Park, 02, Australia",
     *                 "place":"Qudos Bank Arena",
     *                 "latitude":"-33.8484897",
     *                 "longitude":"151.0672072"
     *             }
     *         ]
     *     }
     *
     * @apiUse ArtistNotFound
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "error": "EventsNotFound"
     *     }
     */
    public function events()
    {
        $name = $this->request->getParam('name');
        $result = $this->Spotify->getArtistByName($name);
        if (!empty($result)) {
            $name = $result->name;
        }
        $result = $this->BandsInTown->getEventsByArtist($name);
        $events = [];
        if (!isset($result->errors)) {
            foreach ($result as $event) {
                $events[] = [
                    'id' => $event->id,
                    'name' => $event->venue->name,
                    'date' => $event->datetime,
                    'address' => $event->venue->name . ', ' . $event->venue->city . ', ' . $event->venue->region . ', ' . $event->venue->country,
                    'place' => $event->venue->name,
                    'latitude' => $event->venue->latitude,
                    'longitude' => $event->venue->longitude,

                ];
            }
        }
        if (empty($events)) {
            throw new NotFoundException('Events not found');
        }
        $this->set(compact('events'));
        $this->set('_serialize', ['events']);
    }

    /**
     * @api {GET} /artists/:name/music Get random music from artist
     * @apiName GetArtistMusic
     * @apiGroup Artists
     *
     * @apiParam {String} name     artist name
     *
     * @apiExample {curl} Example usage:
     *     curl -i /artists/Muse/music
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
        if (!empty($previews)) {
            $url = $previews[0];
            $this->set(compact('url'));
            $this->set('_serialize', ['url']);
        } else {
            throw new NotFoundException('Preview not found');
        }
    }


    /**
     * @api {GET} /artists/:name/data Get artist data
     * @apiName GetArtistData
     * @apiGroup Artists
     *
     * @apiParam {Number} name     artist name
     *
     * @apiExample {curl} Example usage:
     *     curl -i /artists/muse/data
     *
     * @apiSuccess {String} name name of artist
     * @apiSuccess {Url} artists.picture picture of artist
     * @apiSuccess {Object[]} artists.socialNetworks social networks
     * @apiSuccess {Url} artists.socialNetworks.spotify spotify artist link
     * @apiSuccess {Url} artists.socialNetworks.bandsintown bandsintown artist link
     * @apiSuccess {Url} artists.socialNetworks.facebook facebook artist link
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "name":"Muse",
     *         "picture":"https://i.scdn.co/image/19ac88c7aec1f68aa6e207aff29efa15d37336a7",
     *         "socialNetworks":{
     *             "spotify":"https://open.spotify.com/artist/12Chz98pHFMPJEknJQMWvI",
     *             "bandsintown":"https://www.bandsintown.com/a/143?came_from=267&app_id=Restival",
     *             "facebook":"http://www.facebook.com/muse"
     *         },
     *         "country":"GB",
     *         "lifespan":"1994",
     *         "type":"Group",
     *         "disambiguation":"UK rock band"
     *     }
     *
     * @apiUse ArtistNotFound
     */
    public function data()
    {
        $name = $this->request->getParam('name');
        $artist = $this->Spotify->getArtistByName($name);
        $name = $artist->name;
        $bit = $this->BandsInTown->getArtistByName($name);
        $mb = $this->MusicBrainz->getArtistInformations($bit->mbid);
        $picture = $artist->images[0]->url;
        $socialNetworks = [
            'spotify' => $artist->external_urls->spotify,
            'bandsintown' => $bit->url,
            'facebook' => $bit->facebook_page_url
        ];
        $country = $mb->country;
        $lifespan = $mb->{'life-span'}->begin;
        $type = $mb->type;
        $disambiguation = $mb->disambiguation;
        $this->set(compact('name', 'picture', 'socialNetworks', 'country', 'lifespan', 'type', 'disambiguation'));
        $this->set('_serialize', ['name', 'picture', 'socialNetworks', 'country', 'lifespan', 'type', 'disambiguation']);
    }

}