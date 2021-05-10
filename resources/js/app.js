import Vue from 'vue';
import store from './store';

var _ = require('lodash');

import 'core-js-bundle/';
require('es6-promise/auto');

import { InertiaApp } from '@inertiajs/inertia-vue';
import moment from 'moment';
import Popper from 'vue-popperjs';
import VModal from 'vue-js-modal';
import ToggleButton from 'vue-js-toggle-button';
import Notifications from 'vue-notification';
import VueGoogleAutocomplete from 'vue-google-autocomplete';
import vSelect from 'vue-select';
import 'vue-popperjs/dist/vue-popper.css';
import DatePicker from 'v-calendar/lib/components/date-picker.umd';
import VueMask from 'v-mask';

//Start: Fontawesome
import { library } from '@fortawesome/fontawesome-svg-core';
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
    faFileAlt
} from '@fortawesome/free-solid-svg-icons';

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

library.add([
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
    faFileAlt
]);

Vue.component('FA', FontAwesomeIcon);
//End: Fontawesome

Vue.component('popper', Popper);

//Register all the Vue components
const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.prototype.moment = moment;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.axios.interceptors.response.use((response) => response, (error) => {
    if (typeof error.response === 'undefined') {
        window.location = route('login');
    }
    return Promise.reject(error);
});

var Turbolinks = require("turbolinks");
Turbolinks.start();

Vue.config.productionTip = false;
Vue.mixin({ methods: { route: (...args) => window.route(...args).url() } });
Vue.use(InertiaApp);
Vue.use(VModal, {dynamic: true, injectModalsContainer: true});
Vue.use(ToggleButton);
Vue.use(Notifications);
Vue.use(VueMask);
Vue.component('vue-google-autocomplete', VueGoogleAutocomplete);
Vue.component('v-select', vSelect);
Vue.component('date-picker', DatePicker);

Vue.prototype.phonestock = {
    STATUS_IN_STOCK: "In Stock",
    STATUS_SOLD: "Sold"
};

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = document.getElementById('app');
new Vue({
    store,
    render: h => h(InertiaApp, {
        props: {
            initialPage: JSON.parse(app.dataset.page),
            resolveComponent: (component) => {
                return import (`@/${component}`).then(module => module.default)
            },
        },
    })
}).$mount(app);
