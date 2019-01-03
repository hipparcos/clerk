import axios from 'axios'

import { AUTH_REQUEST, AUTH_ERROR, AUTH_SUCCESS, AUTH_LOGOUT } from './actions.js'
import { USER_REQUEST } from '../user/actions.js'

import api from './api.js'

const state = {
    token: localStorage.getItem('auth-token') || '',
    expires_in: Number(localStorage.getItem('auth-expires_in')) || 0,
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
            api.login(user.email, user.password)
                .then(token => {
                    localStorage.setItem('auth-token', token.access_token)
                    localStorage.setItem('auth-expires_in', token.expires_in)
                    axios.defaults.headers.common['Authorization'] =
                        'Bearer ' + token.access_token
                    commit(AUTH_SUCCESS, token)
                    dispatch(USER_REQUEST)
                    resolve(token)
                })
                .catch(err => {
                    commit(AUTH_ERROR, err)
                    localStorage.removeItem('auth-token')
                    reject(err)
                })
        })
    },
    [AUTH_LOGOUT]: ({commit, dispatch}) => {
        return new Promise((resolve, reject) => {
            commit(AUTH_LOGOUT)
            localStorage.removeItem('auth-token')
            delete axios.defaults.headers.common['Authorization']
            resolve()
        })
    },
}

const mutations = {
    [AUTH_REQUEST]: (state) => {
        state.status = 'loading'
    },
    [AUTH_SUCCESS]: (state, token) => {
        state.status = 'success'
        state.token = token.access_token
        state.expires_in = token.expires_in
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

