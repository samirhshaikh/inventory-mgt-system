import Vue from 'vue';
import Vuex from 'vuex';
import 'es6-promise/auto';
import dbSettings from '../Helpers/dbSettings';
import framework from './modules/framework';
import store_settings from './modules/store_settings';
import local_settings from './modules/local_settings';
import datatable from './modules/datatable';
import errors from './modules/errors';

Vue.use(Vuex);

const debug = process.env.NODE_ENV !== 'production';

export default new Vuex.Store({
    plugins: [dbSettings],

    modules: {
        framework,
        store_settings,
        local_settings,
        datatable,
        errors
    },

    strict: debug
})
