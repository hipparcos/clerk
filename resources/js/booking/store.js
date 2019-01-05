import moment from 'moment'
import Vue from 'vue'

import {
    BOOKINGS_SET_DATE,
    BOOKINGS_SET_DATE_ERROR,
    BOOKINGS_REQUEST,
    BOOKINGS_SUCCESS,
    BOOKINGS_ERROR,
    BOOKINGS_SORT,
} from './actions.js'

import api from './api.js'

const state = {
    status: '',
    date: moment(),
    bookings: [],
}

const getters = {
    getSelectedDate: state => state.date,
    getBookings: state => state.bookings,
    getBookingsStatus: state => state.status,
    areBookingsLoading: state => state.status === 'loading',
    areBookingsLoaded: state => state.status === 'success',
}

const actions = {
    [BOOKINGS_SET_DATE]: ({commit, dispatch}, { date }) => {
        return new Promise((resolve, reject) => {
            if (date instanceof moment) {
                if (!date.isSame(state.date, 'day') || state.status === '') {
                    commit(BOOKINGS_SET_DATE, date)
                    dispatch(BOOKINGS_REQUEST)
                        .then(bookings => {
                            resolve(bookings)
                        })
                        .catch(err => {
                            reject(err)
                        })
                }
            } else {
                commit(BOOKINGS_SET_DATE_ERROR)
                reject()
            }
        })
    },
    [BOOKINGS_REQUEST]: ({commit, dispatch}) => {
        return new Promise((resolve, reject) => {
            commit(BOOKINGS_REQUEST)
            api.all(state.date)
                .then(bookings => {
                    commit(BOOKINGS_SUCCESS, bookings)
                    resolve(bookings)
                })
                .catch(err => {
                    commit(BOOKINGS_ERROR, err)
                    reject(err)
                })
        })
    },
    [BOOKINGS_SORT]: ({commit, dispatch}, { sorter }) => {
        if (getters.areBookingsLoaded) {
            commit(BOOKINGS_SORT, sorter)
        }
    },
}

const mutations = {
    [BOOKINGS_SET_DATE]: (state, date) => {
        state.date = date
    },
    [BOOKINGS_SET_DATE_ERROR]: (state, err) => {
        state.status = 'error'
    },
    [BOOKINGS_REQUEST]: (state) => {
        state.status = 'loading'
    },
    [BOOKINGS_SUCCESS]: (state, bookings) => {
        state.status = 'success'
        Vue.set(state, 'bookings', bookings)
    },
    [BOOKINGS_ERROR]: (state, err) => {
        state.status = 'error'
    },
    [BOOKINGS_SORT]: (state, sorter) => {
        let bookings = state.bookings.sort(sorter)
        Vue.set(state, 'bookings', bookings)
    },
}

export default {
    state,
    getters,
    actions,
    mutations,
}

