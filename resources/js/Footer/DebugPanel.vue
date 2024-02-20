<template>
    <VueFinalModal
        class="flex justify-center items-center"
        :content-class="[
            'debug_panel_modal relative p-4 rounded-lg',
            {
                'bg-gray-700': dark_mode,
                'bg-white': !dark_mode,
            },
        ]"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
    >
        <div class="p-0 overflow-y-auto text-sm">
            <div
                class="datatable_header"
                :class="{
                    'border-product-color-lighter': dark_mode,
                    'border-product-color': !dark_mode,
                }"
            >
                <h1
                    :class="{
                        'text-product-color-lighter': dark_mode,
                        'text-product-color': !dark_mode,
                    }"
                >
                    Quick Actions
                </h1>
            </div>
        </div>

        <div
            class="w-full h-full flex flex-col flex-grow-0 justify-between overflow-auto relative"
        >
            <div class="my_tab_container">
                <div class="my_tabs">
                    <div
                        @click.native="changeTab(tab.key)"
                        class="my_tab"
                        :class="[
                            {
                                active: active_tab === tab.key,
                            },
                        ]"
                        v-for="tab in [
                            { key: 'errors', label: 'Errors' },
                            { key: 'framework', label: 'Framework' },
                            { key: 'local_settings', label: 'Local Settings' },
                            { key: 'datatable', label: 'Datatable' },
                            { key: 'user_details', label: 'User' },
                        ]"
                    >
                        {{ tab.label }}
                    </div>
                </div>

                <div
                    class="my_tab_body"
                    v-if="active_tab === 'errors'"
                    :class="{
                        'bg-white': dark_mode,
                    }"
                >
                    <div
                        class="mb-6 rounded border border-red-700"
                        v-for="(item, key) in errors.list"
                        :key="key"
                    >
                        <div class="bg-red-700 text-white px-4 py-2 text-xs">
                            {{ getTime }}
                        </div>
                        <div class="p-4 text-sm">
                            {{ item.error }}
                        </div>
                    </div>
                </div>

                <div
                    class="my_tab_body"
                    v-if="active_tab === 'framework'"
                    :class="{
                        'bg-white': dark_mode,
                    }"
                >
                    <div v-if="'app_settings' in session">
                        <vue-json-pretty
                            :data="session.app_settings.framework"
                            :showLength="true"
                            :deep="1"
                        ></vue-json-pretty>
                    </div>
                    <div v-else>No Data</div>
                </div>

                <div
                    class="my_tab_body"
                    v-if="active_tab === 'local_settings'"
                    :class="{
                        'bg-white': dark_mode,
                    }"
                >
                    <div v-if="local_settings">
                        <vue-json-pretty
                            :data="local_settings"
                            :showLength="true"
                            :deep="1"
                        ></vue-json-pretty>
                    </div>
                    <div v-else>No Data</div>
                </div>

                <div
                    class="my_tab_body"
                    v-if="active_tab === 'datatable'"
                    :class="{
                        'bg-white': dark_mode,
                    }"
                >
                    <div v-if="'app_settings' in session">
                        <vue-json-pretty
                            :data="session.app_settings.datatable"
                            :showLength="true"
                            :deep="1"
                        ></vue-json-pretty>
                    </div>
                    <div v-else>No Data</div>
                </div>

                <div
                    class="my_tab_body"
                    v-if="active_tab === 'user_details'"
                    :class="{
                        'bg-white': dark_mode,
                    }"
                >
                    <div v-if="'user_details' in session">
                        <vue-json-pretty
                            :data="session.user_details"
                            :showLength="true"
                            :deep="1"
                        ></vue-json-pretty>
                    </div>
                    <div v-else>No Data</div>
                </div>
            </div>
        </div>
    </VueFinalModal>
</template>

<style>
.debug_panel_modal {
    width: 80%;
    height: 80%;
}
.my_tab_container .my_tab_body {
    min-height: calc(90vh - 255px);
    max-height: calc(90vh - 255px);
}
</style>

<script>
import moment from "moment";
import { mapState } from "vuex";
import { VueFinalModal } from "vue-final-modal";
import VueJsonPretty from "vue-json-pretty";
import "vue-json-pretty/lib/styles.css";

export default {
    components: {
        VueFinalModal,
        VueJsonPretty,
    },

    data() {
        return {
            session: {},
            active_tab: "errors",
        };
    },

    mounted() {
        axios.get("/getDebugInfo").then(
            (response) => {
                this.session = response.data.response.session;
            },
            (error) => {
                this.error_message = "Some error occurred. Please try again.";
            }
        );
    },

    methods: {
        changeTab(tab) {
            this.active_tab = tab;
        },
    },

    computed: {
        getTime() {
            return moment().format("Do MMM Y - HH:mm");
        },
        ...mapState({
            dark_mode: (state) => state.framework.dark_mode,
            local_settings: (state) => state.local_settings,
            errors: (state) => state.errors,
        }),
    },
};
</script>
