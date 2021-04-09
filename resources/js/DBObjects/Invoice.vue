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
                            @click.native="saveAll"
                            :icon="saving_data ? 'sync-alt' : 'check'"
                            :icon_class="saving_data ? 'fa-spin' : ''"
                            split="border-white"
                            class="ml-1"
                            :class="{
                                'bg-green-600': rows.length,
                                'bg-gray-600 text-gray-500 cursor-not-allowed': !rows.length
                            }"
                        >
                            Save All
                        </Button>
                    </div>
                </div>

                <div class="flex flex-row" v-if="!loading">
                    <div class="w-1/2 border-r border-product-color-lighter pr-4">
                        <form class="pl-2" autocomplete="off" @submit.prevent>
                            <div class="flex -mx-3 justify-between items-start form_field_container">
                                <div class="px-3">
                                    <label
                                        class="block form_field_label"
                                        :class="{
                                            'text-gray-700': !dark_mode,
                                            'text-white': dark_mode
                                        }"
                                    >
                                        Invoice No:
                                    </label>
                                    <div class="flex flex-row items-center">
                                        <span v-if="row_keys.indexOf('Id') < 0 || row['Id'] == 0 || row['Id'] == ''">Auto Generated</span>
                                        <span v-else>{{ row["InvoiceNo"] }}</span>
                                    </div>
                                </div>

                                <div class="text-white px-3 mt-5">
                                    <Button
                                        @click.native="addRecord"
                                        icon="plus"
                                        split="border-white"
                                        class="ml-1"
                                        :class="{
                                            'bg-green-600': valid_data,
                                            'bg-gray-600 text-gray-500 cursor-not-allowed': !valid_data
                                        }"
                                    >
                                        {{ add_record_title }}
                                    </Button>
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
                                        Invoice Date
                                    </label>

                                    <CustomDatePicker
                                        :start_date_value="row['InvoiceDate']"
                                        v-bind:required_field="true"
                                        @dateSelected="dateSelected"
                                        @clearDate="clearDate"
                                    ></CustomDatePicker>
                                </div>

                                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">

                                </div>
                            </div>

                            <div class="flex flex-wrap -mx-3 form_field_container">
                                <div class="w-full px-3 mb-6 md:mb-0">
                                    <label
                                        class="block form_field_label"
                                        :class="{
                                        'text-gray-700': !dark_mode,
                                        'text-white': dark_mode
                                    }"
                                    >
                                        IMEI
                                    </label>
                                    <div class="block flex flex-row">
                                        <label
                                            class="block form_value_label"
                                            :class="{
                                            'text-gray-600': !dark_mode,
                                            'text-product-color-lighter': dark_mode
                                        }"
                                        >
                                            {{ getColumnValue("IMEI") }}
                                        </label>
                                        <Button
                                            @click.native="selectPhoneStock"
                                            icon="search"
                                            split="border-white"
                                            class="ml-1 bg-green-600 text-white"
                                        >
                                            Add Phone
                                        </Button>
                                    </div>
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
                    </div>

                    <div class="w-1/2 ml-2 p-2">
                        <SelectedInvoiceDatatable
                            :columns="selected_products_columns"
                            :options="selected_products_options"
                            :source_data="rows"
                            v-bind:load_data_from_server="false"
                            :current_row_id="current_row_id"
                            @editRecord="editRecord"
                            @removeRecord="removeRecord"
                        ></SelectedInvoiceDatatable>
                    </div>
                </div>

                <Loading v-else/>
            </div>
        </div>
    </div>
</template>

<script>
import {mapState, mapActions} from "vuex";
import moment from "moment";
import lazyLoadComponent from "@/Helpers/lazyLoadComponent.js";
import loading from "@/Misc/Loading.vue";
import helper_functions from "../Store/modules/helper_functions";
import {list_controller} from "./list_controller";
import RecordPicker from "../components/Datatable/RecordPicker";
import {notifications} from "../Helpers/notifications";

