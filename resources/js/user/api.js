import axios from 'axios'

/**
 * User is a clerk user.
 */
const User = function({
        id = undefined,
        name = '',
        email = '',
    }) {
    this.id = id
    this.name = name
    this.email = email
}

/**
 * UserError contain the description of a log in error.
 */
class UserError extends Error {
    constructor(message, status, data) {
        super(message)
        this.status = status
        this.data = data
        // User field <=> api field.
        this.fields = {
            'name': 'data.attributes.name',
            'email': 'data.attributes.email',
            'password': 'data.attributes.password',
            'password_confirmation': 'data.attributes.password_confirmation',
        }
    }

    description() {
        if ('message' in this.data) {
            return this.data.message
        }
    }

    hasErrors(field) {
        let containErrors = this.data && ('errors' in this.data)
        return containErrors
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

/**
 * profile returns the profile of the current user.
 * @returns {Promise}.then(User).catch(UserError)
 */
const profile = function() {
    return new Promise((resolve, reject) => {
        axios({
            method: 'get',
            url: '/api/profile'
        })
            .then(resp => {
                let data = resp.data.data
                resolve(new User({
                    id: data.id,
                    name: data.attributes.name,
                    email: data.attributes.email
                }))
            })
            .catch(err => {
                reject(new UserError(
                    err.response.status,
                    err.response.data
                ))
            })
    })
}

/**
 * register creates a user.
 * @param {string} name
 * @param {string} email
 * @param {string} password
 * @param {string} password_confirmation
 * @returns {Promise}.then(User).catch(UserError)
 */
const register = function(name, email, password, password_confirmation) {
    return new Promise((resolve, reject) => {
        axios({
            method: 'post',
            url: '/api/register',
            data: {
                data: {
                    type: "user",
                    attributes: {
                        name: name,
                        email: email,
                        password: password,
                        password_confirmation: password_confirmation,
                    }
                }
            }
        })
            .then(resp => {
                let data = resp.data.data
                resolve(new User({
                    id: data.id,
                    name: data.attributes.name,
                    email: data.attributes.email
                }))
            })
            .catch(err => {
                reject(new UserError(
                    "user.api.register: api call error",
                    err.response.status,
                    err.response.data,
                ))
            })
    })
}

export default {
    // data.
    User,
    UserError,
    // api calls.
    profile,
    register,
}

