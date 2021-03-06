import Vue from 'vue'

import api from './api.js'
import { ROOMS_REQUEST, ROOMS_SUCCESS, ROOMS_ERROR } from './actions.js'

const state = {
    status: '',
    rooms: [],
}

const getters = {
    getRooms: state => state.rooms,
    areRoomsLoaded: state => state.status === 'success',
    areRoomsLoading: state => state.status === 'loading',
}

const actions = {
    [ROOMS_REQUEST]: ({commit, dispatch}) => {
        return new Promise((resolve, reject) => {
            if (!state.status !== 'loading') {
                commit(ROOMS_REQUEST)
                api.all()
                    .then(rooms => {
                        commit(ROOMS_SUCCESS, rooms)
                    })
                    .catch(err => {
                        commit(ROOMS_ERROR)
                    })
            }
        })
    },
}

const mutations = {
    [ROOMS_REQUEST]: (state) => {
        state.status = 'loading'
    },
    [ROOMS_SUCCESS]: (state, rooms) => {
        state.status = 'success'
        state.rooms = rooms
    },
    [ROOMS_ERROR]: (state) => {
        state.status = 'error'
    },
}

export default {
    state,
    getters,
    actions,
    mutations,
}

