import axios from 'axios'

/**
 * room is a clerk room.
 */
const room = function(id, name) {
    this.id = id
    this.name = name
}

/**
 * error contain the description of a getRooms error.
 */
const error = function(status, data) {
    this.status = status
    this.data = data
}

/**
 * getRooms returns a list of all rooms.
 * @returns {Promise}.then([room]).catch(error)
 */
const getRooms = function() {
    return new Promise((resolve, reject) => {
        axios({
            method: 'get',
            url: '/api/rooms'
        })
            .then(resp => {
                let data = resp.data.data
                let rooms = data.map(r => new room(r.id, r.attributes.name))
                resolve(rooms)
            })
            .catch(err => {
                reject(new error(
                    err.response.status,
                    err.response.data
                ))
            })
    })
}

export default {
    // data.
    room,
    error,
    // api calls.
    getRooms,
}

