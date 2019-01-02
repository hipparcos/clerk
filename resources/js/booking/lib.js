import moment from 'moment'

const slot = 15

const unit = 'minutes'

const nextSlot = () => {
    let now = moment()
    let nextHour = moment().endOf('hour').add(1, 'minutes')
    let diff = nextHour.diff(now, unit)
    let diffSlot = Math.floor(diff/slot)
    return nextHour.subtract(diffSlot * slot, unit)
}

export default {
    // const
    slot,
    unit,
    // func
    nextSlot,
}
