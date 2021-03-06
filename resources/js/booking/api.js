import axios from 'axios'
import moment from 'moment'

import err from '../error/lib.js'
import user from '../user/api.js'
import room from '../room/api.js'
import schedule from '../ui/schedule/lib.js'

/**
 * Booking is a clerk booking.
 */
class Booking extends schedule.Event {
    constructor({
            id = undefined,
            start = moment(),
            duration = 15,
            room = {},
            user = {},
        }) {
        super({ id, start, duration })
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
        booking.room = room.Room.fromAPI(roomData)
        let userData = data.relationships.user.data
        booking.user = user.User.fromAPI(userData)
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
class BookingError extends err.APIError {
    constructor({
        message = 'booking.api: error',
        status = 0,
        data = {},
    }) {
        super({ message, status, data, fields: {
            // User properties <=> api field.
            'start': 'data.attributes.start',
            'duration': 'data.attributes.duration',
            'room': 'data.relationships.room.data.id',
        }})
    }

    conflict() {
        return this.status == 409
    }

    isOverridable() {
        return this.hasErrors
            && ('meta.overridable' in this.data.errors)
            && this.data.errors['meta.overridable'][0] === true
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
                reject(new BookingError({
                    message: "booking.api.create: api call error",
                    status: err.response.status,
                    data: err.response.data,
                }))
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
                reject(new BookingError({
                    message: "booking.api.all: api call error",
                    status: err.response.status,
                    data: err.response.data,
                }))
            })
    })
}

/**
 * get retrieves a single booking from the API.
 * @param {Number} id
 * @returns {Promise}.then(Booking).catch(BookingError)
 */
const get = function(id) {
    return new Promise((resolve, reject) => {
        axios({
            method: 'get',
            url: '/api/bookings/' + id,
        })
            .then(resp => {
                let data = resp.data.data
                let booking = Booking.fromAPI(data)
                resolve(booking)
            })
            .catch(err => {
                reject(new BookingError({
                    message: "booking.api.get: api call error",
                    status: err.response.status,
                    data: err.response.data,
                }))
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
                resolve(id)
            })
            .catch(err => {
                reject(new BookingError({
                    message: "booking.api.remove: api call error",
                    status: err.response.status,
                    data: err.response.data,
                }))
            })
    })
}

export default {
    // data.
    Booking,
    BookingError,
    // api calls.
    all,
    get,
    create,
    update,
    remove,
}

