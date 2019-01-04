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
        // Relationships.
        this.room = room
        this.user = user
    }

    clone() {
        return JSON.parse(JSON.stringify(this))
    }

    static fromAPI(data) {
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
        return booking
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
        this.updateEnd()
    }

    get duration() {
        return this._duration
    }
    set duration(newDuration) {
        this._duration = newDuration
        this.updateEnd()
    }

    get end() {
        return this._end
    }
    updateEnd() {
        if (this.start && this.duration) {
            this._end = this.start.clone().add(this.duration, 'minutes')
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
 * save saves a booking in the backend.
 * @param {Booking} booking
 * @param {Boolean} override overrides user collision
 * @returns {Promise}.then(Booking).catch(BookingError)
 */
const save = function(booking, override) {
    return new Promise((resolve, reject) => {
        if (!(booking instanceof Booking)) {
            reject(
                new BookingError(
                    "booking.api.create: first argument must be an instance of Booking."
                )
            )
        }
        axios({
            method: (booking.id) ? 'patch' : 'post',
            url: '/api/bookings' + ((booking.id) ? `/${booking.id}` : ''),
            data: {
                data: {
                    id: booking.id,
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
                let booking = Booking.fromAPI(data)
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

/**
 * creates creates a booking in the backend.
 * @param {Booking} booking
 * @param {Boolean} override overrides user collision
 * @returns {Promise}.then(Booking).catch(BookingError)
 */
const create = function(booking, override) {
    booking.id = undefined
    return save(booking, override)
}

/**
 * update updates a booking in the backend.
 * @param {Booking} booking
 * @param {Boolean} override overrides user collision
 * @returns {Promise}.then(Booking).catch(BookingError)
 */
const update = save

/**
 * all retrieves all bookings from the backend.
 * @param {moment} date
 * @returns {Promise}.then([Booking]).catch(BookingError)
 */
const all = function(date) {
    return new Promise((resolve, reject) => {
        axios({
            method: 'get',
            url: '/api/bookings' + ((date) ? date.format('/YYYY/MM/DD') : ''),
        })
            .then(resp => {
                let data = resp.data.data
                let bookings = data.map(b => Booking.fromAPI(b))
                resolve(bookings)
            })
            .catch(err => {
                reject(new BookingError(
                    "booking.api.all: api call error",
                    err.response.status,
                    err.response.data
                ))
            })
    })
}

/**
 * remove deletes a booking from the backend.
 * @param {Number} booking is
 * @returns {Promise}.then().catch(BookingError)
 */
const remove = function(id) {
    return new Promise((resolve, reject) => {
        axios({
            method: 'delete',
            url: `/api/bookings/${id}`,
        })
            .then(resp => {
                resolve()
            })
            .catch(err => {
                reject(new BookingError(
                    "booking.api.remove: api call error",
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
    all,
    create,
    update,
    remove,
}

