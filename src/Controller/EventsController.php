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
 * @api {GET} /events/:id Get event data
 * @apiName GetEventData
 * @apiGroup Events
 *
 * @apiParam {Number} id     artist id
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
 *              "socialNetworks":{
 *                 "facebook":"https://facebook.com/muse",
 *                 "twitter":"https://twitter.com/muse",
 *                 "instagram":"https://instagram.com/muse"
 *
 *           },
 *           {
 *              "id":-5000,
 *              "name":"Alt-J",
 *              "created":"1200-12-12",
 *              "picture":"http://spotify.com/picture/notfound",
 *              "socialNetworks":{
 *                 "facebook":"https://facebook.com/moyenage",
 *                 "instagram":"https://instagram.com/moyenage"
 *
 *           },
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
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *        "url":"http://spotify.com/preview/swag"
 *     }
 *
 * @apiUse EventNotFound
 */
