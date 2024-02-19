<template>
    <div class="flex flex-col justify-between h-full">
        <div class="flex flex-col">
            <section v-for="(links, section_title) in navigation" class="mt-5">
                <h3
                    class="px-3 py-2 text-base font-semibold uppercase"
                    :class="{
                        'text-gray-600': !dark_mode,
                        'text-product-color-lighter': dark_mode,
                        hidden: section_title == 'Dashboard',
                    }"
                    v-if="
                        expanded_sidebar &&
                        !small_screen &&
                        atleast_one_link_available(section_title)
                    "
                >
                    {{ section_title }}
                </h3>

                <div>
                    <div v-for="link in links">
                        <Link
                            :href="$route(link.route)"
                            class="px-4 py-2 block text-sm no-underline"
                            :class="linkNavigationClass(link.route)"
                            :key="link.title"
                            v-if="link.link_type == 'route' && link.visible"
                            :title="link.title"
                        >
                            <div class="flex flex-row">
                                <div class="w-5 text-center">
                                    <FA class="fas" :icon="link.icon"></FA>
                                </div>
                                <div
                                    class="ml-1"
                                    v-show="expanded_sidebar && !small_screen"
                                >
                                    {{ link.title }}
                                </div>
                            </div>
                        </Link>

                        <a
                            class="px-4 py-2 block text-sm no-underline cursor-pointer hover:bg-gray-200"
                            @click="
                                handleFunctionCall(link.link_function, $event)
                            "
                            v-if="
                                link.link_type == 'javascript_link' &&
                                link.visible
                            "
                        >
                            <div class="flex flex-row">
                                <div class="w-5 text-center">
                                    <FA class="fas" :icon="link.icon"></FA>
                                </div>
                                <div
                                    class="ml-1"
                                    v-show="expanded_sidebar && !small_screen"
                                >
                                    {{ link.title }}
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </section>
        </div>

        <div class="flex flex-col py-2">
            <button
                icon="sign-out-alt"
                class="py-2 pl-3 block text-sm"
                :class="linkNavigationClass('doLogout')"
                @click.native="doLogout"
                style="display: block"
            >
                <div class="flex flex-row">
                    <div class="w-5 text-center">
                        <FA :icon="['fas', 'sign-out-alt']"></FA>
                    </div>
                    <div
                        class="ml-1"
                        v-show="expanded_sidebar && !small_screen"
                    >
                        Logout
                    </div>
                </div>
            </button>

            <button
                @click="toggleSidebar"
                class="bg-product-color text-white p-2 rounded mt-1 ml-2 mr-2"
                :class="{
                    'hover:bg-product-color-lighter': dark_mode,
                    'hover: text-gray-900': dark_mode,
                    'hover: bg-gray-900': !dark_mode,
                }"
            >
                <FA
                    :icon="['fas', 'angle-double-left']"
                    v-if="expanded_sidebar && !small_screen"
                ></FA>
                <FA :icon="['fas', 'angle-double-right']" v-else></FA>
            </button>
        </div>

        <Window class="hidden" @resizeWindow="resizeWindow"></Window>
    </div>
</template>

<script>
import { mapActions, mapState } from "vuex";
import AppSettings from "../Misc/AppSettings.vue";
import StoreSettings from "../Misc/StoreSettings.vue";
import { useModal } from "vue-final-modal";
import { usePage, Link } from "@inertiajs/vue3";
import Confirm from "../components/Confirm";

const page = usePage();

