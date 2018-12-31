import axios from 'axios'

import { ROOMS_REQUEST, ROOMS_SUCCESS, ROOMS_ERROR } from './actions.js'

const state = {
    status: '',
    rooms: [],
}

const getters = {
    getRooms: state => state.rooms,
    areRoomsLoaded: state => !!state.rooms.length,
}

const actions = {
    [ROOMS_REQUEST]: ({commit, dispatch}) => {
        commit(ROOMS_REQUEST)
        axios({
            method: 'get',
            url: '/api/rooms'
        })
            .then(resp => {
                commit(ROOMS_SUCCESS, resp)
            })
            .catch(resp => {
                commit(ROOMS_ERROR)
            })
    },
}

const mutations = {
    [ROOMS_REQUEST]: (state) => {
        state.status = 'loading'
    },
    [ROOMS_SUCCESS]: (state, resp) => {
        state.status = 'success'
        state.rooms = resp.data.data
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

