<template>
    <VueFinalModal
        class="flex justify-center items-center text-sm"
        :content-class="[
            'user_settings_modal relative p-4 rounded-lg bg-gray-900',
            {
                'bg-gray-700': dark_mode,
                'bg-white': !dark_mode,
            },
        ]"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
    >
        <div class="flex flex-row h-full">
            <div
                class="w-56 bg-gray-900 text-white h-full justify-between flex flex-col"
            >
                <div
                    class="flex flex-col bg-gray-800 px-4 py-8 items-center text-center"
                >
                    <div>
                        <img
                            :src="getAvatar"
                            class="w-24 h-24 border-2 border-color-white rounded-full"
                        />
                        <span class="mt-4">{{ page.user }}</span>
                    </div>

                    <div class="p-4">
                        <Button
                            icon="sign-out-alt"
                            split="border-gray-900"
                            class="mt-2 bg-red-600"
                            @click.native="change_route($route('doLogout'))"
                            >Logout</Button
                        >
                    </div>
                </div>

                <div class="px-4 py-2 text-xs text-gray-200">
                    IMS v{{ version }}
                </div>
            </div>

            <div
                class="grow flex flex-col p-4 h-full"
                :class="{
                    'text-white': dark_mode,
                    'text-black': !dark_mode,
                }"
            >
                <h1
                    class="border-b border-product-color-lighter mb-4 pb-1 text-xl"
                    :class="{
                        'text-product-color-lighter': dark_mode,
                        'text-product-color': !dark_mode,
                    }"
                >
                    Settings
                </h1>

                <div class="flex items-center">
                    <input
                        type="checkbox"
                        class="toggle text-orange-950"
                        :checked="dark_mode"
                        @change="toggleDarkmode"
                    />
                    <span class="ml-2">Dark Mode</span>
                </div>

                <div class="mt-5">
                    <span class="mr-2">Page Size:</span>

                    <v-select
                        v-model="page_size"
                        @option:selected="setPagesize"
                        :options="page_sizes"
                        label="Code"
                        class="w-24 generic_vs_select"
                        v-bind:clearable="false"
                    />
                </div>
            </div>
        </div>
    </VueFinalModal>
</template>

<style>
.user_settings_modal {
    width: 600px;
    height: auto;
}
</style>

<script>
import { mapState, mapActions } from "vuex";
import { VueFinalModal } from "vue-final-modal";
import { usePage } from "@inertiajs/vue3";
import Toggle from "../Datatable/Cells/Toggle";

const page = usePage();

export default {
    components: {
        Toggle,
        VueFinalModal,
    },

    data() {
        return {
            version: require("../../../package.json").version,
            page_sizes: [10, 20, 30, 40, 50],
        };
    },

    methods: {
        change_route(url) {
            location.replace(url);
        },

        ...mapActions({
            toggleDarkmode: "framework/toggleDarkmode",
            setPagesize: "framework/setPagesize",
        }),
    },

    computed: {
        getAvatar() {
            return "https://fastly.picsum.photos/id/223/200/200.jpg?hmac=CNNyWbBcEAJ7TPkTmEEwdGrLFEYkxpTeVwJ7U0LB30Y";
        },

        ...mapState({
            dark_mode: (state) => state.framework.dark_mode,
            expanded_sidebar: (state) => state.db.expanded_sidebar,
            page_size: (state) => state.framework.page_size,
        }),

        page() {
            return page.props;
        },
    },
};
</script>