export default {
    props: {
        options: {
            type: Object,
            default: () => ({})
        },
        edit_id: {
            type: String,
            default: ""
        },
    },

    mixins: [list_controller, notifications],

    created() {
        this.setTableMetaData({
            columns: this.selected_products_columns,
            options: this.selected_products_options
        });

        this.setActiveTab(this.selected_products_options.id);
    },

    components: {
        SelectedInvoiceDatatable: lazyLoadComponent({
            componentFactory: () => import("@/Datatable/Datatable"),
            loading: loading
        })
    },

    data() {
        return {
            row: {},
            rows: [],

            saving_data: false,
            checking_duplicate_imei: false,
            duplicate_imei: false,
            loading: false,

            add_record_title: "Add",
            current_row_id: "",

            selected_products_options: {
                enable_search: true,
                url: "",
                Id: "selected_invoices_table",
                pagination: true,
                primary_key: "Id",
                record_name: "Phone",
                sorting: {
                    enabled: true,
                    default: 'IMEI',
                    direction: 'asc'
                }
            },
            selected_products_columns: [
                {
                    enabled: true,
                    key: "IMEI",
                    name: "IMEI",
                    order: 1,
                    searching: false,
                    sorting: false,
                    td: "text-left break-words",
                    th: "text-left"
                },
                {
                    enabled: true,
                    key: "phone_details",
                    name: "Phone",
                    order: 3,
                    searching: false,
                    sorting: false,
                    type: "Phone",
                    td: "text-left",
                    th: "text-left break-words"
                },
                {
                    enabled: true,
                    key: "Size",
                    name: "Size",
                    order: 5,
                    searching: false,
                    sorting: false,
                    td: "text-left",
                    th: "text-left break-words"
                },
                {
                    enabled: true,
                    key: "Cost",
                    name: "Cost",
                    order: 5,
                    searching: false,
                    sorting: false,
                    type: "Float",
                    td: "text-left",
                    th: "text-left break-words"
                },
                {
                    enabled: true,
                    key: "route",
                    name: "Actions",
                    order: 6,
                    searching: false,
                    sorting: false,
                    th: "",
                    type: "AddPhoneActions"
                }
            ]
        };
    },

    computed: {
        valid_data() {
            if (
                this.rows.length == 0 ||
                this.row_keys.indexOf("InvoiceDate") < 0 || this.row["InvoiceDate"] == ""
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
            local_settings: state => state.local_settings,
            store_settings: state => state.store_settings,
            refresh_suppliers: state => state.framework.refresh_suppliers,
            refresh_handset_models: state => state.framework.refresh_handset_models,
            refresh_handset_manufacturers: state => state.framework.refresh_handset_manufacturers,
            refresh_handset_colors: state => state.framework.refresh_handset_colors
        })
    },

    mounted() {
        //Get the data from server
        if (this.edit_id != "") {
            this.add_record_title = "Add";

            this.loading = true;

            axios
                .post(route("sales.get-single"), {
                    Id: this.edit_id
                })
                .then(
                    response => {
                        let record = response.data.response.record;

                        this.row = _.cloneDeep(record);

                        _.forEach(record.childs, (child_row, key) => {
                            child_row["row_id"] = helper_functions.getRandomId();
                            this.rows.push(child_row);
                        });

                        this.loading = false;
                    },
                    error => {
                        this.loading = false;
                    }
                );
        } else {
            this.add_record_title = "Add";

            this.child_row["row_id"] = helper_functions.getRandomId();
            this.current_row_id = _.clone(this.child_row["row_id"]);
        }
    },

    methods: {
        isDuplicateIMEI() {
            if (this.row_keys.indexOf("IMEI") < 0 || this.row["IMEI"] == "") {
                return false;
            }

            //Check in existing rows
            this.duplicate_imei = false;
            _.forIn(this.rows, (object, key) => {
                if (object["Id"] == 0) {
                    if (object["IMEI"] == this.row["IMEI"]) {
                        this.duplicate_imei = true;
                    }
                } else if (object["Id"] != this.row["Id"] && object["IMEI"] == this.row["IMEI"]) {
                    this.duplicate_imei = true;
                }
            });
            if (this.duplicate_imei) {
                return false;
            }

            this.checking_duplicate_imei = true;
            this.duplicate_imei = false;

            axios
                .post(route("phonestock.check-duplicate-imei"), {
                    Id: this.row["Id"],
                    IMEI: this.row["IMEI"]
                })
                .then(response => {
                    this.checking_duplicate_imei = false;

                    this.duplicate_imei = false;

                    this.checking_duplicate_imei = false;
                })
                .catch(error => {
                    this.checking_duplicate_imei = false;

                    if (error.response.data == "duplicate_imei") {
                        this.duplicate_imei = true;
                    }
                });
        },

        editRecord(row) {
            this.row = _.cloneDeep(row);

            this.add_record_title = "Update";

            this.current_row_id = _.clone(this.row["row_id"]);

            this.$refs.imei.focus();
        },

        removeRecord(row_id) {
            let rows = [];

            _.forIn(this.rows, (object, key) => {
                if (object["row_id"] != row_id) {
                    rows.push(_.cloneDeep(object));
                }
            });

            this.rows = _.cloneDeep(rows);
        },

        dateSelected(date) {
            if (date != '' && date != null) {
                this.row['InvoiceDate'] = date;
                // this.row['InvoiceDateTimeStamp'] = moment(date).format("X");
            } else {
                this.row['InvoiceDate'] = "";
                // this.row['InvoiceDateTimeStamp'] = "";
            }
        },

        clearDate(key) {
            this.row['InvoiceDate'] = "";
            // this.row['InvoiceDateTimeStamp'] = "";
        },

        setModel(value) {
            let object = helper_functions.searchJsonObjects(this.handset_models, "Id", value);
            if (Object.keys(object).length) {
                this.row['model'] = object['Name'];
            }
        },

        setManufacturer(value) {
            let object = helper_functions.searchJsonObjects(this.handset_manufacturers, "Id", value);
            if (Object.keys(object).length) {
                this.row['manufacturer'] = object['Name'];
            }
        },

        setColor(value) {
            let object = helper_functions.searchJsonObjects(this.handset_colors, "Id", value);
            if (Object.keys(object).length) {
                this.row['color'] = object['Name'];
            }
        },

        getColumnValue(column) {
            return _.get(this.row, column);
        },

        addRecord() {
            //Validate
            if (!this.valid_data) {
                return false;
            }

            if (this.edit_id == "") {
                this.row["Id"] = 0;
            }

            let rows = [];
            let existing_row = false;
            _.forIn(this.rows, (object, key) => {
                if (object["row_id"] == this.row["row_id"]) {
                    rows.push(_.cloneDeep(this.row));
                    existing_row = true;
                } else {
                    rows.push(object);
                }
            });
            if (!existing_row) {
                rows.push(_.cloneDeep(this.row));
            }
            this.rows = _.cloneDeep(rows);

            this.row["Id"] = 0;
            this.row["IMEI"] = "";
            this.row["ModelNo"] = "";
            this.row["Network"] = "";
            this.row["Comments"] = "";
            this.row["IsActive"] = 0;

            this.add_record_title = "Add";
            this.row["row_id"] = helper_functions.getRandomId();
            this.current_row_id = "";

            this.$refs.imei.focus();
        },

        saveAll() {
            if (this.rows.length == 0) {
                return false;
            }

            this.row["operation"] = this.edit_id == "" ? "add" : "edit";

            //save the user
            this.saving_data = true;

            axios
                .post(route("phonestock.save"), {
                    rows: this.rows
                })
                .then(response => {
                    if (response.data.message == "record_saved") {
                        this.$notify({
                            group: "messages",
                            title: "Success",
                            text: response.data.response.records_count + " " + this.options.record_name + (response.data.response.records_count > 1 ? "s" : "") + " saved successfully."
                        });

                        this.refreshData(this.options.id);
                    }

                    this.saving_data = false;

                    this.$modal.hide(this.$parent.name);
                })
                .catch(error => {
                    this.saving_data = false;

                    if (error.response.data == "record_not_found") {
                        this.$notify({
                            group: "messages",
                            title: "Error",
                            type: "error",
                            text: this.formatMessage(error.response.data, this.options.record_name)
                        });
                    } else if (error.response.data == "duplicate_imei") {
                        this.duplicate_imei = true;
                    }
                });
        },

        selectPhoneStock() {
            this.setPopperOpen(true);

            this.$modal.show(
                RecordPicker,
                {
                    columns: [
                        {
                            enabled: true,
                            key: "IMEI",
                            name: "IMEI",
                            order: 1,
                            searching: false,
                            sorting: false,
                            td: "text-left break-words",
                            th: "text-left"
                        },
                        {
                            enabled: true,
                            key: "phone_details",
                            name: "Phone",
                            order: 3,
                            searching: false,
                            sorting: false,
                            type: "Phone",
                            td: "text-left",
                            th: "text-left break-words"
                        },
                        {
                            enabled: true,
                            key: "Size",
                            name: "Size",
                            order: 4,
                            searching: false,
                            sorting: false,
                            td: "text-left",
                            th: "text-left break-words"
                        },
                        {
                            enabled: true,
                            key: "Cost",
                            name: "Cost",
                            order: 5,
                            searching: false,
                            sorting: false,
                            type: "Float",
                            td: "text-left",
                            th: "text-left break-words"
                        },
                        {
                            enabled: true,
                            key: "Network",
                            name: "Network",
                            order: 6,
                            searching: false,
                            sorting: false,
                            td: "text-left",
                            th: "text-left break-words"
                        },
                        {
                            enabled: true,
                            key: "Status",
                            name: "Status",
                            order: 7,
                            searching: false,
                            sorting: false,
                            td: "text-left",
                            th: "text-left break-words"
                        },
                        {
                            enabled: true,
                            key: "route",
                            name: "Action",
                            order: 8,
                            searching: false,
                            sorting: false,
                            th: "",
                            type: "RecordPickerActions"
                        }
                    ],
                    options: {
                        enable_search: true,
                        url: route('datatable.phonestock.available'),
                        Id: "phonestock_record_picker",
                        pagination: true,
                        primary_key: "Id",
                        record_name: "Phone",
                        sorting: {
                            enabled: true,
                            default: 'IMEI',
                            direction: 'asc'
                        }
                    },
                    submitRecordsSelected: (selected_records) => {
                        this.rows = [];
                        _.forEach(selected_records, (data, key) => {
                            this.rows.push(data);
                        });
                    }
                },
                {
                    width: "85%",
                    height: "600px"
                },
                {
                    "closed": event => {
                        this.setTableMetaData({
                            columns: this.selected_products_columns,
                            options: this.selected_products_options
                        });

                        this.setActiveTab(this.selected_products_options.id);
                    }
                }
            );
        },

        ...mapActions({
            setTableMetaData: 'datatable/setTableMetaData',
            setActiveTab: 'local_settings/setActiveTab',
            setPopperOpen: 'local_settings/setPopperOpen',
            refreshData: "framework/refreshData",
            setCachedData: "local_settings/setCachedData",
            addError: "errors/addError"
        })
    },

    watch: {
        refresh_suppliers: function () {
            this.load_suppliers();
        },

        refresh_handset_models: function () {
            this.load_handset_models();
        },

        refresh_handset_manufacturers: function () {
            this.load_handset_manufacturers();
        },

        refresh_handset_colors: function () {
            this.load_handset_colors();
        }
    }
};
</script>
