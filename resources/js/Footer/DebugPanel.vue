<template>
    <div
        class="flex h-full border border-product-color"
        :class="{
            'bg-gray-800 text-white': dark_mode,
        }"
    >
        <div
            class="w-1/5 bg-gray-900 text-white h-full flex flex-col justify-between"
        >
            <div class="h-10 p-4">
                <h1
                    class="text-product-color text-product-color-lighter mb-4 pb-1 text-xl tracking-tight"
                >
                    Quick Actions
                </h1>
            </div>
        </div>

        <div
            class="w-4/5 flex flex-col flex-grow-0 justify-between overflow-auto relative"
        >
            <PopperTabCollection>
                <tab name="Errors" v-if="errors.list.length" id="errors">
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
                </tab>

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
            </PopperTabCollection>
        </div>
    </div>
</template>

<script>
import { mapState } from "vuex";
import { moment } from "moment";
import VueJsonPretty from "vue-json-pretty";

export default {
    components: {
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
