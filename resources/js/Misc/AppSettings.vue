<template>
    <div
        class="flex h-full border border-product-color"
        :class="{
            'bg-gray-700 text-white': dark_mode
        }"
    >
        <div class="flex-grow flex flex-col justify-between">
            <div class="p-4 overflow-y-auto text-sm flex-grow">
                <div
                    class="flex border-b border-product-color-lighter mb-4 pb-1"
                    :class="{
                        'border-product-color-lighter': dark_mode,
                        'border-product-color': !dark_mode
                    }"
                >
                    <h1
                        class="text-xl pt-2 ml-1 w-full"
                        :class="{
                            'text-product-color-lighter': dark_mode,
                            'text-product-color': !dark_mode
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
                                for="business_name"
                            >
                                Business Name
                            </label>
                            <input
                                class="block w-full generic_input"
                                id="business_name"
                                type="text"
                                v-model.trim="business_name"
                            />
                            <p
                                class="text-red-500 text-xs italic mb-2"
                                :class="{
                                    hidden:
                                        business_name != '' &&
                                        business_name != null
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
                                for="stock_types"
                            >
                                Stock Types
                            </label>
                            <textarea
                                class="block w-full generic_input"
                                id="stock_types"
                                v-model.trim="stock_types"
                            ></textarea>
                            <p
                                class="text-red-500 text-xs italic mb-2"
                                :class="{
                                    hidden:
                                        stock_types != '' &&
                                        stock_types != null
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
                                    hidden:
                                        networks != '' &&
                                        networks != null
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
                                        phone_sizes != null
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
                                        stock_statuses != null
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
                                        payment_types != null
                                }"
                            >
                                Required
                            </p>
                        </div>
                    </div>
                    <!-- <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label
                                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                for="grid-first-name"
                            >
                                First Name
                            </label>
                            <input
                                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                                id="grid-first-name"
                                type="text"
                                placeholder="Jane"
                            />
                            <p class="text-red-500 text-xs italic">
                                Please fill out this field.
                            </p>
                        </div>
                        <div class="w-full md:w-1/2 px-3">
                            <label
                                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                for="grid-last-name"
                            >
                                Last Name
                            </label>
                            <input
                                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                id="grid-last-name"
                                type="text"
                                placeholder="Doe"
                            />
                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full px-3">
                            <label
                                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                for="grid-password"
                            >
                                Password
                            </label>
                            <input
                                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                id="grid-password"
                                type="password"
                                placeholder="******************"
                            />
                            <p class="text-gray-600 text-xs italic">
                                Make it as long and as crazy as you'd like
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 mb-2">
                        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                            <label
                                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                for="grid-city"
                            >
                                City
                            </label>
                            <input
                                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                id="grid-city"
                                type="text"
                                placeholder="Albuquerque"
                            />
                        </div>
                        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                            <label
                                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                for="grid-state"
                            >
                                State
                            </label>
                            <div class="relative">
                                <select
                                    class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    id="grid-state"
                                >
                                    <option>New Mexico</option>
                                    <option>Missouri</option>
                                    <option>Texas</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
                                >
                                    <svg
                                        class="fill-current h-4 w-4"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                            <label
                                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                for="grid-zip"
                            >
                                Zip
                            </label>
                            <input
                                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                id="grid-zip"
                                type="text"
                                placeholder="90210"
                            />
                        </div>
                    </div> -->
                </form>
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
            business_name: "",
            stock_types: "",
            networks: "",
            phone_sizes: "",
            stock_statuses: "",
            payment_types: "",
        }
    },

    computed: {
        ...mapState({
            store_settings: state => state.store_settings,
            dark_mode: state => state.framework.dark_mode,
            expanded_sidebar: state => state.framework.expanded_sidebar
        })
    },

    mounted() {
        this.business_name = this.store_settings.name;
        this.stock_types = this.store_settings.stock_types;
        this.networks = this.store_settings.networks;
        this.phone_sizes = this.store_settings.phone_sizes;
        this.stock_statuses = this.store_settings.stock_statuses;
        this.payment_types = this.store_settings.payment_types;
    },

    methods: {
        save() {
            let settings = {
                name: this.business_name,
                stock_types: this.stock_types,
                networks: this.networks,
                phone_sizes: this.phone_sizes,
                stock_statuses: this.stock_statuses,
                payment_types: this.payment_types
            };
            // console.log(settings);

            this.setStoreSettings(settings);

            this.$modal.hide(this.$parent.name)
        },

        ...mapActions({
            setStoreSettings: "store_settings/setStoreSettings"
        })
    }
};
</script>
