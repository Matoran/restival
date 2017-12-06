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
 * @apiDefine PerformersNotFound
 *
 * @apiError PerformersNotFound The event has not performers.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "PerformersNotFound"
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
     * @apiSuccess {String} events.id event id
     * @apiSuccess {String} events.name event name
     * @apiSuccess {Date} events.date event date
     * @apiSuccess {String} events.address event address
     * @apiSuccess {String} events.place event place
     * @apiSuccess {Number} events.latitude latitude
     * @apiSuccess {Number} events.longitude longitude
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "events":[
     *             {
     *                 "id":"E0-001-103559243-1",
     *                 "name":"Estas Tonne",
     *                 "date":"2017-12-06 00:00:00",
     *                 "address":"3 Rue du Stand",
     *                 "place":"Palladium",
     *                 "latitude":"46.2032478",
     *                 "longitude":"6.1348724"
     *             },
     *             {
     *                 "id":"E0-001-106772309-6",
     *                 "name":"Tess (Chat Noir)",
     *                 "date":"2017-12-08 21:00:00",
     *                 "address":"13, rue Vautier",
     *                 "place":"Chat Noir",
     *                 "latitude":"46.1850382",
     *                 "longitude":"6.1424173"
     *             }
     *         ]
     *     }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "error": "AddressNotFound"
     *     }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "error": "EventsNotFound"
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
            if (!empty((array)$event->performers)) {
                if (empty((array)$event->venue_address)) {
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
     *         "artists":[
     *             {
     *                 "picture":"https://i.scdn.co/image/7d0ec00334f74076dbfc12902d70b74557c4cefd",
     *                 "socialNetworks":{
     *                     "spotify":"https://open.spotify.com/artist/6mdiAmATAx73kdxrNrnlao",
     *                     "bandsintown":"https://www.bandsintown.com/a/1301?came_from=267&app_id=Restival",
     *                     "facebook":"https://www.facebook.com/ironmaiden"
     *                 },
     *                 "country":"GB",
     *                 "lifespan":"1975-12-25",
     *                 "type":"Group",
     *                 "disambiguation":"English heavy metal band",
     *                 "name":"Iron Maiden"
     *             },
     *             {
     *                 "picture":"https://i.scdn.co/image/b56bd1ee232e6d40431745fe9b304b270caaa609",
     *                 "socialNetworks":{
     *                     "spotify":"https://open.spotify.com/artist/37394IP6uhnjIpsawpMu4l",
     *                     "bandsintown":"https://www.bandsintown.com/a/201?came_from=267&app_id=Restival",
     *                     "facebook":"https://www.facebook.com/killswitchengage"
     *                 },
     *                 "country":"US",
     *                 "lifespan":"1999",
     *                 "type":"Group",
     *                 "disambiguation":"",
     *                 "name":"Killswitch Engage"
     *             }
     *         ]
     *     }
     *
     * @apiUse EventNotFound
     * @apiUse PerformersNotFound
     */
    public function data()
    {
        $id = $this->request->getParam('id');
        $data = $this->Eventful->data($id);
        $artists = [];
        if (empty((array)$data->performers)) {
            throw new NotFoundException('Performers not found');
        }
        $perfomers = is_array($data->performers->performer) ? $data->performers->performer : $data->performers;
        foreach ($perfomers as $id => $performer) {
            $name = $performer->name;

            $artist = $this->Spotify->getArtistByName($name, false);
            if (!empty($artist)) {
                $name = $artist->name;
            }
            $bit = $this->BandsInTown->getArtistByName($name);
            $artistTemp = [
                'picture' => $artist->images[0]->url,
                'socialNetworks' => [
                    'spotify' => $artist->external_urls->spotify,
                    'bandsintown' => $bit->url,
                    'facebook' => $bit->facebook_page_url
                ],

            ];
            if(!empty($bit->mbid)){
                $mb = $this->MusicBrainz->getArtistInformations($bit->mbid);
                $artistTemp = array_merge($artistTemp, ['country' => $mb->country,
                'lifespan' => $mb->{'life-span'}->begin,
                'type' => $mb->type,
                'disambiguation' => $mb->disambiguation]);
            }
            $artistTemp['name'] = $name;
            $artists[] = $artistTemp;

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
     *        "url":"https://p.scdn.co/mp3-preview/52414dfddfb72762158e02fff5f129a0a544a61f?cid=919405e7e15a4ecd9d4e4e55c178ce91"
     *     }
     *
     * @apiUse EventNotFound
     * @apiUse PerformersNotFound
     */
    public function music()
    {
        $id = $this->request->getParam('id');
        $data = $this->Eventful->data($id);
        if (empty((array)$data->performers)) {
            throw new NotFoundException('Performers not found');
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



