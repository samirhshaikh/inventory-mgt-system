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
                        Purchase Details
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
                                'bg-green-600': valid_data,
                                'bg-gray-600 text-gray-500 cursor-not-allowed': !valid_data
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
                                        Supplier
                                    </label>
                                    <div class="flex flex-row items-center" v-if="!loading_suppliers">
                                        <v-select
                                            :value="row['SupplierId']"
                                            label="SupplierName"
                                            v-model="row['SupplierId']"
                                            :reduce="supplier => supplier.Id"
                                            :options="suppliers"
                                            class="w-48 generic_vs_select"
                                            v-if="!loading_suppliers"
                                            :class="{
                                                required_field: row['SupplierId'] == '' || row['SupplierId'] == null
                                            }"
                                        ></v-select>
                                        <button class="p-1" @click="addSupplier">
                                            <FA :icon="['fas', 'plus']" class="ml-1"></FA>
                                        </button>
                                    </div>
                                    <Loading v-else/>
                                </div>

                                <div class="text-white px-3 mt-5">

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
                                    <label
                                        class="block form_field_label"
                                        :class="{
                                            'text-gray-700': !dark_mode,
                                            'text-white': dark_mode
                                        }"
                                    >
                                        Invoice No
                                    </label>
                                    <input
                                        class="w-52 generic_input"
                                        type="text"
                                        v-model="row['InvoiceNo']"
                                        autocomplete="off"
                                    />
                                </div>
                            </div>

                            <div
                                class="flex justify-between border-b border-product-color-lighter mb-4 pb-1"
                                :class="{
                                    'border-product-color-lighter': dark_mode,
                                    'border-product-color': !dark_mode
                                }"
                            >
                                <h1
                                    class="text-base pt-2 ml-1"
                                    :class="{
                                        'text-product-color-lighter': dark_mode,
                                        'text-product-color': !dark_mode
                                    }"
                                >
                                    Phone Details
                                </h1>
                                <div
                                    class="mr-2 text-white"
                                >
                                    <Button
                                        @click.native="addRecord"
                                        icon="plus"
                                        split="border-white"
                                        class="ml-1"
                                        :class="{
                                            'bg-green-600': valid_phone_data,
                                            'bg-gray-600 text-gray-500 cursor-not-allowed': !valid_phone_data
                                        }"
                                    >
                                        {{ add_record_title }}
                                    </Button>
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
                                        <input
                                            class="w-52 generic_input"
                                            type="text"
                                            v-model.trim="child_row['IMEI']"
                                            v-on:blur="isDuplicateIMEI"
                                            :class="{
                                                required_field: imei_validation_message != ''
                                            }"
                                            autocomplete="off"
                                            ref="imei"
                                        />

                                        <Loading
                                            class="ml-2 mt-3 text-sm"
                                            v-if="checking_duplicate_imei"
                                            loading_message="Checking IMEI..."
                                        />
                                    </div>

                                    <p
                                        class="form_field_message"
                                        :class="{
                                            hidden: imei_validation_message == '' || imei_validation_message == 'Required'
                                        }"
                                    >
                                        {{ imei_validation_message }}
                                    </p>
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
                                        Stock Type
                                    </label>
                                    <v-select
                                        :value="child_row['StockType']"
                                        label="Size"
                                        v-model="child_row['StockType']"
                                        :options="stock_types"
                                        class="w-32 generic_vs_select"
                                        :class="{
                                            required_field: child_row['StockType'] == '' || child_row['StockType'] == null
                                        }"
                                    ></v-select>
                                </div>

                                <div class="w-full md:w-1/2 px-3">
                                    <label
                                        class="block form_field_label"
                                        :class="{
                                            'text-gray-700': !dark_mode,
                                            'text-white': dark_mode
                                        }"
                                    >
                                        Stock Status
                                    </label>
                                    <v-select
                                        :value="child_row['Status']"
                                        label="Status"
                                        v-model="child_row['Status']"
                                        :options="stock_statuses"
                                        class="w-48 generic_vs_select"
                                        :class="{
                                            required_field: child_row['Status'] == '' || child_row['Status'] == null
                                        }"
                                    ></v-select>
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
                                        Make
                                    </label>
                                    <div class="flex flex-row items-center" v-if="!loading_handset_manufacturers">
                                        <v-select
                                            :value="child_row['manufacturer']['Id']"
                                            label="Name"
                                            v-model="child_row['manufacturer']['Id']"
                                            :reduce="manufacturer => manufacturer.Id"
                                            :options="handset_manufacturers"
                                            class="w-48 generic_vs_select"
                                            @input="setManufacturer"
                                            :class="{
                                                required_field: child_row['manufacturer']['Id'] == '' || child_row['manufacturer']['Id'] == null
                                            }"
                                        ></v-select>
                                        <button class="p-1" @click="addHandsetManufacturer">
                                            <FA :icon="['fas', 'plus']" class="ml-1"></FA>
                                        </button>
                                    </div>

                                    <Loading v-else />
                                </div>

                                <div class="w-full md:w-1/2 px-3">
                                    <label
                                        class="block form_field_label"
                                        :class="{
                                            'text-gray-700': !dark_mode,
                                            'text-white': dark_mode
                                        }"
                                    >
                                        Model
                                    </label>
                                    <div class="flex flex-row items-center" v-if="!loading_handset_models">
                                        <v-select
                                            :value="child_row['model']['Id']"
                                            label="Name"
                                            v-model="child_row['model']['Id']"
                                            :reduce="model => model.Id"
                                            :options="handset_models"
                                            class="w-60 generic_vs_select"
                                            @input="setModel"
                                            :class="{
                                            required_field: child_row['model']['Id'] == '' || child_row['model']['Id'] == null
                                        }"
                                        ></v-select>
                                        <button class="p-1" @click="addHandsetModel">
                                            <FA :icon="['fas', 'plus']" class="ml-1"></FA>
                                        </button>
                                    </div>
                                    <Loading v-else/>
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
                                        Color
                                    </label>
                                    <div class="flex flex-row items-center" v-if="!loading_handset_colors">
                                        <v-select
                                            :value="child_row['color']['Id']"
                                            v-model="child_row['color']['Id']"
                                            :reduce="color => color.Id"
                                            label="Name"
                                            :options="handset_colors"
                                            class="w-64 generic_vs_select"
                                            @input="setColor"
                                            :class="{
                                                required_field: child_row['color']['Id'] == '' || child_row['color']['Id'] == null
                                            }"
                                        ></v-select>
                                        <button class="p-1" @click="addHandsetColor">
                                            <FA :icon="['fas', 'plus']" class="ml-1"></FA>
                                        </button>
                                    </div>
                                    <Loading v-else/>
                                </div>

                                <div class="w-full md:w-1/2 px-3">
                                    <label
                                        class="block form_field_label"
                                        :class="{
                                            'text-gray-700': !dark_mode,
                                            'text-white': dark_mode
                                        }"
                                    >
                                        Size
                                    </label>
                                    <v-select
                                        :value="child_row['Size']"
                                        label="Size"
                                        v-model="child_row['Size']"
                                        :options="phone_sizes"
                                        class="w-32 generic_vs_select"
                                        :class="{
                                            required_field: child_row['Size'] == '' || child_row['Size'] == null
                                        }"
                                    ></v-select>
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
                                        Network
                                    </label>
                                    <v-select
                                        :value="child_row['Network']"
                                        label="Network"
                                        v-model="child_row['Network']"
                                        :options="networks"
                                        class="w-48 generic_vs_select"
                                        :class="{
                                            required_field: child_row['Network'] == '' || child_row['Network'] == null
                                        }"
                                    ></v-select>
                                </div>

                                <div class="w-full md:w-1/2 px-3">
                                    <label
                                        class="block form_field_label"
                                        :class="{
                                            'text-gray-700': !dark_mode,
                                            'text-white': dark_mode
                                            }"
                                    >
                                        Model No
                                    </label>
                                    <input
                                        class="w-48 generic_input"
                                        type="text"
                                        v-model.trim="child_row['ModelNo']"
                                        autocomplete="off"
                                    />
                                </div>
                            </div>

                            <div class="flex flex-wrap -mx-3 form_field_container border-b border-product-color-lighter pb-5">
                                <div class="w-full md:w-1/2 px-3">
                                    <label
                                        class="block form_field_label"
                                        :class="{
                                            'text-gray-700': !dark_mode,
                                            'text-white': dark_mode
                                        }"
                                    >
                                        Cost
                                    </label>
                                    £ <input
                                    class="w-32 generic_input"
                                    type="number"
                                    v-model.number="child_row['Cost']"
                                    autocomplete="off"
                                    :class="{
                                        required_field: child_row['Cost'] == '' || child_row['Cost'] == null
                                    }"
                                />
                                </div>
                            </div>

                            <div class="flex flex-wrap -mx-3 form_field_container">
                                <div class="w-full px-3">
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
                        <PurchaseItemsDatatable
                            :columns="columns"
                            :options="options"
                            :source_data="rows"
                            v-bind:load_data_from_server="false"
                            :current_row_id="current_row_id"
                            @editRecord="editRecord"
                            @removeRecord="removeRecord"
                        ></PurchaseItemsDatatable>
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
import helper_functions from "../store/modules/helper_functions";
import {list_controller} from "./list_controller";
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

    components: {
        PurchaseItemsDatatable: lazyLoadComponent({
            componentFactory: () => import("@/Datatable/Datatable"),
            loading: loading
        })
    },

    data() {
        return {
            row: {
                SupplierId: "",
                InvoiceDate: moment().format("D-MMM-YYYY"),
                InvoiceNo: "",
                Comments: ""
            },
            child_row: {
                manufacturer: {
                    Id: "",
                    Name: ""
                },
                model: {
                    Id: "",
                    Name: ""
                },
                color: {
                    Id: "",
                    Name: ""
                },
                StockType: "New",
                Status: this.phonestock.STATUS_IN_STOCK,
                Network: "Unlocked"
            },
            rows: [],
            deleted_childs: [],

            saving_data: false,
            checking_duplicate_imei: false,
            duplicate_imei: false,
            loading: false,

            add_record_title: "Add",
            current_row_id: "",

            columns: [
                {
                    enabled: true,
                    key: "IMEI",
                    name: "IMEI",
                    order: 1,
                    searching: false,
                    sorting: false,
                    td: "w-30 text-left break-words",
                    th: "text-left"
                },
                {
                    enabled: true,
                    key: "",
                    name: "Phone",
                    order: 3,
                    searching: false,
                    sorting: false,
                    type: "Phone",
                    td: "text-left",
                    th: "w-32 text-left break-words"
                },
                {
                    enabled: true,
                    key: "Size",
                    name: "Size",
                    order: 5,
                    searching: false,
                    sorting: false,
                    td: "text-left",
                    th: "w-16 text-left break-words"
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
                    th: "w-16 text-left break-words"
                },
                {
                    enabled: true,
                    key: "route",
                    name: "Actions",
                    order: 6,
                    searching: false,
                    sorting: false,
                    th: "w-10",
                    type: "AddPurchaseActions"
                }
            ]
        };
    },

    computed: {
        valid_data() {
            if (
                this.rows.length == 0 ||
                this.row_keys.indexOf("InvoiceDate") < 0 || this.row["InvoiceDate"] == "" ||
                this.row_keys.indexOf("SupplierId") < 0 || this.row["SupplierId"] == "" || this.row["SupplierId"] == null
            ) {
                return false;
            }

            return true;
        },

        valid_phone_data() {
            if (
                this.imei_validation_message != "" ||
                this.child_row_keys.indexOf("StockType") < 0 || this.child_row["StockType"] == "" || this.child_row["StockType"] == null ||
                this.child_row_keys.indexOf("manufacturer") < 0 || this.child_row["manufacturer"]["Id"] == "" || this.child_row["manufacturer"]["Id"] == null ||
                this.child_row_keys.indexOf("model") < 0 || this.child_row["model"]["Id"] == "" || this.child_row["model"]["Id"] == null ||
                this.child_row_keys.indexOf("color") < 0 || this.child_row["color"]["Id"] == "" || this.child_row["color"]["Id"] == null ||
                this.child_row_keys.indexOf("Size") < 0 || this.child_row["Size"] == "" || this.child_row['Size'] == null ||
                this.child_row_keys.indexOf("Cost") < 0 || this.child_row["Cost"] == "" || parseFloat(this.child_row["Cost"]) == 0 ||
                this.child_row_keys.indexOf("Network") < 0 || this.child_row["Network"] == "" || this.child_row["Network"] == null ||
                this.child_row_keys.indexOf("Status") < 0 || this.child_row["Status"] == "" || this.child_row["Status"] == null
            ) {
                return false;
            }

            return true;
        },

        imei_validation_message() {
            if (this.duplicate_imei) {
                return "Duplicate IMEI. Please choose another IMEI.";
            } else if (
                this.child_row_keys.indexOf("IMEI") < 0 ||
                this.child_row["IMEI"] == ""
            ) {
                return "Required";
            }

            return "";
        },

        row_keys() {
            return Object.keys(this.row);
        },

        child_row_keys() {
            return Object.keys(this.child_row);
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
                .get(route("purchase.get-single"), {
                    params: {
                        Id: this.edit_id
                    }
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
        editRecord(child_row) {
            this.child_row = _.cloneDeep(child_row);

            this.add_record_title = "Update";

            this.current_row_id = _.clone(this.child_row["row_id"]);

            this.$refs.imei.focus();
        },

        removeRecord(row_id) {
            let rows = [];

            _.forIn(this.rows, (object, key) => {
                if (object["row_id"] != row_id) {
                    rows.push(_.cloneDeep(object));
                } else if (Object.keys(object).indexOf("Id") >= 0 && object["Id"] != "") {
                    this.deleted_childs.push(object);
                }
            });

            this.rows = _.cloneDeep(rows);
        },

        dateSelected(date) {
            if (date != '' && date != null) {
                this.row['InvoiceDate'] = date;
            } else {
                this.row['InvoiceDate'] = "";
            }
        },

        clearDate(key) {
            this.row['InvoiceDate'] = "";
        },

        setModel(value) {
            let object = helper_functions.searchJsonObjects(this.handset_models, "Id", value);
            if (Object.keys(object).length) {
                this.child_row['model']['Name'] = object['Name'];
            }
        },

        setManufacturer(value) {
            let object = helper_functions.searchJsonObjects(this.handset_manufacturers, "Id", value);
            if (Object.keys(object).length) {
                this.child_row['manufacturer']['Name'] = object['Name'];
            }
        },

        setColor(value) {
            let object = helper_functions.searchJsonObjects(this.handset_colors, "Id", value);
            if (Object.keys(object).length) {
                this.child_row['color']['Name'] = object['Name'];
            }
        },

        getColumnValue(column) {
            return _.get(this.row, column);
        },

        addRecord() {
            //Validate
            if (!this.valid_phone_data) {
                return false;
            }

            let rows = [];
            let existing_row = false;
            _.forIn(this.rows, (object, key) => {
                if (object["row_id"] === this.child_row["row_id"]) {
                    rows.push(_.cloneDeep(this.child_row));
                    existing_row = true;
                } else {
                    rows.push(object);
                }
            });
            if (!existing_row) {
                rows.push(_.cloneDeep(this.child_row));
            }
            this.rows = _.cloneDeep(rows);

            this.child_row["IMEI"] = "";
            this.child_row["Id"] = "";
            // this.child_row["StockType"] = "New";
            // this.child_row["Status"] = this.phonestock.STATUS_IN_STOCK;
            // this.child_row["manufacturer"]["Id"] = "";
            // this.child_row["manufacturer"]["Name"] = "";
            // this.child_row["model"]["Id"] = "";
            // this.child_row["model"]["Name"] = "";
            // this.child_row["color"]["Id"] = "";
            // this.child_row["color"]["Name"] = "";
            // this.child_row["Size"] = "";
            // this.child_row["Network"] = "Unlocked";
            this.child_row["ModelNo"] = "";
            // this.child_row["Cost"] = "";
            this.child_row["IsActive"] = 1;

            this.add_record_title = "Add";
            this.child_row["row_id"] = helper_functions.getRandomId();
            this.current_row_id = "";

            this.$refs.imei.focus();
        },

        saveAll() {
            if (this.rows.length == 0) {
                return false;
            }

            this.row["operation"] = this.edit_id == "" ? "add" : "edit";
            this.row["childs"] = this.rows;
            this.row["deleted_childs"] = this.deleted_childs;

            //save the user
            this.saving_data = true;

            axios
                .post(route("purchase.save"), this.row)
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

                    if (error.response.data == "duplicate_imei") {
                        this.duplicate_imei = true;
                    } else {
                        this.$notify({
                            group: "messages",
                            title: "Error",
                            type: "error",
                            text: this.formatMessage(error.response.data, this.options.record_name)
                        });

                        _.forIn(this.deleted_childs, (object, key) => {
                            this.rows.push(_.clone(object))
                        });
                    }
                });
        },

        isDuplicateIMEI() {
            if (this.child_row_keys.indexOf("IMEI") < 0 || this.child_row["IMEI"] == "") {
                return false;
            }

            //Check in existing rows
            this.duplicate_imei = false;
            _.forIn(this.rows, (object, key) => {
                if (object["Id"] == 0) {
                    if (object["IMEI"] == this.child_row["IMEI"]) {
                        this.duplicate_imei = true;
                    }
                } else if (object["Id"] != this.child_row["Id"] && object["IMEI"] == this.child_row["IMEI"]) {
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
                    Id: this.child_row["Id"],
                    IMEI: this.child_row["IMEI"]
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

        ...mapActions({
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
