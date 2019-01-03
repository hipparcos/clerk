import axios from 'axios'

/**
 * user is a clerk user.
 */
const user = function(id, name, email) {
    this.id = id
    this.name = name
    this.email = email
}

/**
 * error contain the description of a log in error.
 */
const error = function(status, data) {
    this.status = status
    this.data = data
}

/**
 * profile returns the profile of the current user.
 * @returns {Promise}.then(user).catch(error)
 */
const profile = function() {
    return new Promise((resolve, reject) => {
        axios({
            method: 'get',
            url: '/api/profile'
        })
            .then(resp => {
                let data = resp.data.data
                resolve(new user(
                    data.id,
                    data.attributes.name,
                    data.attributes.email
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
    user,
    // api calls.
    profile,
}

