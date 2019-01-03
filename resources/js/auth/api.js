import axios from 'axios'
import config from './config.js'

/**
 * token is the result of an authentication request.
 */
const token = function(token, expires) {
    this.access_token = token
    this.expires_in = expires
}

/**
 * error contain the description of a log in error.
 */
const error = function(status, data) {
    this.status = status
    this.data = data
}

/**
 * login perform a login API call.
 * @param {string} email
 * @param {string} password
 * @returns {Promise}.then(infos).catch(error)
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
                resolve(new token(
                    resp.data.access_token,
                    resp.data.expires_in
                ))
            })
            .catch(err => {
                reject(new error(
                    err.response.status,
                    err.response.data
                ))
            })
    })
}

export default {
    // data.
    token,
    error,
    // api calls.
    login,
}
