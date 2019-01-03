import axios from 'axios'
import Vue from 'vue'

import { USER_REQUEST, USER_ERROR, USER_SUCCESS } from './actions.js'
import { AUTH_LOGOUT } from './../auth/actions.js'

import api from './api.js'

const state = {
    status: '',
    profile: {}
}

const getters = {
    getProfile: state => state.profile,
    isProfileLoaded: state => !!state.profile.id,
}

const actions = {
    [USER_REQUEST]: ({commit, dispatch}) => {
        commit(USER_REQUEST)
        api.profile()
            .then(user => {
                commit(USER_SUCCESS, user)
            })
            .catch(err => {
                commit(USER_ERROR)
                dispatch(AUTH_LOGOUT)
            })
    },
}

const mutations = {
    [USER_REQUEST]: (state) => {
        state.status = 'loading'
    },
    [USER_SUCCESS]: (state, user) => {
        state.status = 'success'
        Vue.set(state, 'profile', user)
    },
    [USER_ERROR]: (state) => {
        state.status = 'error'
    },
    [AUTH_LOGOUT]: (state) => {
        state.profile = {}
    }
}

export default {
    state,
    getters,
    actions,
    mutations,
}

