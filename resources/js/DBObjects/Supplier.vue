<template>
    <VueFinalModal
        class="flex justify-center items-center"
        :content-class="[
            'supplier_modal relative p-4 rounded-lg dark:bg-gray-900',
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
                    {{ options.record_name }} Details
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
                        :icon="saving_data ? 'sync-alt' : 'check'"
                        :icon_class="saving_data ? 'fa-spin' : ''"
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
                            Name
                        </label>
                        <input
                            class="w-72 generic_input"
                            type="text"
                            v-model.trim="row['SupplierName']"
                            maxlength="255"
                            autocomplete="off"
                            ref="supplier_name"
                            :class="{
                                required_field:
                                    row['SupplierName'] == '' ||
                                    row['SupplierName'] == null,
                            }"
                        />
                    </div>

                    <div class="w-full md:w-1/2 form_field_container">
                        <label
                            class="form_field_label"
                            :class="{
                                'text-gray-700': !dark_mode,
                                'text-white': dark_mode,
                            }"
                        >
                            Contact No 1
                        </label>
                        <input
                            type="text"
                            v-model="row['ContactNo1']"
                            class="w-48 generic_input"
                            maxlength="12"
                            autocomplete="off"
                        />
                    </div>

                    <div class="w-full md:w-1/2 form_field_container">
                        <label
                            class="form_field_label"
                            :class="{
                                'text-gray-700': !dark_mode,
                                'text-white': dark_mode,
                            }"
                        >
                            Contact No 2
                        </label>
                        <input
                            type="text"
                            v-model="row['ContactNo2']"
                            class="w-48 generic_input"
                            maxlength="12"
                            autocomplete="off"
                        />
                    </div>

                    <div class="w-full md:w-1/2 form_field_container">
                        <label
                            class="form_field_label"
                            :class="{
                                'text-gray-700': !dark_mode,
                                'text-white': dark_mode,
                            }"
                        >
                            Address
                        </label>
                        <vue-google-autocomplete
                            id="address"
                            classname="w-1/2 generic_input"
                            placeholder="Address"
                        >
                        </vue-google-autocomplete>
                    </div>

                    <div class="w-full md:w-1/2 form_field_container">
                        <div class="hidden">
                            <label
                                class="form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode,
                                }"
                            >
                                Cost
                            </label>
                            £
                            <input
                                class="w-32 generic_input"
                                type="number"
                                v-model.number="row['CurrentBalance']"
                                autocomplete="off"
                            />
                        </div>
                    </div>

                    <div class="w-full md:w-1/2 form_field_container">
                        <label
                            class="form_field_label"
                            :class="{
                                'text-gray-700': !dark_mode,
                                'text-white': dark_mode,
                            }"
                        >
                            Comments
                        </label>
                        <textarea
                            class="w-3/4 generic_input"
                            v-model.trim="row['Comments']"
                            rows="3"
                        />
                    </div>

                    <div class="w-full md:w-1/2 form_field_container">
                        <label
                            class="form_field_label"
                            :class="{
                                'text-gray-700': !dark_mode,
                                'text-white': dark_mode,
                            }"
                        >
                            Is Active
                        </label>

                        <div class="mt-1 flex items-center">
                            <input
                                type="checkbox"
                                class="toggle"
                                :checked="is_active"
                                @change="toggleIsActive"
                            /><span class="label-text ml-1">{{
                                is_active ? "Yes" : "No"
                            }}</span>
                        </div>
                    </div>

                    <RecordStamp :row="row" v-if="edit_id != ''" />
                </div>
            </form>

            <Loading v-else />
        </div>
    </VueFinalModal>
</template>

<style>
.supplier_modal {
    width: 750px;
    height: auto;
}
</style>

<script>
import { mapState, mapActions } from "vuex";
import { VueFinalModal } from "vue-final-modal";
import { notifications } from "../Helpers/notifications";
import RecordStamp from "../Datatable/Cells/RecordStamp";

export default {
    mixins: [notifications],

    components: {
        RecordStamp,
        VueFinalModal,
    },

    props: {
        options: {
            type: Object,
            default: () => ({}),
        },
        edit_id: {
            type: String,
            default: "",
        },
    },

    data() {
        return {
            row: {},

            is_active: true,

            saving_data: false,
            loading: false,
        };
    },

    computed: {
        valid_data() {
            if (
                this.row_keys.indexOf("SupplierName") < 0 ||
                this.row["SupplierName"] == ""
            ) {
                return false;
            }

            return true;
        },

        row_keys() {
            return Object.keys(this.row);
        },

        ...mapState({
            dark_mode: (state) => state.framework.dark_mode,
            expanded_sidebar: (state) => state.framework.expanded_sidebar,
            local_settings: (state) => state.local_settings,
        }),
    },

    mounted() {
        //Get the data from server
        if (this.edit_id != "") {
            this.loading = true;

            axios
                .get(route("suppliers.get-single"), {
                    params: {
                        Id: this.edit_id,
                    },
                })
                .then(
                    (response) => {
                        let record = response.data.response.record;
                        this.is_active = record.IsActive;

                        this.row = _.cloneDeep(record);

                        this.loading = false;

                        this.$nextTick(() => {
                            this.$refs.supplier_name.focus();
                        });
                    },
                    (error) => {
                        this.loading = false;
                    }
                );
        } else {
            this.$nextTick(() => {
                this.$refs.supplier_name.focus();
            });
        }
    },

    methods: {
        toggleIsActive() {
            this.is_active = !this.is_active;
        },

        getColumnValue(column) {
            return _.get(this.row, column);
        },

        save() {
            //Validate
            if (!this.valid_data) {
                return false;
            }

            this.row["IsActive"] = this.is_active ? 1 : 0;
            this.row["operation"] = this.edit_id == "" ? "add" : "edit";

            //save the user
            this.saving_data = true;

            axios
                .post(route("suppliers.save"), this.row)
                .then((response) => {
                    if (response.data.message == "record_saved") {
                        // this.$notify({
                        //     group: "messages",
                        //     title: "Success",
                        //     text:
                        //         this.options.record_name +
                        //         " " +
                        //         (this.row["operation"] == "add"
                        //             ? "added"
                        //             : "edited") +
                        //         " successfully.",
                        // });

                        //Reset the cache
                        if (
                            this.options.hasOwnProperty("cache_data") &&
                            this.options.cache_data
                        ) {
                            this.resetCachedData(this.options.id);
                        }

                        this.refreshData(this.options.id);

                        this.refreshSuppliers();
                    }

                    this.saving_data = false;

                    this.$emit("confirm");
                })
                .catch((error) => {
                    this.saving_data = false;

                    if (error.response.data.message == "record_not_found") {
                        // this.$notify({
                        //     group: "messages",
                        //     title: "Error",
                        //     type: "error",
                        //     text: this.formatMessage(
                        //         error.response.data,
                        //         this.options.record_name
                        //     ),
                        // });
                    } else if (
                        error.response.data.message == "duplicate_name"
                    ) {
                        this.duplicate_name = true;
                    }
                });
        },

        ...mapActions({
            refreshData: "framework/refreshData",
            refreshSuppliers: "framework/refreshSuppliers",
            setCachedData: "local_settings/setCachedData",
            resetCachedData: "local_settings/resetCachedData",
            addError: "errors/addError",
        }),
    },
};
</script>
