import axios from 'axios'

/**
 * Room is a clerk room.
 */
const Room = function({
        id = undefined,
        name = ''
    }) {
    this.id = id
    this.name = name
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
                let rooms = data.map(r => new Room({id: r.id, name: r.attributes.name}))
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

