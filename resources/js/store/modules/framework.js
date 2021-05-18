/**
 * State storage for framework settings. Like expanded side bar, dark mode, page size, etc.
 */

import moment from 'moment';

const state = {
    expanded_sidebar: true,
    dark_mode: false,
    page_size: 10,
    refresh_data: false,
    refresh_suppliers: false,
    refresh_customers: false,
    refresh_handset_models: false,
    refresh_handset_manufacturers: false,
    refresh_handset_colors: false,
    tab_to_refresh: ''
}

const getters = {
    expanded_sidebar: (state) => {
        return new Boolean(state.expanded_sidebar);
    },

    dark_mode: (state) => {
        return new Boolean(state.dark_mode);
    },

    page_size: (state) => {
        return state.page_size;
    },

    refresh_data: (state) => {
        return state.refresh_data;
    },

    refresh_suppliers: (state) => {
        return state.refresh_suppliers;
    },

    refresh_customers: (state) => {
        return state.refresh_customers;
    },

    refresh_handset_models: (state) => {
        return state.refresh_handset_models;
    },

    refresh_handset_manufacturers: (state) => {
        return state.refresh_handset_manufacturers;
    },

    refresh_handset_colors: (state) => {
        return state.refresh_handset_colors;
    }
}

const actions = {
    toggleSidebar({state, commit}) {
        commit('setSidebar', !state.expanded_sidebar);
    },

    toggleDarkmode({state, commit}) {
        commit('setDarkmode', !state.dark_mode);
    },

    setPagesize({commit}, page_size) {
        commit('setPagesize', page_size);
    },

    refreshData({commit}, tab) {
        commit('refreshData', tab);
    },

    setTabToRefresh({ commit }, payload) {
        commit('setTabToRefresh', payload);
    },

    refreshSuppliers({commit}) {
        commit('refreshSuppliers');
    },

    refreshCustomers({commit}) {
        commit('refreshCustomers');
    },

    refreshHandsetModels({commit}) {
        commit('refreshHandsetModels');
    },

    refreshHandsetManufacturers({commit}) {
        commit('refreshHandsetManufacturers');
    },

    refreshHandsetColors({commit}) {
        commit('refreshHandsetColors');
    }
}

const mutations = {
    setFrameworkFromAppSettings(state, payload) {
        console.log(['restoring framework from app_settings', payload]);
        for (let key in payload) {
            if (state.hasOwnProperty(key)) {
                switch (key) {
                    case 'dark_mode':
                        state[key] = (payload[key] != '0');
                        break;
                    case 'page_size':
                        state[key] = Number(payload[key]);
                        break;
                    default:
                        state[key] = payload[key];
                        break;
                }
            }
        }
    },

    setSidebar(state, value) {
        state.expanded_sidebar = value;
    },

    setDarkmode(state, value) {
        state.dark_mode = value;
    },

    setPagesize(state, page_size) {
        state.page_size = page_size;
    },

    refreshData(state, tab) {
        state.refresh_data = moment().format();
        state.tab_to_refresh = tab;
    },

    setTabToRefresh(state, tab) {
        state.tab_to_refresh = tab;
    },

    refreshSuppliers(state) {
        state.refresh_suppliers = moment().format();
    },

    refreshCustomers(state) {
        state.refresh_customers = moment().format();
    },

    refreshHandsetModels(state) {
        state.refresh_handset_models = moment().format();
    },

    refreshHandsetManufacturers(state) {
        state.refresh_handset_manufacturers = moment().format();
    },

    refreshHandsetColors(state) {
        state.refresh_handset_colors = moment().format();
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
