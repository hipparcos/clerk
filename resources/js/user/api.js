import axios from 'axios'

import err from '../error/lib.js'

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
class UserError extends err.APIError {
    constructor({
        message = 'user.api: error',
        status = 0,
        data = {},
    }) {
        super({ message, status, data, fields: {
            // User properties <=> api field.
            'name': 'data.attributes.name',
            'email': 'data.attributes.email',
            'password': 'data.attributes.password',
            'password_confirmation': 'data.attributes.password_confirmation',
        }})
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
                reject(new UserError({
                    message: 'user.api.profile: api call error',
                    status: err.response.status,
                    data: err.response.data,
                }))
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
                reject(new UserError({
                    message: "user.api.register: api call error",
                    status: err.response.status,
                    data: err.response.data,
                }))
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

