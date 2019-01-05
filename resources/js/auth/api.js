import axios from 'axios'
import config from './config.js'

import err from '../error/lib.js'

/**
 * Token is the result of an authentication request.
 */
const Token = function(token, expires) {
    this.access_token = token
    this.expires_in = expires
}

/**
 * AuthError contain the description of a log in error.
 */
class AuthError extends err.APIError {
    constructor({
        message = 'auth.api: error',
        status = 0,
        data = {},
    }) {
        super({ message, status, data, fields: {}})
    }

    hint() {
        if ('hint' in this.data) {
            return this.data.hint.replace('username', 'email')+'.'
        }
    }
}

/**
 * login perform a login API call.
 * @param {string} email
 * @param {string} password
 * @returns {Promise}.then(Token).catch(AuthError)
 */
const login = function(email, password) {
    return new Promise((resolve, reject) => {
        axios({
            method: 'post',
            url: '/oauth/token',
            data: {
                grant_type:    config.grant_type,
                client_id:     config.client_id,
                client_secret: config.client_secret,
                username:      email,
                password:      password,
                scope:         config.scope,
            }
        })
            .then(resp => {
                resolve(new Token(
                    resp.data.access_token,
                    resp.data.expires_in
                ))
            })
            .catch(err => {
                reject(new AuthError({
                    message: 'auth.api.login: api call error',
                    status: err.response.status,
                    data: err.response.data,
                }))
            })
    })
}

export default {
    // data.
    Token,
    AuthError,
    // api calls.
    login,
}
