<template>
    <div
        class="flex h-full border border-product-color"
        :class="{
            'bg-gray-700 text-white': dark_mode,
        }"
    >
        <div class="flex-grow flex flex-col justify-between">
            <div class="p-4 overflow-y-auto text-sm flex-grow">
                <div
                    class="flex border-b border-product-color-lighter mb-4 pb-1"
                    :class="{
                        'border-product-color-lighter': dark_mode,
                        'border-product-color': !dark_mode,
                    }"
                >
                    <h1
                        class="text-xl pt-2 ml-1 w-full"
                        :class="{
                            'text-product-color-lighter': dark_mode,
                            'text-product-color': !dark_mode,
                        }"
                    >
                        Application Settings
                    </h1>
                    <div
                        class="float-right flex justify-end mr-2 text-white w-64"
                    >
                        <Button
                            @click.native="$emit('close')"
                            icon="times"
                            split="border-white"
                            class="bg-red-600"
                        >
                            Close
                        </Button>
                        <Button
                            @click.native="save"
                            icon="check"
                            split="border-white"
                            class="bg-green-600 ml-1"
                        >
                            Save
                        </Button>
                    </div>
                </div>

                <form class="w-full max-w-lg pl-2">
                    <div class="flex flex-wrap -mx-3 mb-5">
                        <div class="w-full px-3">
                            <label
                                class="block uppercase tracking-wide text-gray-700 text-sm font-semibold mb-2"
                                for="stock_types"
                            >
                                Stock Types
                            </label>
                            <textarea
                                class="block w-full generic_input"
                                id="stock_types"
                                v-model.trim="stock_types"
                                ref="stock_types"
                            ></textarea>
                            <p
                                class="text-red-500 text-xs italic mb-2"
                                :class="{
                                    hidden:
                                        stock_types != '' &&
                                        stock_types != null,
                                }"
                            >
                                Required
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap -mx-3 mb-5">
                        <div class="w-full px-3">
                            <label
                                class="block uppercase tracking-wide text-gray-700 text-sm font-semibold mb-2"
                                for="networks"
                            >
                                Networks
                            </label>
                            <textarea
                                class="block w-full generic_input"
                                id="networks"
                                v-model.trim="networks"
                            ></textarea>
                            <p
                                class="text-red-500 text-xs italic mb-2"
                                :class="{
                                    hidden: networks != '' && networks != null,
                                }"
                            >
                                Required
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap -mx-3 mb-5">
                        <div class="w-full px-3">
                            <label
                                class="block uppercase tracking-wide text-gray-700 text-sm font-semibold mb-2"
                                for="phone_sizes"
                            >
                                Phone Sizes
                            </label>
                            <textarea
                                class="block w-full generic_input"
                                id="phone_sizes"
                                v-model.trim="phone_sizes"
                            ></textarea>
                            <p
                                class="text-red-500 text-xs italic mb-2"
                                :class="{
                                    hidden:
                                        phone_sizes != '' &&
                                        phone_sizes != null,
                                }"
                            >
                                Required
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap -mx-3 mb-5">
                        <div class="w-full px-3">
                            <label
                                class="block uppercase tracking-wide text-gray-700 text-sm font-semibold mb-2"
                                for="stock_statuses"
                            >
                                Stock Status
                            </label>
                            <textarea
                                class="block w-full generic_input"
                                id="stock_statuses"
                                v-model.trim="stock_statuses"
                            ></textarea>
                            <p
                                class="text-red-500 text-xs italic mb-2"
                                :class="{
                                    hidden:
                                        stock_statuses != '' &&
                                        stock_statuses != null,
                                }"
                            >
                                Required
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap -mx-3 mb-5">
                        <div class="w-full px-3">
                            <label
                                class="block uppercase tracking-wide text-gray-700 text-sm font-semibold mb-2"
                                for="payment_types"
                            >
                                Payment Types
                            </label>
                            <textarea
                                class="block w-full generic_input"
                                id="payment_types"
                                v-model.trim="payment_types"
                            ></textarea>
                            <p
                                class="text-red-500 text-xs italic mb-2"
                                :class="{
                                    hidden:
                                        payment_types != '' &&
                                        payment_types != null,
                                }"
                            >
                                Required
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { mapState, mapActions } from "vuex";

export default {
    data() {
        return {
            stock_types: "",
            networks: "",
            phone_sizes: "",
            stock_statuses: "",
            payment_types: "",
        };
    },

    computed: {
        ...mapState({
            app_settings: (state) => state.app_settings,
            dark_mode: (state) => state.framework.dark_mode,
            expanded_sidebar: (state) => state.framework.expanded_sidebar,
        }),
    },

    mounted() {
        this.stock_types = this.app_settings.stock_types;
        this.networks = this.app_settings.networks;
        this.phone_sizes = this.app_settings.phone_sizes;
        this.stock_statuses = this.app_settings.stock_statuses;
        this.payment_types = this.app_settings.payment_types;

        this.$nextTick(() => {
            this.$refs.stock_types.focus();
        });
    },

    methods: {
        save() {
            let settings = {
                stock_types: this.stock_types,
                networks: this.networks,
                phone_sizes: this.phone_sizes,
                stock_statuses: this.stock_statuses,
                payment_types: this.payment_types,
            };
            // console.log(settings);

            this.setAppSettings(settings);

            this.$modal.hide(this.$parent.name);
        },

        ...mapActions({
            setAppSettings: "app_settings/setAppSettings",
        }),
    },
};
</script>
