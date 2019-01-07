import axios from 'axios'

import err from '../error/lib.js'

/**
 * Room is a clerk room.
 */
class Room {
    constructor({
            id = undefined,
            name = ''
        }) {
        this.id = id
        this.name = name
    }

    static fromAPI(data) {
        return new Room({
            id: data.id,
            name: data.attributes.name,
        })
    }
}

/**
 * RoomError contain the description of a getRooms error.
 */
class RoomError extends err.APIError {
    constructor({
        message = 'room.api: error',
        status = 0,
        data = {},
    }) {
        super({ message, status, data, fields: {
            'name': 'data.attributes.name',
        }})
    }
}

/**
 * all returns a list of all rooms.
 * @returns {Promise}.then([Room]).catch(RoomError)
 */
const all = function() {
    return new Promise((resolve, reject) => {
        axios({
            method: 'get',
            url: '/api/rooms'
        })
            .then(resp => {
                let data = resp.data.data
                let rooms = data.map(r => new Room.fromAPI(r))
                resolve(rooms)
            })
            .catch(err => {
                reject(new RoomError({
                    message: 'room.api.all: api call error',
                    status: err.response.status,
                    data: err.response.data,
                }))
            })
    })
}

export default {
    // data.
    Room,
    RoomError,
    // api calls.
    all,
}

