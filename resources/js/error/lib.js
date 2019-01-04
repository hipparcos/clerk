/**
 * APIError is the type of an api error.
 */
class APIError extends Error {
    // @param {Number} status HTTP status code
    // @param {Object} data HTTP response body
    // @param {Object} fields model properties <=> api fields.
    constructor({
        message = '',
        fileName = undefined,
        lineNumber = undefined,
        status = 0,
        data = {},
        fields = {},
    }) {
        super(message, fileName, lineNumber)
        this.status = status
        this.data = data
        // Model properties <=> api fields.
        this.fields = fields
    }

    description() {
        if ('message' in this.data) {
            return this.data.message
        }
    }

    hasErrors(field) {
        let containErrors = this.data && ('errors' in this.data)
        return (containErrors || this.status >= 400)
            && (!field || (
                field in this.fields && this.fields[field] in this.data.errors))
    }

    errorsFor(field) {
        if (this.hasErrors(field)) {
            return this.data.errors[this.fields[field]]
        }
        return []
    }
}

export default {
    APIError,
}
