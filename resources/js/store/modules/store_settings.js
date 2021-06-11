/**
 * State storage for some commonly used items.
 */

const state = {
    name: "Store Name",
    address: "",
    phone: "",
    email: ""
}

const getters = {
    name: (state) => {
        return state.name;
    },
    address: (state) => {
        return state.address;
    },
    phone: (state) => {
        return state.phone;
    },
    email: (state) => {
        return state.email;
    },
}

const actions = {
    setStoreSettings({commit}, payload) {
        commit('setStoreSettings', payload);
    }
}

const mutations = {
    setStoreSettingsFromAppSettings(state, payload) {
        console.log(['restore store_settings from app_settings', payload]);
        for (let key in payload) {
            if (state.hasOwnProperty(key)) {
                state[key] = payload[key];
            }
        }
    },

    setStoreSettings(state, settings) {
        state.name = settings.name;
        state.address = settings.address;
        state.phone = settings.phone;
        state.email = settings.email;
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
