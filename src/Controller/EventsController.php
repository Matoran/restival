<?php
/**
 * @apiDefine EventNotFound
 *
 * @apiError EventNotFound The event was not found.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "EventNotFound"
 *     }
 */

namespace App\Controller;

use App\Controller\AppController;
use App\Controller\Component\BandsInTownComponent;
use App\Controller\Component\EventfulComponent;
use App\Controller\Component\GoogleMapsComponent;
use App\Controller\Component\MusicBrainzComponent;
use App\Controller\Component\SpotifyComponent;
use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;
use Cake\Utility\Hash;

/**
 * @property bool|SpotifyComponent Spotify
 * @property bool|EventfulComponent Eventful
 * @property bool|BandsInTownComponent BandsInTown
 * @property bool|GoogleMapsComponent GoogleMaps
 * @property bool|MusicBrainzComponent MusicBrainz
 */
class EventsController extends AppController
{
    /**
     * @api {GET} /events/around/:address Get all events around address
     * @apiName EventsAround
     * @apiGroup Events
     *
     * @apiParam {String} address     address
     *
     * @apiExample {curl} Example usage:
     *     curl -i "/events/around/Genève"
     *     curl -i "/events/around/Rue de Lyon, Genève"
     *     curl -i "/events/around/Suisse"
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
     *              "address":"Route Cantonale, 1015 Lausanne"
     *              "place":"EPFL"
     *           }
     *        ]
     *     }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "error": "AddressNotFound"
     *     }
     */
    public function around()
    {
        $address = $this->request->getParam('address');
        $latLng = $this->GoogleMaps->getLatLngFromAddress($address);
        $data = $this->Eventful->around($latLng['location']->lat, $latLng['location']->lng, $latLng['radius']);
        if ($data->total_items == 0) {
            throw new NotFoundException('Events not found');
        }
        $events = [];
        foreach ($data->events->event as $id => $event) {
            if (!empty($event->performers)) {
                if (empty($event->venue_address)) {
                    $latitude = $event->latitude;
                    $longitude = $event->longitude;
                    $address = $this->GoogleMaps->latLngToAddress($latitude, $longitude)->results[0]->formatted_address;
                } else {
                    $address = $event->venue_address;
                }
                $events[] = [
                    'id' => (string)$event->{'@attributes'}->id,
                    'name' => $event->title,
                    'date' => $event->start_time,
                    'address' => $address,
                    'place' => $event->venue_name,
                    'latitude' => $event->latitude,
                    'longitude' => $event->longitude
                ];
            }
        }
        $this->set(compact('events'));
        $this->set('_serialize', ['events']);
    }


    /**
     * @api {GET} /events/:id/data Get event data
     * @apiName GetEventData
     * @apiGroup Events
     *
     * @apiParam {Number} id     artist id
     *
     * @apiExample {curl} Example usage:
     *     curl -i /events/1/data
     *
     * @apiSuccess {Object[]} artists List of artists
     * @apiSuccess {Number} artists.id id artist
     * @apiSuccess {String} artists.name artist name
     * @apiSuccess {Date} artists.created date of creation
     * @apiSuccess {Url} artists.picture picture of artist
     * @apiSuccess {Object[]} artists.socialNetworks social networks
     * @apiSuccess {String} artists.socialNetworks.name social network name
     * @apiSuccess {Url} artists.socialNetworks.url social network url
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *        "artists":[
     *           {
     *              "id":1,
     *              "name":"Muse",
     *              "created":"2000-12-12",
     *              "picture":"http://spotify.com/picture/weqweziu",
     *              "socialNetworks":[
     *                 {
     *                   "name":"facebook",
     *                   "url":"https://facebook.com/muse"
     *                 },
     *                 {
     *                   "name":"twitter",
     *                   "url":"https://twitter.com/muse"
     *                 },
     *                 {
     *                   "name":"instagram",
     *                   "url":"https://instagram.com/muse"
     *                 }
     *              ]
     *           },
     *           {
     *              "id":5000,
     *              "name":"Alt-J",
     *              "created":"2005-12-12",
     *              "picture":"http://spotify.com/picture/altj",
     *              "socialNetworks":[
     *                 {
     *                   "name":"facebook",
     *                   "url":"https://facebook.com/altj"
     *                 },
     *                 {
     *                   "name":"twitter",
     *                   "url":"https://twitter.com/altj"
     *                 }
     *              ]
     *           }
     *        ]
     *     }
     *
     * @apiUse EventNotFound
     */
    public function data()
    {
        $id = $this->request->getParam('id');
        $data = $this->Eventful->data($id);
        $artists = [];
        if (empty((array)$data->performers)) {
            throw new NotFoundException('No performers for this event');
        }
        $perfomers = is_array($data->performers->performer) ? $data->performers->performer : $data->performers;
        foreach ($perfomers as $id => $performer) {
            $name = $performer->name;

            $artist = $this->Spotify->getArtistByName($name, false);
            if (!empty($artist)) {
                $name = $artist->name;
            }
            $bit = $this->BandsInTown->getArtistByName($name);
            $mb = $this->MusicBrainz->getArtistInformations($bit->mbid);
            $artists[] = [
                'picture' => $artist->images[0]->url,
                'socialNetworks' => [
                    'spotify' => $artist->external_urls->spotify,
                    'bandsintown' => $bit->url,
                    'facebook' => $bit->facebook_page_url
                ],
                'country' => $mb->country,
                'lifespan' => $mb->{'life-span'}->begin,
                'type' => $mb->type,
                'disambiguation' => $mb->disambiguation
            ];

        }
        $this->set(compact('artists'));
        $this->set('_serialize', ['artists']);
    }

    /**
     * @api {GET} /events/:id/music Get music event
     * @apiName GetEventMusic
     * @apiGroup Events
     *
     * @apiParam {Number} id     event id
     *
     * @apiExample {curl} Example usage:
     *     curl -i /events/1/music
     *
     * @apiSuccess {Url} url url of previw
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *        "url":"http://spotify.com/preview/swag"
     *     }
     *
     * @apiUse EventNotFound
     */
    public function music()
    {
        $id = $this->request->getParam('id');
        $data = $this->Eventful->data($id);
        if (empty((array)$data->performers)) {
            throw new NotFoundException('No performers for this event');
        }
        $perfomers = is_array($data->performers->performer) ? $data->performers->performer : $data->performers;
        $onePreviewPerArtistMax = [];
        foreach ($perfomers as $id => $performer) {
            $result = $this->Spotify->getArtistByName($performer->name, false);
            if (!empty($result)) {
                $id = $result->id;
                $previews = Hash::filter(Hash::extract($this->Spotify->getTopTracksById($id), '{n}.preview_url'));
                shuffle($previews);
                if (!empty($previews)) {
                    $onePreviewPerArtistMax[] = $previews[0];
                }
            }
        }
        if (!empty($onePreviewPerArtistMax)) {
            shuffle($onePreviewPerArtistMax);
            $url = $onePreviewPerArtistMax[0];
            $this->set(compact('url'));
            $this->set('_serialize', ['url']);
        } else {
            throw new NotFoundException('Preview not found');
        }
    }
}



