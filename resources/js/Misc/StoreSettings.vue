<template>
    <VueFinalModal
        class="flex justify-center items-center"
        :content-class="[
            'store_settings_modal relative p-4 rounded-lg',
            {
                'bg-gray-700': dark_mode,
                'bg-white': !dark_mode,
            },
        ]"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
    >
        <div class="p-0 overflow-y-auto text-sm">
            <div
                class="datatable_header"
                :class="{
                    'border-product-color-lighter': dark_mode,
                    'border-product-color': !dark_mode,
                }"
            >
                <h1
                    :class="{
                        'text-product-color-lighter': dark_mode,
                        'text-product-color': !dark_mode,
                    }"
                >
                    Store Settings
                </h1>
                <div class="search_bar_container">
                    <Button
                        @click.native="$emit('closed')"
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
                            'bg-gray-600 text-gray-500 cursor-not-allowed':
                                !valid_data,
                        }"
                    >
                        Save
                    </Button>
                </div>
            </div>

            <form class="w-full max-w-lg pl-2">
                <div class="flex flex-wrap items-start">
                    <div class="w-full form_field_container">
                        <label class="form_field_label" for="business_name">
                            Business Name
                        </label>
                        <input
                            class="w-full generic_input"
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
                                    business_name != null,
                            }"
                        >
                            Required
                        </p>
                    </div>

                    <div class="w-full form_field_container">
                        <label class="form_field_label"> Address </label>
                        <textarea
                            class="w-full generic_input"
                            id="address"
                            v-model.trim="store_address"
                        ></textarea>

                        <p
                            class="form_field_message"
                            :class="{
                                hidden:
                                    store_address != '' &&
                                    store_address != null,
                            }"
                        >
                            Required
                        </p>
                    </div>

                    <div class="w-full form_field_container">
                        <label class="form_field_label" for="phone">
                            Phone
                        </label>
                        <input
                            class="w-full generic_input"
                            id="phone"
                            type="text"
                            v-model.trim="phone"
                        />
                    </div>

                    <div class="w-full form_field_container">
                        <label class="form_field_label" for="phone">
                            Email
                        </label>
                        <input
                            class="w-full generic_input"
                            id="email"
                            type="text"
                            v-model.trim="email"
                        />

                        <p
                            class="form_field_message"
                            :class="{
                                hidden: valid_email,
                            }"
                        >
                            Valid email required
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </VueFinalModal>
</template>

<style>
.store_settings_modal {
    width: 450px;
    height: auto;
}
</style>

<script>
import { mapState, mapActions } from "vuex";
import { VueFinalModal } from "vue-final-modal";
import helper_functions from "../Helpers/helper_functions";

export default {
    components: {
        VueFinalModal,
    },

    data() {
        return {
            business_name: "",
            store_address: "",
            phone: "",
            email: "",
        };
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
            return this.email === "" || helper_functions.validEmail(this.email);
        },

        ...mapState({
            store_settings: (state) => state.store_settings,
            dark_mode: (state) => state.framework.dark_mode,
            expanded_sidebar: (state) => state.framework.expanded_sidebar,
        }),
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
                email: this.email,
            };

            this.setStoreSettings(settings);

            this.$emit("confirm");
        },

        ...mapActions({
            setStoreSettings: "store_settings/setStoreSettings",
        }),
    },
};
</script>
