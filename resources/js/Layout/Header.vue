<template>
    <header
        class="z-50 flex w-full h-10 bg-product-color text-white fixed items-center"
    >
        <div
            class="flex flex-shrink-0 text-xl font-semibold text-center mt-0 mb-0 border-r border-white h-full content-center"
            :class="{
                'w-64': expanded_sidebar && !small_screen,
                'w-12': !expanded_sidebar || small_screen,
            }"
        >
            <div
                v-if="expanded_sidebar && !small_screen"
                class="mb-auto mt-auto truncate px-2 text-center w-full"
            >
                {{ store_settings.name }}
            </div>
            <div v-else class="mb-auto mt-auto truncate px-1 w-full">
                {{
                    store_settings.name
                        .split(" ")
                        .map((item) => item[0])
                        .join("")
                }}
            </div>
        </div>

        <div class="w-full">
            <div class="float-left flex justify-between"></div>

            <div class="float-right flex justify-between">
                <button
                    class="px-4 border-product-color-lighter border-l flex items-center text-sm h-10"
                    @click="showUserOverlay"
                >
                    <img
                        :src="getAvatar"
                        class="w-5 h-5 border-2 border-color-white rounded-full"
                    />
                    <span class="ml-2 hidden md:block">{{ page.user }}</span>
                </button>
            </div>
        </div>

        <Window class="hidden" @resizeWindow="resizeWindow"></Window>
    </header>
</template>

<script>
import { mapActions, mapState } from "vuex";
import UserOverlayVue from "../Misc/UserOverlay";
import { usePage } from "@inertiajs/vue3";
import { useModal } from "vue-final-modal";

const page = usePage();

export default {
    data() {
        return {
            width: 0,
        };
    },

    computed: {
        page() {
            return page.props;
        },

        small_screen() {
            return this.width <= 640;
        },

        getAvatar() {
            return "https://fastly.picsum.photos/id/223/200/200.jpg?hmac=CNNyWbBcEAJ7TPkTmEEwdGrLFEYkxpTeVwJ7U0LB30Y";
        },

        ...mapState({
            store_settings: (state) => state.store_settings,
            dark_mode: (state) => state.framework.dark_mode,
            expanded_sidebar: (state) => state.framework.expanded_sidebar,
        }),
    },

    methods: {
        resizeWindow(width) {
            this.width = width;
        },

        showUserOverlay() {
            const parent = this;

            this.setPopperOpen(true);

            const { open, close } = useModal({
                component: UserOverlayVue,
                attrs: {
                    onConfirm() {
                        close();
                    },
                    onClosed() {
                        parent.setPopperOpen(false);
                    },
                },
            });

            open();

            this.setPopperOpen(true);
        },

        ...mapActions({
            setPopperOpen: "local_settings/setPopperOpen",
        }),
    },
};
</script>

<style scoped></style>
