<template>
    <VueFinalModal
        class="flex justify-center items-center"
        :content-class="[
            'part_details_modal relative p-4 rounded-lg',
            {
                'bg-gray-700': dark_mode,
                'bg-white': !dark_mode,
            },
        ]"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
    >
        <div
            class="p-0 text-sm h-full"
            :class="{
                'text-gray-900': !dark_mode,
                'text-white': dark_mode,
            }"
        >
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
                    Part Details
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
                        icon_class=""
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

            <form
                class="w-full pl-2"
                autocomplete="off"
                v-if="!loading"
                @submit.prevent
            >
                <div class="flex flex-wrap items-start">
                    <div class="w-full form_field_container">
                        <label
                            class="form_field_label"
                            :class="{
                                'text-gray-700': !dark_mode,
                                'text-white': dark_mode,
                            }"
                        >
                            Supplier
                        </label>
                        <div class="block flex flex-row">
                            <SupplierPicker
                                source="parts_suppliers"
                                :selected_value="supplier_id"
                                :required_field="
                                    supplier_id == '' || supplier_id == null
                                "
                                :enable_add="true"
                                @on-option-selected="onSupplierSelected"
                                @on-data-load-complete="suppliersLoaded"
                                ref="supplier_picker"
                            />
                        </div>
                    </div>

                    <div class="w-full form_field_container">
                        <label
                            class="form_field_label"
                            :class="{
                                'text-gray-700': !dark_mode,
                                'text-white': dark_mode,
                            }"
                        >
                            Part
                        </label>
                        <div
                            class="flex flex-row items-center"
                            v-if="!loading_parts"
                        >
                            <v-select
                                :value="part_id"
                                label="Name"
                                v-model="part_id"
                                :reduce="(part) => part.id"
                                :options="parts"
                                class="w-80 generic_vs_select"
                                :class="{
                                    required_field:
                                        part_id == '' || part_id == null,
                                }"
                            ></v-select>

                            <Button
                                @click.native="addPart"
                                icon="plus"
                                split="border-white"
                                class="ml-1 bg-green-600 text-white"
                            >
                                New
                            </Button>
                        </div>
                    </div>

                    <div class="w-full form_field_container items-center">
                        <label
                            class="form_field_label"
                            :class="{
                                'text-gray-700': !dark_mode,
                                'text-white': dark_mode,
                            }"
                            >cost
                        </label>
                        <span
                            :class="{
                                'text-gray-700': !dark_mode,
                                'text-white': dark_mode,
                            }"
                            class="mr-1"
                            >£</span
                        ><input
                            class="w-32 generic_input"
                            type="number"
                            v-model="cost"
                            autocomplete="off"
                            ref="amount"
                        />
                    </div>
                </div>
            </form>

            <Loading v-else />
        </div>
    </VueFinalModal>
</template>

<style>
.part_details_modal {
    width: 650px;
    height: auto;
}
</style>

<script>
import { VueFinalModal } from "vue-final-modal";
import { notifications } from "../../../Helpers/notifications";
import { ref } from "vue";
import { mapState } from "vuex";
import { list_controller } from "../../../Helpers/list_controller";

export default {
    mixins: [list_controller, notifications],

    components: {
        VueFinalModal,
    },

    props: {
        data: {
            type: Object,
            default: () => ({
                part_id: "",
                supplier_id: "",
                cost: "",
            }),
        },
        onSave: {
            type: Function,
        },
    },

    setup() {
        const part_id = ref("");
        const supplier_id = ref("");
        const cost = ref("");

        return { part_id, supplier_id, cost };
    },

    mounted() {
        this.part_id = this.data.part_id;
        this.supplier_id = this.data.supplier_id;
        this.cost = this.data.cost;
    },

    data() {
        return {
            supplier_loaded: false,
            loading: false,
        };
    },

    computed: {
        valid_data() {
            if (
                this.part_id === null ||
                this.part_id === "" ||
                this.supplier_id === null ||
                this.supplier_id === ""
            ) {
                return false;
            }

            return true;
        },

        ...mapState({
            dark_mode: (state) => state.framework.dark_mode,
            refresh_parts: (state) => state.framework.refresh_parts,
        }),
    },

    methods: {
        save() {
            const handler = this.onSave;
            if (typeof handler === "function") {
                handler({
                    part_id: this.part_id,
                    supplier_id: this.supplier_id,
                    cost: this.cost,
                });
            }

            this.$emit("confirm");
        },

        onSupplierSelected(value) {
            this.supplier_id = value;
        },

        suppliersLoaded() {
            if (!this.edit_id && !this.supplier_loaded) {
                this.$nextTick(() => {
                    // this.$refs.customers_picker.$refs.customer_id.$el
                    //     .querySelector("input")
                    //     .focus();
                    this.supplier_loaded = true;
                });
            }
        },
    },

    watch: {
        refresh_parts: function () {
            this.load_parts();
        },
    },
};
</script>
