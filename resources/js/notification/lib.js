const Type = {
    success: 'success',
    error: 'error',
}

/**
 * Notification is a simple notification.
 * @param {Type} type
 * @param {String} message
 * @param {Number} timeout in second
 */
class Notification {
    constructor({
        type = Type.success,
        message = '',
        timeout = 5,
    }) {
        this.id = 0
        this.type = type
        this.message = message
        this.timeout = timeout
    }
}

export default {
    Type,
    Notification,
}
