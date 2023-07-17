import { createStore } from "vuex";

import "es6-promise/auto";
import dbSettings from "../Helpers/dbSettings";
import framework from "./modules/framework";
import app_settings from "./modules/app_settings";
import store_settings from "./modules/store_settings";
import local_settings from "./modules/local_settings";
import datatable from "./modules/datatable";
import errors from "./modules/errors";

const debug = process.env.NODE_ENV !== "production";

export default createStore({
    plugins: [dbSettings],

    modules: {
        framework,
        app_settings,
        store_settings,
        local_settings,
        datatable,
        errors,
    },

    strict: debug,
});
