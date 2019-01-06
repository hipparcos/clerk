import moment from 'moment'

class Event {
    constructor({
        id = 0,
        start = null,
        duration = 0,
        color = '',
    }) {
        this.id = id
        this.start = start
        this.duration = duration
        this.color = color
    }

    get start() {
        return this._start
    }
    set start(start) {
        this._start = moment(start)
    }
}

export default {
    Event,
}
