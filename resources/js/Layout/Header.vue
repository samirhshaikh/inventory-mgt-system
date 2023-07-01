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
                class="mb-auto mt-auto w-full"
            >
                Inventory Mgt System
            </div>
            <div v-else class="mb-auto mt-auto w-full">IMS</div>
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
                    <span class="ml-2 hidden md:block">{{ $page.user }}</span>
                </button>
            </div>
        </div>

        <Window class="hidden" @resizeWindow="resizeWindow"></Window>
    </header>
</template>

<script>
import { mapActions, mapState } from "vuex";
import UserOverlayVue from "../Misc/UserOverlay";

export default {
    data() {
        return {
            width: 0,
        };
    },

    computed: {
        small_screen() {
            return this.width <= 640;
        },

        getAvatar() {
            return "https://i.picsum.photos/id/402/200/200.jpg?hmac=9PZqzeq_aHvVAxvDPNfP6GuD58m4rilq-TUrG4e7V80";
        },

        ...mapState({
            dark_mode: (state) => state.framework.dark_mode,
            expanded_sidebar: (state) => state.framework.expanded_sidebar,
        }),
    },

    methods: {
        resizeWindow(width) {
            this.width = width;
        },

        showUserOverlay() {
            this.setPopperOpen(true);

            this.$modal.show(
                UserOverlayVue,
                {},
                {
                    width: "80%",
                    height: "80%",
                }
            );
        },

        ...mapActions({
            setPopperOpen: "local_settings/setPopperOpen",
        }),
    },
};
</script>

<style scoped></style>
