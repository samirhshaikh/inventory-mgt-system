<template>
    <div class="h-full">
        <ul
            class="list-none flex px-4 pt-2"
            :class="{
                'bg-product-color-lighter': !dark_mode,
                'bg-gray-600': dark_mode
            }"
        >
            <li
                class="mr-1"
                v-for="(tab, i) in tabs"
                :key="i"
                v-show="tab.isVisible"
            >
                <a
                    v-html="tab.header"
                    class="inline-block py-2 px-4 text-sm rounded-t cursor-pointer"
                    @click="selectTab(tab.hash, $event)"
                    :href="tab.hash"
                    :class="{
                        '-mb-px text-product-color': tab.isActive,
                        'opacity-50 hover:text-product-color': !tab.isActive,
                        'text-gray-400 cursor-not-allowed': tab.isDisabled,
                        'bg-white': !dark_mode,
                        'bg-gray-800': dark_mode
                    }"
                    data-turbolinks="false"
                ></a>
            </li>
        </ul>

        <div
            class="tabs-component-panels px-4 py-8"
            style="height: calc(100% - 38px);"
        >
            <slot />
        </div>
    </div>
</template>

<script>
import expiringStorage from '../Helpers/expiringStorage';
import { mapState, mapActions } from "vuex";
export default {
    name: "PopperTabCollection",

    props: {
        cacheLifetime: {
            default: 5
        },
        options: {
            type: Object,
            required: false,
            default: () => ({
                userUrlFragment: true,
                defaultTabHash: null
            })
        }
    },

    data() {
        return {
            tabs: [],
            activeTabHash: "",
            activeTabIndex: 0,
            lastActiveTabHash: ""
        };
    },

    computed: {
        storageKey() {
            return `vue-tabs-component.cache.${window.location.host}${window.location.pathname}`;
        },

        ...mapState({
            dark_mode: state => state.framework.dark_mode,
            popper_active_tab: state => state.local_settings.popper_active_tab
        })
    },

    created() {
        this.tabs = this.$children;
    },

    mounted() {
        window.addEventListener("hashchange", () =>
            this.selectTab(window.location.hash)
        );

        if (this.findTab(window.location.hash)) {
            this.selectTab(window.location.hash);
            return;
        }

        const previousSelectedTabHash = expiringStorage.get(this.storageKey);

        if (this.findTab(previousSelectedTabHash)) {
            this.selectTab(previousSelectedTabHash);
            return;
        }

        //default tab
        if (
            this.options.defaultTabHash !== null &&
            this.findTab("#" + this.options.defaultTabHash)
        ) {
            this.selectTab("#" + this.options.defaultTabHash);
            return;
        }

        //Select the first tab
        if (this.tabs.length) {
            this.selectTab(this.tabs[0].hash);
        }
    },

    methods: {
        selectTab(selectedTabHash, event) {
            // see if we should store the hash in the url fragment
            if (
                event &&
                (this.options === undefined || !this.options.useUrlFragment)
            ) {
                event.preventDefault();
            }

            const selectedTab = this.findTab(selectedTabHash);

            if (!selectedTab) {
                return;
            }

            if (event && selectedTab.isDisabled) {
                event.preventDefault();
                return;
            }

            if (this.lastActiveTabHash === selectedTab.hash) {
                this.$root.$emit("tabs-clicked", selectedTab);
                return;
            }

            this.tabs.forEach(tab => {
                tab.isActive = tab.hash === selectedTab.hash;
            });

            this.$root.$emit("tabs-changed", selectedTab);

            if (selectedTab.id) {
                this.setPopperActiveTab(selectedTab.id);
            }

            this.activeTabHash = selectedTab.hash;
            this.activeTabIndex = this.getTabIndex(selectedTabHash);

            this.lastActiveTabHash = this.activeTabHash = selectedTab.hash;

            expiringStorage.set(
                this.storageKey,
                selectedTab.hash,
                this.cacheLifetime
            );
        },

        findTab(hash) {
            return this.tabs.find(tab => tab.hash === hash);
        },

        setTabVisible(hash, visible) {
            const tab = this.findTab(hash);
            if (!tab) {
                return;
            }

            tab.isVisible = visible;
            if (tab.isActive) {
                //If tab is active, set a different one as active
                tab.isActive = visible;

                this.tabs.every((tab, index, array) => {
                    if (tab.isVisible) {
                        tab.isActive = true;
                        return false;
                    }

                    return true;
                });
            }
        },

        getTabIndex(hash) {
            const tab = this.findTab(hash);

            return this.tabs.indexOf(tab);
        },

        getTabHash(index) {
            const tab = this.tabs.find(tab => this.tabs.indexOf(tab) === index);

            if (!tab) {
                return;
            }

            return tab.hash;
        },

        getActiveTab() {
            return this.findTab(this.activeTabHash);
        },

        getActiveTabIndex() {
            return this.getTabIndex(this.activeTabHash);
        },

        ...mapActions({
            setPopperActiveTab: "local_settings/setPopperActiveTab"
        })
    }
};
</script>
