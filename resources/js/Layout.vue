<template>
    <div
        class="flex min-h screen"
        :class="{
            'text-gray-900': !dark_mode,
            'text-white': dark_mode
        }"
    >
        <Header></Header>

        <main class="w-full mt-10">
            <div
                :class="{
                    'ml-64': expanded_sidebar && !small_screen,
                    'ml-12': !expanded_sidebar || small_screen
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
                'w-64': expanded_sidebar && !small_screen,
                'w-12': !expanded_sidebar || small_screen,
                'bg-gray-300': !dark_mode,
                'bg-gray-700': dark_mode
            }">
                <Navigation></Navigation>
            </aside>
        </main>

        <notifications group="messages" position="bottom left" :duration="5000"/>

        <Window
            class="hidden"
            @resizeWindow="resizeWindow"
        ></Window>
    </div>
</template>

<script>
import {mapState} from 'vuex';

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
            },
            width: 0
        };
    },

    created() {
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

    computed: {
        small_screen() {
            return this.width <= 640;
        },

        ...mapState({
            dark_mode: state => state.framework.dark_mode,
            expanded_sidebar: state => state.framework.expanded_sidebar
        })
    },

    methods: {
        resizeWindow(width) {
            this.width = width;
        }
    }
}
</script>
