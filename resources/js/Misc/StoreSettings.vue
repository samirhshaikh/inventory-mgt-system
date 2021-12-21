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
                        Store Settings
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
                            class="ml-1"
                            :class="{
                                'bg-green-600': valid_data,
                                'bg-gray-600 text-gray-500 cursor-not-allowed': !valid_data
                            }"
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
                                ref="business_name"
                            />

                            <p
                                class="form_field_message"
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
                                for="address"
                            >
                                Address
                            </label>
                            <textarea
                                class="block w-full generic_input"
                                id="address"
                                v-model.trim="store_address"
                            ></textarea>

                            <p
                                class="form_field_message"
                                :class="{
                                    hidden:
                                        store_address != '' &&
                                        store_address != null
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
                                for="phone"
                            >
                                Phone
                            </label>
                            <input
                                class="block w-full generic_input"
                                id="phone"
                                type="text"
                                v-model.trim="phone"
                            />
                        </div>
                    </div>

                    <div class="flex flex-wrap -mx-3 mb-5">
                        <div class="w-full px-3">
                            <label
                                class="block uppercase tracking-wide text-gray-700 text-sm font-semibold mb-2"
                                for="phone"
                            >
                                Email
                            </label>
                            <input
                                class="block w-full generic_input"
                                id="email"
                                type="text"
                                v-model.trim="email"
                            />

                            <p
                                class="form_field_message"
                                :class="{
                                    hidden:
                                        valid_email
                                }"
                            >
                                Valid email required
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
import helper_functions from "../Helpers/helper_functions";

export default {
    data() {
        return {
            business_name: "",
            store_address: "",
            phone: "",
            email: "",
        }
    },

    computed: {
        valid_data() {
            if (
                this.business_name == "" ||
                this.store_address == null ||
                !this.valid_email
            ) {
                return false;
            }

            return true;
        },

        valid_email() {
            return this.email === '' || helper_functions.validEmail(this.email)
        },

        ...mapState({
            store_settings: state => state.store_settings,
            dark_mode: state => state.framework.dark_mode,
            expanded_sidebar: state => state.framework.expanded_sidebar
        })
    },

    mounted() {
        this.business_name = this.store_settings.name;
        this.store_address = this.store_settings.address;
        this.phone = this.store_settings.phone;
        this.email = this.store_settings.email;

        this.$nextTick(() => {
            this.$refs.business_name.focus();
        });
    },

    methods: {
        save() {
            if (!this.valid_data) {
                return false;
            }

            let settings = {
                name: this.business_name,
                address: this.store_address,
                phone: this.phone,
                email: this.email
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
