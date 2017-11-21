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


/**
 * @api {GET} /artists/:id/events Get all events from artist
 * @apiName GetArtistsEvents
 * @apiGroup Artists
 *
 * @apiParam {Number} id          artist id
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

/**
 * @api {GET} /artists/:name/id Get artist id
 * @apiName GetArtistId
 * @apiGroup Artists
 *
 * @apiParam {String} name     artist name
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *        "id":1
 *     }
 *
 * @apiUse ArtistNotFound
 */


/**
 * @api {GET} /artists/:id/music Get random music from artist
 * @apiName GetArtistId
 * @apiGroup Artists
 *
 * @apiParam {Number} id     artist id
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *        "url":"http://spotify.com/preview/wrdasda"
 *     }
 *
 * @apiUse ArtistNotFound
 */


/**
 * @api {GET} /artists/:id/data Get artist data
 * @apiName GetArtistData
 * @apiGroup Artists
 *
 * @apiParam {Number} id     artist id
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *        "name":"Muse",
 *        "created":"2000-12-12",
 *        "picture":"http://spotify.com/picture/weqweziu",
 *        "socialNetworks":{
 *           "facebook":"https://facebook.com/muse",
 *           "twitter":"https://twitter.com/muse",
 *           "instagram":"https://instagram.com/muse"
 *        }
 *     }
 *
 * @apiUse ArtistNotFound
 */