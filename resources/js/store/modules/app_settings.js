/**
 * State storage for some commonly used items.
 */

const state = {
    stock_types: "New,Kit,Partial,Refurb,Used,Others,ReTrade",
    networks: "BT Mobile,CPW,EE,EMEA,Giffgaff,O2,Orange,Sainsbury,Tesco,Three Mobile,T-Mobile,TPO Mobile,Unlocked,Virgin,Vodafone,Wifi only,Others",
    phone_sizes: "4 GB,8 GB,16 GB,32 GB,64 GB,128 GB,256 GB,512 GB,Other,None",
    stock_statuses: "In Stock,Returned,Sold,Rejected,Others",
    payment_types: "Card,Cash,Cheque,Online"
}

const getters = {
    stock_types: (state) => {
        return state.stock_types;
    },
    networks: (state) => {
        return state.networks;
    },
    phone_sizes: (state) => {
        return state.phone_sizes;
    },
    stock_statuses: (state) => {
        return state.stock_statuses;
    },
    payment_types: (state) => {
        return state.payment_types;
    }
}

const actions = {
    setAppSettings({commit}, payload) {
        commit('setAppSettings', payload);
    }
}

const mutations = {
    setAppSettingsFromAppSettings(state, payload) {
        console.log(['restore app_settings from app_settings', payload]);
        for (let key in payload) {
            if (state.hasOwnProperty(key)) {
                state[key] = payload[key];
            }
        }
    },

    setAppSettings(state, settings) {
        console.log(settings);
        state.stock_types = settings.stock_types;
        state.networks = settings.networks;
        state.phone_sizes = settings.phone_sizes;
        state.stock_statuses = settings.stock_statuses;
        state.payment_types = settings.payment_types;
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
