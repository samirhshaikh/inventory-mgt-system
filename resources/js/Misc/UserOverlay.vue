<template>
    <div
        class="flex h-full border border-product-color"
        :class="{
            'bg-gray-700 text-white': dark_mode
        }"
    >
        <div
            class="w-56 bg-gray-900 text-white h-full justify-between flex flex-col"
        >
            <div>
                <div class="flex flex-col bg-gray-800 px-4 py-8 items-center">
                    <img
                        :src="getAvatar"
                        class="w-24 h-24 border-2 border-color-white rounded-full"
                    />
                    <span class="mt-4">{{ $page.user }}</span>
                </div>

                <div class="flex flex-col p-4">
                    <Button
                        icon="sign-out-alt"
                        split="border-gray-900"
                        class="mt-2 bg-red-600"
                        @click.native="change_route(route('doLogout'))"
                        >Logout</Button
                    >
                </div>
            </div>

            <div class="px-4 py-2 text-xs text-gray-200">
                IMS v{{ version }}
            </div>
        </div>

        <div class="flex-grow flex flex-col justify-between">
            <div class="p-4 overflow-y-auto text-sm flex-grow">
                <h1
                    class="border-b border-product-color-lighter mb-4 pb-1 text-xl"
                    :class="{
                        'text-product-color-lighter': dark_mode,
                        'text-product-color': !dark_mode
                    }"
                >
                    Settings
                </h1>

                <div class="flex items-center">
                    <toggle-button
                    :value="dark_mode"
                    :labels="true"
                    @change="toggleDarkmode()"
                    color="#1380B6"
                    /><span class="ml-2">Dark Mode</span>
                </div>

                <div class="flex items-center mt-5">
                    <span class="mr-2">Page Size:</span>

                    <v-select
                        :value="page_size"
                        @input="setPagesize"
                        :options="page_sizes"
                        label="Code"
                        class="w-24 generic_vs_select"
                        v-bind:clearable="false"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapState, mapActions } from "vuex";
import moment from "moment";
import Button from "../components/Button";

export default {
    data() {
        return {
            version: require("../../../package.json").version,
            page_sizes: [10, 20, 30, 40, 50]
        };
    },

    methods: {
        change_route(url) {
            location.replace(url);
        },

        ...mapActions({
            toggleDarkmode: 'framework/toggleDarkmode',
            setPagesize: 'framework/setPagesize'
        })
    },

    computed: {
        getAvatar() {
            return "https://i.picsum.photos/id/402/200/200.jpg?hmac=9PZqzeq_aHvVAxvDPNfP6GuD58m4rilq-TUrG4e7V80";
        },

        ...mapState({
            dark_mode: state => state.framework.dark_mode,
            expanded_sidebar: state => state.db.expanded_sidebar,
            page_size: state => state.framework.page_size,
        })
    }
};
</script>
