/**
 * State storage for errors.
 */

import moment from "moment";

const state = {
    list: [],
};

const getters = {
    list: (state) => {
        return state.list;
    },
};

const actions = {
    addError({ commit }, payload) {
        commit("addError", payload);
    },

    clearErrors({ commit }) {
        commit("clearErrors");
    },
};

const mutations = {
    addError(state, payload) {
        state.list.unshift({
            time: moment().format(),
            error: payload,
        });
    },

    clearErrors(state) {
        state.list = [];
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};
