<template>
    <VueFinalModal
        class="flex justify-center items-center"
        :content-class="[
            'app_settings_modal relative p-4 rounded-lg dark:bg-gray-900',
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
                    Application Settings
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
                        class="bg-green-600 ml-1"
                    >
                        Save
                    </Button>
                </div>
            </div>

            <form class="w-full pl-2">
                <div class="flex flex-wrap items-start">
                    <div class="w-full form_field_container">
                        <label class="form_field_label" for="stock_types">
                            Stock Types
                        </label>
                        <textarea
                            class="w-full generic_input"
                            id="stock_types"
                            v-model.trim="stock_types"
                            ref="stock_types"
                        ></textarea>
                        <p
                            class="text-red-500 text-xs italic mb-2"
                            :class="{
                                hidden:
                                    stock_types != '' && stock_types != null,
                            }"
                        >
                            Required
                        </p>
                    </div>

                    <div class="w-full form_field_container">
                        <label class="form_field_label" for="networks">
                            Networks
                        </label>
                        <textarea
                            class="w-full generic_input"
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

                    <div class="w-full form_field_container">
                        <label class="form_field_label" for="phone_sizes">
                            Phone Sizes
                        </label>
                        <textarea
                            class="w-full generic_input"
                            id="phone_sizes"
                            v-model.trim="phone_sizes"
                        ></textarea>
                        <p
                            class="text-red-500 text-xs italic mb-2"
                            :class="{
                                hidden:
                                    phone_sizes != '' && phone_sizes != null,
                            }"
                        >
                            Required
                        </p>
                    </div>

                    <div class="w-full form_field_container">
                        <label class="form_field_label" for="stock_statuses">
                            Stock Status
                        </label>
                        <textarea
                            class="w-full generic_input"
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

                    <div class="w-full form_field_container">
                        <label class="form_field_label" for="payment_types">
                            Payment Types
                        </label>
                        <textarea
                            class="w-full generic_input"
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
    </VueFinalModal>
</template>

<style>
.app_settings_modal {
    width: 450px;
    height: auto;
}
</style>

<script>
import { mapState, mapActions } from "vuex";
import { VueFinalModal } from "vue-final-modal";

export default {
    components: {
        VueFinalModal,
    },

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

            this.$emit("confirm");
        },

        ...mapActions({
            setAppSettings: "app_settings/setAppSettings",
        }),
    },
};
</script>
