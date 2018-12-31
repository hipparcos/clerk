import axios from 'axios'

import { AUTH_REQUEST, AUTH_ERROR, AUTH_SUCCESS, AUTH_LOGOUT } from './actions.js'
import { USER_REQUEST } from '../user/actions.js'

import config from './config.js'

const state = {
    token: localStorage.getItem('user-token') || '',
    status: '',
    hasLoadedOnce: false
}

// If token is already set.
if (state.token) {
    axios.defaults.headers.common['Authorization'] = 'Bearer ' + state.token
}

const getters = {
    isAuthenticated: state => !!state.token,
    authStatus: state => state.status,
}

const actions = {
    [AUTH_REQUEST]: ({commit, dispatch}, user) => {
        return new Promise((resolve, reject) => {
            commit(AUTH_REQUEST)
            axios({
                method: 'post',
                url: '/oauth/token',
                data: {
                    grant_type:    config.grant_type,
                    client_id:     config.client_id,
                    client_secret: config.client_secret,
                    username:      user.email,
                    password:      user.password,
                    scope:         config.scope,
                }
            })
                .then(resp => {
                    localStorage.setItem('user-token', resp.data.access_token)
                    axios.defaults.headers.common['Authorization'] =
                        'Bearer ' + resp.data.access_token
                    commit(AUTH_SUCCESS, resp)
                    dispatch(USER_REQUEST)
                    resolve(resp)
                })
                .catch(err => {
                    commit(AUTH_ERROR, err)
                    localStorage.removeItem('user-token')
                    reject(err)
                })
        })
    },
    [AUTH_LOGOUT]: ({commit, dispatch}) => {
        return new Promise((resolve, reject) => {
            commit(AUTH_LOGOUT)
            localStorage.removeItem('user-token')
            delete axios.defaults.headers.common['Authorization']
            resolve()
        })
    },
}

const mutations = {
    [AUTH_REQUEST]: (state) => {
        state.status = 'loading'
    },
    [AUTH_SUCCESS]: (state, resp) => {
        state.status = 'success'
        state.token = resp.data.access_token
        state.hasLoadedOnce = true
    },
    [AUTH_ERROR]: (state) => {
        state.status = 'error'
        state.hasLoadedOnce = true
    },
    [AUTH_LOGOUT]: (state) => {
        state.token = ''
    }
}

export default {
    state,
    getters,
    actions,
    mutations,
}

