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
                        class="text-base md:text-xl pt-2 ml-1 w-full"
                        :class="{
                            'text-product-color-lighter': dark_mode,
                            'text-product-color': !dark_mode
                        }"
                    >
                        {{ options.record_name }} Details
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
                            :icon="saving_data ? 'sync-alt' : 'check'"
                            :icon_class="saving_data ? 'fa-spin' : ''"
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

                <form class="w-full pl-2" autocomplete="off" v-if="!loading" @submit.prevent>
                    <div class="flex flex-wrap -mx-3 form_field_container">
                        <div class="w-full px-3">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
                                }"
                            >
                                Name
                            </label>
                            <input
                                class="w-72 generic_input"
                                type="text"
                                v-model.trim="row['CustomerName']"
                                maxlength="255"
                                :class="{
                                    required_field: row['CustomerName'] == '' || row['CustomerName'] == null
                                }"
                                autocomplete="off"
                            />
                        </div>
                    </div>

                    <div class="flex flex-wrap -mx-3 form_field_container">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
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
                        <div class="w-full md:w-1/2 px-3">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
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
                    </div>

                    <div class="flex flex-wrap -mx-3 form_field_container">
                        <div class="w-full md:w-1/2 px-3">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
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

                        <div class="w-full md:w-1/2 px-3">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
                                }"
                            >
                                Balance
                            </label>
                            £ <input
                            class="w-32 generic_input"
                            type="number"
                            v-model.number="row['Balance']"
                            autocomplete="off"
                        />
                        </div>
                    </div>

                    <div class="flex flex-wrap -mx-3 form_field_container">
                        <div class="w-full md:w-1/2 px-3">
                            <label
                                class="block form_field_label"
                                :class="{
                                        'text-gray-700': !dark_mode,
                                        'text-white': dark_mode
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

                        <div class="w-full md:w-1/2 px-3">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
                                }"
                            >
                                Is Active
                            </label>
                            <toggle-button
                                :value="is_active"
                                :sync="true"
                                :labels="{ checked: 'Yes', unchecked: 'No' }"
                                @change="toggleIsActive()"
                            />
                        </div>
                    </div>

                    <div class="flex flex-wrap -mx-3 form_field_container" v-if="edit_id != ''">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
                                }"
                            >
                                Created By
                            </label>
                            <label
                                class="block form_value_label"
                                :class="{
                                    'text-gray-600': !dark_mode,
                                    'text-product-color-lighter': dark_mode
                                }"
                            >
                                {{ getColumnValue("CreatedBy") }}
                            </label>
                        </div>
                        <div class="w-full md:w-1/2 px-3">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
                                }"
                            >
                                Creation Date
                            </label>
                            <label
                                class="block form_value_label"
                                :class="{
                                    'text-gray-600': !dark_mode,
                                    'text-product-color-lighter': dark_mode
                                }"
                            >
                                {{ getColumnValue("CreatedDate") }}
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-wrap -mx-3 form_field_container" v-if="edit_id != ''">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
                                }"
                            >
                                Updated By
                            </label>
                            <label
                                class="block form_value_label"
                                :class="{
                                    'text-gray-600': !dark_mode,
                                    'text-product-color-lighter': dark_mode
                                }"
                            >
                                {{ getColumnValue("UpdatedBy") }}
                            </label>
                        </div>
                        <div class="w-full md:w-1/2 px-3">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
                                }"
                            >
                                Updated Date
                            </label>
                            <label
                                class="block form_value_label"
                                :class="{
                                    'text-gray-600': !dark_mode,
                                    'text-product-color-lighter': dark_mode
                                }"
                            >
                                {{ getColumnValue("UpdatedDate") }}
                            </label>
                        </div>
                    </div>
                </form>

                <Loading v-else />
            </div>
        </div>
    </div>
</template>

<script>
import { mapState, mapActions } from "vuex";
import moment from "moment";
import Button from "../components/Button";
import {notifications} from "../Helpers/notifications";

export default {
    mixins: [notifications],

    props: {
        options: {
            type: Object,
            default: () => ({})
        },
        edit_id: {
            type: String,
            default: ""
        }
    },

    data() {
        return {
            row: {},

            is_active: true,

            saving_data: false,
            loading: false
        };
    },

    computed: {
        valid_data() {
            if (
                this.row_keys.indexOf("CustomerName") < 0 ||
                this.row["CustomerName"] == ""
            ) {
                return false;
            }

            return true;
        },

        row_keys() {
            return Object.keys(this.row);
        },

        ...mapState({
            dark_mode: state => state.framework.dark_mode,
            expanded_sidebar: state => state.framework.expanded_sidebar,
            local_settings: state => state.local_settings
        })
    },

    mounted() {
        //Get the data from server
        if (this.edit_id != "") {
            this.loading = true;

            axios
                .get(route("customer_sales.get-single"), {
                    params: {
                        Id: this.edit_id
                    }
                })
                .then(
                    response => {
                        let record = response.data.response.record;
                        this.is_active = record.IsActive;

                        this.row = _.cloneDeep(record);

                        this.loading = false;
                    },
                    error => {
                        this.loading = false;
                    }
                );
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
                .post(route("customer_sales.save"), this.row)
                .then(response => {
                    if (response.data.message == "record_saved") {
                        this.$notify({
                            group: "messages",
                            title: "Success",
                            text:
                                this.options.record_name +
                                " " +
                                (this.row["operation"] == "add"
                                    ? "added"
                                    : "edited") +
                                " successfully."
                        });

                        //Reset the cache
                        if (
                            this.options.hasOwnProperty("cache_data") &&
                            this.options.cache_data
                        ) {
                            this.resetCachedData(this.options.id);
                        }

                        this.refreshData(this.options.id);

                        this.refreshCustomerSales();
                    }

                    this.saving_data = false;

                    this.$modal.hide(this.$parent.name);
                })
                .catch(error => {
                    this.saving_data = false;

                    if (error.response.data.message == "record_not_found") {
                        this.$notify({
                            group: "messages",
                            title: "Error",
                            type: "error",
                            text: this.formatMessage(error.response.data, this.options.record_name)
                        });
                    } else if (error.response.data.message == "duplicate_name") {
                        this.duplicate_name = true;
                    }
                });
        },

        isDuplicateName() {
            if (this.row_keys.indexOf("Name") < 0 || this.row["Name"] == "") {
                return false;
            }

            this.checking_duplicate_name = true;
            this.duplicate_name = false;

            axios
                .post(route("handsets.check-duplicate-name"), {
                    Id: this.row["Id"],
                    Name: this.row["Name"]
                })
                .then(response => {
                    this.checking_duplicate_name = false;

                    this.duplicate_name = false;

                    this.checking_duplicate_name = false;
                })
                .catch(error => {
                    this.checking_duplicate_name = false;

                    if (error.response.data.message == "duplicate_name") {
                        this.duplicate_name = true;
                    }
                });
        },

        ...mapActions({
            refreshData: "framework/refreshData",
            refreshCustomerSales: "framework/refreshCustomerSales",
            setCachedData: "local_settings/setCachedData",
            resetCachedData: "local_settings/resetCachedData",
            addError: "errors/addError"
        })
    }
};
</script>
