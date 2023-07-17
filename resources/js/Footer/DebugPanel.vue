<template>
    <VueFinalModal
        class="flex justify-center items-center"
        :content-class="[
            'debug_panel_modal relative p-4 rounded-lg dark:bg-gray-900',
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
            class="w-4/5 flex flex-col flex-grow-0 justify-between overflow-auto relative"
        >
            <div class="tabs">
                <div
                    class="tab tab_lg tab-bordered"
                    name="Errors"
                    v-if="errors.list.length"
                    id="errors"
                >
                    <div
                        class="mb-6 rounded border border-red-700"
                        v-for="(item, key) in errors.list"
                        :key="key"
                    >
                        <div class="bg-red-700 text-white px-4 py-2 text-xs">
                            {{ moment(item.time).format("Do MMM Y - HH:mm") }}
                        </div>
                        <div class="p-4 text-sm">
                            {{ item.error }}
                        </div>
                    </div>
                </div>

                <tab name="Framework" id="framework">
                    <p class="bg-gray-600 text-white rounded py-3 px-3">
                        session.app_settings.framework
                    </p>
                    <div v-if="'app_settings' in session">
                        <vue-json-pretty
                            :data="session.app_settings.framework"
                            :showLength="true"
                            :deep="1"
                        ></vue-json-pretty>
                    </div>
                    <div v-else>No Data</div>
                </tab>

                <tab name="Local Settings" id="local_settings">
                    <p class="bg-gray-600 text-white rounded py-3 px-3">
                        local_settings
                    </p>
                    <div v-if="local_settings">
                        <vue-json-pretty
                            :data="local_settings"
                            :showLength="true"
                            :deep="1"
                        ></vue-json-pretty>
                    </div>
                    <div v-else>No Data</div>
                </tab>

                <tab name="Datatable" id="datatable">
                    <p class="bg-gray-600 text-white rounded py-3 px-3">
                        session.app_settings.datatable
                    </p>
                    <div v-if="'app_settings' in session">
                        <vue-json-pretty
                            :data="session.app_settings.datatable"
                            :showLength="true"
                            :deep="1"
                        ></vue-json-pretty>
                    </div>
                    <div v-else>No Data</div>
                </tab>

                <tab name="User" id="user_details">
                    <p class="bg-gray-600 text-white rounded py-3 px-3">
                        session.user_details
                    </p>
                    <div v-if="'user_details' in session">
                        <vue-json-pretty
                            :data="session.user_details"
                            :showLength="true"
                            :deep="1"
                        ></vue-json-pretty>
                    </div>
                    <div v-else>No Data</div>
                </tab>
            </div>
        </div>
    </VueFinalModal>
</template>

<style>
.debug_panel_modal {
    width: 80%;
    height: 80%;
}
</style>

<script>
import { mapState } from "vuex";
import { VueFinalModal } from "vue-final-modal";
import VueJsonPretty from "vue-json-pretty";

export default {
    components: {
        VueFinalModal,
        VueJsonPretty,
    },

    data() {
        return {
            session: {},
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

    computed: {
        ...mapState({
            dark_mode: (state) => state.framework.dark_mode,
            local_settings: (state) => state.local_settings,
            errors: (state) => state.errors,
        }),
    },
};
</script>
