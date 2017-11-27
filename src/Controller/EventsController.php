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
 *              "address":"EPFL"
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
