const validateEvent = function(obj) {
    return ('name' in obj)
        && ('start' in obj)
        && ('duration' in obj)
}

export default {
    validateEvent,
}
