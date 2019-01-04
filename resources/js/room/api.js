import axios from 'axios'

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
const RoomError = function(status, data) {
    this.status = status
    this.data = data
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
                reject(new RoomError(
                    err.response.status,
                    err.response.data
                ))
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

