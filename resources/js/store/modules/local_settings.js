/**
 * State storage for local data. Will not be sent to server for storing in app settings.
 */

const state = {
    active_tab: null,
    popper_open: false,
    popper_active_tab: null,
    cached_data: {},
    is_IE_11: !!window.MSInputMethodContext && !!document.documentMode
}

const getters = {
    active_tab: (state) => {
        return state.active_tab;
    },

    popper_open: (state) => {
        return state.popper_open;
    },

    popper_active_tab: (state) => {
        return state.popper_active_tab;
    },

    cached_data: (state) => {
        return state.cached_data;
    }
}

const actions = {
    setActiveTab({ commit }, payload) {
        commit('setActiveTab', payload);
    },

    setPopperOpen({ commit }, payload) {
        commit('setPopperOpen', payload);
    },

    setPopperActiveTab({ commit }, payload) {
        commit('setPopperActiveTab', payload);
    },

    setCachedData({commit}, payload) {
        commit('setCachedData', payload)
    },

    resetCachedData({commit}, key) {
        commit('resetCachedData', key)
    }
}

const mutations = {
    setActiveTab(state, tab) {
        state.active_tab = tab;
    },

    setPopperOpen(state, value) {
        state.popper_open = value;
    },

    setPopperActiveTab(state, value) {
        state.popper_active_tab = value;
    },

    setCachedData(state, payload) {
        state.cached_data[payload.key] = payload.data;
    },

    resetCachedData(state, key) {
        if (state.cached_data.hasOwnProperty(key)) {
            state.cached_data[key] = [];
        }
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