export default {
    components: {
        Link,
    },

    data() {
        return {
            navigation: {
                Dashboard: [
                    {
                        title: "Dashboard",
                        link_type: "route",
                        route: "dashboard",
                        icon: ["fas", "home"],
                        visible: true,
                    },
                ],
                Main: [
                    {
                        title: "Sales",
                        link_type: "route",
                        route: "sales",
                        icon: ["fas", "arrow-up"],
                        visible: true,
                    },
                    {
                        title: "Purchases",
                        link_type: "route",
                        route: "purchases",
                        icon: ["fas", "arrow-down"],
                        visible: true,
                    },
                ],

                Masters: [
                    {
                        title: "Customers",
                        link_type: "route",
                        route: "customer_sales",
                        icon: ["fas", "users"],
                        visible: true,
                    },
                    {
                        title: "Suppliers",
                        link_type: "route",
                        route: "suppliers",
                        icon: ["fas", "building"],
                        visible: true,
                    },
                    {
                        title: "Manufacturers",
                        link_type: "route",
                        route: "handset-manufacturers",
                        icon: ["fas", "industry"],
                        visible: true,
                    },
                    {
                        title: "Colors",
                        link_type: "route",
                        route: "handset-colors",
                        icon: ["fas", "palette"],
                        visible: true,
                    },
                    {
                        title: "Models",
                        link_type: "route",
                        route: "handset-models",
                        icon: ["fas", "mobile-alt"],
                        visible: true,
                    },
                ],

                Admin: [
                    {
                        title: "Users",
                        link_type: "route",
                        route: "users",
                        icon: ["fas", "user"],
                        visible: page.props.user_details.IsAdmin,
                    },
                    {
                        title: "Store",
                        link_type: "javascript_link",
                        link_function: "storeSettings",
                        icon: ["fas", "store"],
                        visible: page.props.user_details.IsAdmin,
                    },
                    {
                        title: "Settings",
                        link_type: "javascript_link",
                        link_function: "appSettings",
                        icon: ["fas", "cog"],
                        visible: page.props.user_details.IsAdmin,
                    },
                ],
            },
            currentRoute: window.location.pathname,
            width: 0,
        };
    },

    computed: {
        small_screen() {
            return this.width <= 640;
        },

        ...mapState({
            dark_mode: (state) => state.framework.dark_mode,
            expanded_sidebar: (state) => state.framework.expanded_sidebar,
        }),
    },

    methods: {
        doLogout() {
            const { open, close } = useModal({
                component: Confirm,
                attrs: {
                    title: "Logout",
                    text: "Are you sure you want to logout",
                    yes_handler: () => {
                        location.replace(route("doLogout"));
                    },
                    onConfirm() {
                        close();
                    },
                    onClosed() {},
                },
            });

            open();
        },

        resizeWindow(width) {
            this.width = width;
        },

        atleast_one_link_available(key) {
            return (
                this.navigation[key].filter((link) => link.visible).length > 0
            );
        },

        linkNavigationClass(link_route) {
            let cssClasses = [];

            if (
                (this.currentRoute == "/" && link_route == "dashboard") ||
                route().current(link_route)
            ) {
                if (this.dark_mode) {
                    cssClasses.push("text-product-color-lighter");
                    cssClasses.push("bg-gray-800");
                } else {
                    cssClasses.push("text-product-color");
                    cssClasses.push("bg-white");
                }
            } else {
                if (this.dark_mode) {
                    cssClasses.push("hover:bg-gray-600");
                } else {
                    cssClasses.push("hover:bg-gray-200");
                }
            }

            return cssClasses;
        },

        handleFunctionCall(functionName, event) {
            this[functionName](event);
        },

        appSettings() {
            const parent = this;

            this.setPopperOpen(true);

            const { open, close } = useModal({
                component: AppSettings,
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
        },

        storeSettings() {
            const parent = this;

            this.setPopperOpen(true);

            const { open, close } = useModal({
                component: StoreSettings,
                attrs: {
                    heading: "test data",
                    onConfirm() {
                        close();
                    },
                    onClosed() {
                        parent.setPopperOpen(false);
                    },
                },
            });

            open();
        },

        ...mapActions({
            toggleSidebar: "framework/toggleSidebar",
            setPopperOpen: "local_settings/setPopperOpen",
        }),
    },
};
</script>
