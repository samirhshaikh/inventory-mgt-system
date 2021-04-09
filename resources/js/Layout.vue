<template>
    <div
        class="flex min-h screen"
        :class="{
            'text-gray-900': !dark_mode,
            'text-white': dark_mode
        }"
    >
        <header
            class="z-50 flex w-full h-10 bg-product-color text-white fixed items-center"
        >
            <div
                class="flex flex-shrink-0 text-xl font-semibold text-center mt-0 mb-0 border-r border-white h-full content-center"
                :class="{
                    'w-64': expanded_sidebar,
                    'w-12': !expanded_sidebar
                }"
            >
                <div v-if="expanded_sidebar" class="mb-auto mt-auto w-full">Inventory Mgt System</div>
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
                        <span class="ml-2">{{ $page.user }}</span>
                    </button>
                </div>
            </div>
        </header>

        <main class="w-full mt-10">
            <div
                :class="{
                    'ml-64': expanded_sidebar,
                    'ml-12': !expanded_sidebar
                }"
            >
                <section
                    class="overflow-y-auto content"
                    :class="{
                        'bg-gray-800': dark_mode,
                        'bg-white': !dark_mode
                    }"
                >
                    <slot></slot>
                </section>

                <Footer></Footer>
            </div>

            <aside
                class="main_side_bar fixed content mt-10"
                :class="{
                'w-64': expanded_sidebar,
                'w-12': !expanded_sidebar,
                'bg-gray-300': !dark_mode,
                'bg-gray-700': dark_mode
            }">
                <Navigation></Navigation>
            </aside>
        </main>

        <notifications group="messages" position="bottom left" :duration="5000"/>
    </div>
</template>

<script>
import {mapState, mapActions} from 'vuex';
import UserOverlayVue from './Misc/UserOverlay.vue';

export default {
    data() {
        return {
            restoreState: {
                framework: {method: 'framework/setFrameworkFromAppSettings', payload: this.$page.app_settings},
                store_settings: {
                    method: 'store_settings/setStoreSettingsFromAppSettings',
                    payload: this.$page.app_settings
                },
                datatable: {method: 'datatable/setDatatableFromAppSetting', payload: this.$page.app_settings}
            }
        };
    },

    created() {
        console.log(this.$page.app_settings);
        //app_settings in not initialized in the session. So nothing to restore.
        if (this.$page.app_settings === null || Object.keys(this.$page.app_settings).length === 0) {
            return;
        }

        _.forEach(this.restoreState, (data, key) => {
            // Check whether the app_settings (data.payload) contains the state we want to restore
            if (Object.keys(data.payload).indexOf(key) >= 0) {
                this.$store.commit(data.method, data.payload[key]);
            }
        });
    },

    methods: {
        showUserOverlay() {
            this.setPopperOpen(true);

            this.$modal.show(UserOverlayVue, {}, {
                width: '80%',
                height: '80%'
            });
        },

        ...mapActions({
            setPopperOpen: 'local_settings/setPopperOpen'
        })
    },

    computed: {
        getAvatar() {
            return "https://i.picsum.photos/id/402/200/200.jpg?hmac=9PZqzeq_aHvVAxvDPNfP6GuD58m4rilq-TUrG4e7V80";
        },

        ...mapState({
            dark_mode: state => state.framework.dark_mode,
            expanded_sidebar: state => state.framework.expanded_sidebar
        })
    }
}
</script>
