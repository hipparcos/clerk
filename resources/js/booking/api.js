import axios from 'axios'
import moment from 'moment'

import user from '../user/api.js'
import room from '../room/api.js'

/**
 * Booking is a clerk booking.
 */
class Booking {
    constructor({
            id = undefined,
            start = moment(),
            duration = 15,
            room = {},
            user = {},
        }) {
        this.id = id
        this.start = start
        this.duration = duration
        this.end = this.start.clone().add(duration, 'minutes')
        // Relationships.
        this.room = room
        this.user = user
    }

    get start() {
        return this._start
    }
    set start(newStart) {
        if (newStart instanceof moment) {
            this._start = newStart
        } else {
            this._start = moment(newStart)
        }
    }

    get room() {
        return this._room
    }
    set room(newRoom) {
        if (newRoom instanceof room.Room) {
            this._room = newRoom
        }
    }

    get user() {
        return this._user
    }
    set user(newUser) {
        if (newUser instanceof user.User) {
            this._user = newUser
        }
    }
}

/**
 * BookingError contain the description of booking error.
 */
class BookingError extends Error {
    constructor(message, status, data) {
        super(message)
        this.status = status
        this.data = data
    }
}

/**
 * create creates a booking in the backend.
 * @returns {Promise}.then(Booking).catch(BookingError)
 */
const create = function(booking, override) {
    return new Promise((resolve, reject) => {
        if (!(booking instanceof Booking)) {
            reject(
                new BookingError(
                    "booking.api.create: first argument must be an instance of Booking."
                )
            )
        }
        axios({
            method: 'post',
            url: '/api/bookings',
            data: {
                data: {
                    type: "booking",
                    attributes: {
                        start: booking.start,
                        duration: booking.duration,
                    },
                    relationships: {
                        room: {
                            data: { id: booking.room.id }
                        }
                    },
                },
                meta: {
                    overrideUserCollision: override
                }
            }
        })
            .then(resp => {
                let data = resp.data.data
                let booking = new Booking({
                    id: data.id,
                    start: data.attributes.start,
                    duration: data.attributes.duration,
                })
                let roomData = data.relationships.room.data
                booking.room = new room.Room({
                    id: roomData.id,
                    name: roomData.attributes.name,
                })
                let userData = data.relationships.user.data
                booking.user = new user.User({
                    id: userData.id,
                    name: userData.attributes.name,
                    email: userData.attributes.email,
                })
                resolve(booking)
            })
            .catch(err => {
                reject(new BookingError(
                    "booking.api.create: api call error",
                    err.response.status,
                    err.response.data
                ))
            })
    })
}

export default {
    // data.
    Booking,
    BookingError,
    // api calls.
    create,
}

