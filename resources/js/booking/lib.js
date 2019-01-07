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

const roomSorter = (l, r) => {
    let lroom = l.room.name
    let rroom = r.room.name
    return lroom.localeCompare(rroom)
}

const userSorter = (l, r) => {
    let luser = l.user.name
    let ruser = r.user.name
    return luser.localeCompare(ruser)
}

const timeSorter = (l, r) => {
    return l.start.isBefore(r.start) ? -1 : 1
}

export default {
    // const
    slot,
    unit,
    // func
    nextSlot,
    // compareFunctions
    roomSorter,
    userSorter,
    timeSorter,
}
