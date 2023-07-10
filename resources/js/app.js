import { createApp, h } from "vue";
import store from "./store";

const _ = require("lodash");

import "core-js-bundle/";
require("es6-promise/auto");

import { createInertiaApp } from "@inertiajs/vue3";
import moment from "moment";
// import VModal from "vue-js-modal";
// import ToggleButton from "vue-js-toggle-button";
// import Notifications from "vue-notification";
import VueGoogleAutocomplete from "vue-google-autocomplete";
import vSelect from "vue-select";
// import DatePicker from "v-calendar/lib/components/date-picker.umd";
// import VuePdfApp from "vue-pdf-app";

//Start: Fontawesome
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

// import {} from '@fortawesome/free-regular-svg-icons';

import {
    faSpinner,
    faSignOutAlt,
    faAngleDoubleLeft,
    faAngleDoubleRight,
    faSyncAlt,
    faBug,
    faSearch,
    faCog,
    faUsers,
    faUser,
    faMobileAlt,
    faIndustry,
    faHome,
    faPalette,
    faArrowUp,
    faArrowDown,
    faTimes,
    faCheck,
    faPlus,
    faMinus,
    faBuilding,
    faCalendarAlt,
    faMagic,
    faPen,
    faTrash,
    faFileAlt,
    faStore,
    faRedoAlt,
} from "@fortawesome/free-solid-svg-icons";

library.add(
    faSpinner,
    faSignOutAlt,
    faAngleDoubleLeft,
    faAngleDoubleRight,
    faSyncAlt,
    faBug,
    faSearch,
    faCog,
    faUsers,
    faUser,
    faMobileAlt,
    faIndustry,
    faHome,
    faPalette,
    faArrowUp,
    faArrowDown,
    faTimes,
    faCheck,
    faPlus,
    faMinus,
    faBuilding,
    faCalendarAlt,
    faMagic,
    faPen,
    faTrash,
    faFileAlt,
    faStore,
    faRedoAlt
);

//End: Fontawesome

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require("axios");
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

window.axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (typeof error.response === "undefined") {
            window.location = route("login");
        }
        return Promise.reject(error);
    }
);

var Turbolinks = require("turbolinks");
Turbolinks.start();

// app.use(VModal, { dynamic: true, injectModalsContainer: true });
// app.use(ToggleButton);
// app.use(Notifications);
// app.component("date-picker", DatePicker);
// app.component("vue-pdf-app", VuePdfApp);

import Layout from "../js/Layout";

createInertiaApp({
    resolve: (name) => require(`./Pages/${name}`),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin);

        app.component("FA", FontAwesomeIcon);
        app.component("vue-google-autocomplete", VueGoogleAutocomplete);
        app.component("v-select", vSelect);

        //Register all the Vue components
        const files = require.context("./", true, /\.vue$/i);
        files.keys().map((key) => {
            // console.log([key.split("/").pop().split(".")[0], files(key).default]);
            app.component(
                key.split("/").pop().split(".")[0],
                files(key).default
            );
        });

        app.config.globalProperties.moment = moment;

        app.mixin({
            methods: { route: (...args) => window.route(...args).url() },
        });

        app.config.globalProperties.phonestock = {
            STATUS_IN_STOCK: "In Stock",
            STATUS_SOLD: "Sold",
        };

        app.use(store);

        app.config.globalProperties.$route = route;

        app.mount(el);
    },
});
