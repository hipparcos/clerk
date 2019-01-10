import { NOTIFICATIONS_PUSH, NOTIFICATIONS_REMOVE, NOTIFICATIONS_ERROR } from './actions.js'

import lib from './lib.js'

const state = {
    nextId: 1,
    notifications: [],
    error: null,
}

const getters = {
    hasNotifications: state => state.notifications.length > 0,
    getNotifications: state => state.notifications,
}

const actions = {
    [NOTIFICATIONS_PUSH]: ({commit}, notification) => {
        if (notification instanceof lib.Notification) {
            commit(NOTIFICATIONS_PUSH, notification)
            return notification.id
        }
        commit(NOTIFICATIONS_ERROR, notification)
    },
    [NOTIFICATIONS_REMOVE]: ({commit, dispatch}, { id }) => {
        commit(NOTIFICATIONS_REMOVE, id)
    },
}

const mutations = {
    [NOTIFICATIONS_PUSH]: (state, notification) => {
        notification.id = state.nextId
        state.nextId++
        state.notifications.push(notification)
    },
    [NOTIFICATIONS_REMOVE]: (state, id) => {
        state.notifications = state.notifications.filter(n => n.id !== id)
    },
    [NOTIFICATIONS_ERROR]: (state, err) => {
        state.error = err
    },
}

export default {
    state,
    getters,
    actions,
    mutations,
}

