import moment from 'moment'

class Event {
    constructor({
        name = '',
        start = null,
        duration = 0,
        data = {},
    }) {
        this.name = name
        this.start = start
        this.duration = duration
        this.data = data
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
