<template>
    <footer class="flex w-full bg-gray-900 h-10 justify-between">
        <div class="flex">
            Footer
        </div>
        <div class="flex">
            <button
                class="border-gray-500 border-l text-white p-3 hover:bg-gray-800"
                @click="doRefresh"
                v-show="isDataTabLoaded"
                :title="loading ? 'Loading' : 'Refresh'"
            >
                <FA
                    :icon="['fas', 'sync-alt']"
                    :class="{ 'fa-spin': loading }"
                ></FA>
            </button>
            <button
                class="border-gray-500 border-l text-white p-3 hover:bg-gray-800 relative"
                :class="{
                    'text-red-500': errors.list.length,
                    'text-white': !errors.list.length
                }"
                title="Debug Panel"
                @click="openDebugPanel"
            >
                <FA :icon="['fas', 'bug']"></FA>

                <div
                    v-if="errors.list.length"
                    class="bg-red-500 absolute rounded-lg text-center h-4 w-4 text-white py-1 badge"
                >
                    {{ errors.list.length }}
                </div>
            </button>
            <div class="border-gray-500 border-l text-white p-3 text-sm">
                &copy;{{ getYear }}, {{ store_settings.name }}
            </div>
        </div>
    </footer>
</template>

<script>
import moment from "moment";
import { mapState, mapActions } from "vuex";
import DebugPanel from '../Footer/DebugPanel.vue';

export default {
    data() {
        return {};
    },

    computed: {
        loading() {
            return false;
        },

        isDataTabLoaded() {
            return true;
        },

        getYear() {
            return moment().format("YYYY");
        },

        ...mapState({
            store_settings: state => state.store_settings,
            errors: state => state.errors,
            tab_to_refresh: state => state.framework.tab_to_refresh,
            active_tab: state => state.local_settings.active_tab,
        })
    },

    methods: {
        doRefresh() {},

        openDebugPanel() {
            this.setPopperOpen(true);

            this.$modal.show(
                DebugPanel,
                {},
                {
                    width: '80%',
                    height: '80%'
                },
                {
                    'closed': this.closeDebugPanel
                });
        },

        closeDebugPanel() {
            this.setPopperOpen(false);
        },

        ...mapActions({
            setPopperOpen: 'local_settings/setPopperOpen'
        })
    }
};
</script>
