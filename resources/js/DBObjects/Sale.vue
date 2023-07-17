<template>
    <VueFinalModal
        class="flex justify-center items-center"
        :content-class="[
            'sale_modal relative p-4 rounded-lg dark:bg-gray-900',
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
                        @click.native="saveAll"
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

            <div class="flex flex-row w-full" v-if="!loading">
                <div class="w-1/2 border-r border-product-color-lighter pr-4">
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
                                    Customer {{ row["CustomerId"] }}
                                </label>
                                <CustomerSalesPicker
                                    :selected_value="row['CustomerId']"
                                    :required_field="
                                        row['CustomerId'] == '' ||
                                        row['CustomerId'] == null
                                    "
                                    :enable_add="true"
                                    :enable_edit="true"
                                    @on-option-selected="onCustomerSelected"
                                    @on-data-load-complete="customerSalesLoaded"
                                    ref="customer_sales_picker"
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
                                    Invoice Date
                                </label>

                                <CustomDatePicker
                                    :start_date_value="row['InvoiceDate']"
                                    v-bind:required_field="true"
                                    @date-selected="dateSelected"
                                    @clearDate="clearDate"
                                ></CustomDatePicker>
                            </div>

                            <div class="w-full md:w-1/2 form_field_container">
                                <label
                                    class="form_field_label"
                                    :class="{
                                        'text-gray-700': !dark_mode,
                                        'text-white': dark_mode,
                                    }"
                                >
                                    Invoice No
                                </label>
                                <div class="flex flex-row items-center">
                                    <span
                                        v-if="
                                            row_keys.indexOf('Id') < 0 ||
                                            row['Id'] == 0 ||
                                            row['Id'] == ''
                                        "
                                        >Auto Generated</span
                                    >
                                    <span v-else>{{ row["InvoiceNo"] }}</span>
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
                                    Payment Type
                                </label>

                                <v-select
                                    :value="row['PaymentMethod']"
                                    label="Size"
                                    v-model="row['PaymentMethod']"
                                    :options="payment_types"
                                    class="w-40 generic_vs_select"
                                    :class="{
                                        required_field:
                                            row['PaymentMethod'] == '' ||
                                            row['PaymentMethod'] == null,
                                    }"
                                ></v-select>
                            </div>

                            <div class="w-full md:w-1/2 form_field_container">
                                <label
                                    class="form_field_label"
                                    :class="{
                                        'text-gray-700': !dark_mode,
                                        'text-white': dark_mode,
                                    }"
                                >
                                    VAT
                                </label>
                                <div class="flex flex-row items-center">
                                    <input
                                        class="w-20 generic_input mr-1"
                                        type="number"
                                        v-model.number="row['VAT']"
                                        autocomplete="off"
                                        ref="cost"
                                    />%
                                </div>
                            </div>
                        </div>

                        <div
                            class="datatable_header"
                            :class="{
                                'border-product-color-lighter': dark_mode,
                                'border-product-color': !dark_mode,
                            }"
                        >
                            <h2
                                :class="{
                                    'text-product-color-lighter': dark_mode,
                                    'text-product-color': !dark_mode,
                                }"
                            >
                                Phone Details
                            </h2>
                            <div class="search_bar_container">
                                <Button
                                    @click.native="updateRecord"
                                    icon="plus"
                                    split="border-white"
                                    class="ml-1"
                                    :class="{
                                        'bg-green-600': valid_phone_data,
                                        'bg-gray-600 text-gray-500 cursor-not-allowed':
                                            !valid_phone_data,
                                    }"
                                    v-if="current_row_id != ''"
                                >
                                    Update
                                </Button>
                                <Button
                                    @click.native="selectPhoneStock"
                                    icon="search"
                                    split="border-white"
                                    class="ml-1 bg-green-600 text-white"
                                    v-if="current_row_id == ''"
                                >
                                    Add Phone
                                </Button>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-start">
                            <div
                                class="w-full md:w-1/2 form_field_container"
                                v-if="current_row_id != ''"
                            >
                                <label
                                    class="form_field_label"
                                    :class="{
                                        'text-gray-700': !dark_mode,
                                        'text-white': dark_mode,
                                    }"
                                >
                                    IMEI
                                </label>
                                <div class="block flex flex-row">
                                    <label
                                        class="form_value_label"
                                        :class="{
                                            'text-gray-600': !dark_mode,
                                            'text-product-color-lighter':
                                                dark_mode,
                                        }"
                                    >
                                        {{ child_row["IMEI"] }}
                                    </label>
                                </div>
                            </div>

                            <div
                                class="w-full md:w-1/2 form_field_container"
                                v-if="current_row_id != ''"
                            >
                                <label
                                    class="form_field_label"
                                    :class="{
                                        'text-gray-700': !dark_mode,
                                        'text-white': dark_mode,
                                    }"
                                >
                                    Cost
                                </label>
                                £<input
                                    class="w-32 generic_input ml-1"
                                    type="number"
                                    v-model.number="child_row['Cost']"
                                    autocomplete="off"
                                    :class="{
                                        required_field:
                                            child_row['Cost'] == '' ||
                                            child_row['Cost'] == null,
                                    }"
                                    ref="cost"
                                />
                            </div>

                            <div class="w-full form_field_container">
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

                            <RecordStamp :row="row" v-if="edit_id != ''" />
                        </div>

                        <div
                            class="datatable_header"
                            :class="{
                                'border-product-color-lighter': dark_mode,
                                'border-product-color': !dark_mode,
                            }"
                        >
                            <h2
                                :class="{
                                    'text-product-color-lighter': dark_mode,
                                    'text-product-color': !dark_mode,
                                }"
                            >
                                Trade In Details
                            </h2>
                            <div class="search_bar_container">
                                <Button
                                    @click.native="tradeInPhone"
                                    :icon="tradeInAvailable ? 'pen' : 'plus'"
                                    split="border-white"
                                    class="ml-1 bg-green-600"
                                >
                                    Trade In Phone
                                </Button>
                                <Button
                                    @click.native="removeTradeInPhone"
                                    icon="trash"
                                    split="border-white"
                                    class="ml-1 bg-red-600"
                                    v-if="tradeInAvailable"
                                >
                                    {{
                                        deleting_tradein_record
                                            ? "Deleting"
                                            : "Delete"
                                    }}
                                </Button>
                            </div>
                        </div>

                        <div v-if="tradeInAvailable">
                            <div
                                v-for="(item, key) in row.tradein.purchase
                                    .children"
                                :class="{
                                    'border-t border-gray-300 pt-5': key > 0,
                                }"
                            >
                                <div class="flex flex-wrap items-start">
                                    <div
                                        class="w-full md:w-1/2 form_field_container"
                                    >
                                        <label
                                            class="form_field_label"
                                            :class="{
                                                'text-gray-700': !dark_mode,
                                                'text-white': dark_mode,
                                            }"
                                        >
                                            IMEI
                                        </label>
                                        <label
                                            class="form_value_label"
                                            :class="{
                                                'text-gray-600': !dark_mode,
                                                'text-product-color-lighter':
                                                    dark_mode,
                                            }"
                                        >
                                            {{ item.IMEI }}
                                        </label>
                                    </div>

                                    <div
                                        class="w-full md:w-1/2 form_field_container"
                                    >
                                        <label
                                            class="form_field_label"
                                            :class="{
                                                'text-gray-700': !dark_mode,
                                                'text-white': dark_mode,
                                            }"
                                        >
                                            Phone
                                        </label>
                                        <label
                                            class="form_value_label"
                                            :class="{
                                                'text-gray-600': !dark_mode,
                                                'text-product-color-lighter':
                                                    dark_mode,
                                            }"
                                        >
                                            {{ item.manufacturer.Name }} -
                                            {{ item.model.Name }} -
                                            {{ item.color.Name }}
                                        </label>
                                    </div>

                                    <div
                                        class="w-full md:w-1/2 form_field_container"
                                    >
                                        <label
                                            class="form_field_label"
                                            :class="{
                                                'text-gray-700': !dark_mode,
                                                'text-white': dark_mode,
                                            }"
                                        >
                                            Size
                                        </label>
                                        <label
                                            class="form_value_label"
                                            :class="{
                                                'text-gray-600': !dark_mode,
                                                'text-product-color-lighter':
                                                    dark_mode,
                                            }"
                                        >
                                            {{ item.Size }}
                                        </label>
                                    </div>

                                    <div
                                        class="w-full md:w-1/2 form_field_container"
                                    >
                                        <label
                                            class="form_field_label"
                                            :class="{
                                                'text-gray-700': !dark_mode,
                                                'text-white': dark_mode,
                                            }"
                                        >
                                            Cost
                                        </label>
                                        <label
                                            class="form_value_label"
                                            :class="{
                                                'text-gray-600': !dark_mode,
                                                'text-product-color-lighter':
                                                    dark_mode,
                                            }"
                                        >
                                            £{{ item.Cost }}
                                        </label>
                                    </div>

                                    <div
                                        class="w-full md:w-1/2 form_field_container"
                                    >
                                        <label
                                            class="form_field_label"
                                            :class="{
                                                'text-gray-700': !dark_mode,
                                                'text-white': dark_mode,
                                            }"
                                        >
                                            StockType
                                        </label>
                                        <label
                                            class="form_value_label"
                                            :class="{
                                                'text-gray-600': !dark_mode,
                                                'text-product-color-lighter':
                                                    dark_mode,
                                            }"
                                        >
                                            {{ item.StockType }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex flex-wrap -mx-3 form_field_container"
                            v-else
                        >
                            <div class="w-full md:w-1/2 form_field_container">
                                <label
                                    class="form_value_label"
                                    :class="{
                                        'text-gray-700': !dark_mode,
                                        'text-white': dark_mode,
                                    }"
                                >
                                    No Trade In with this sale.
                                </label>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="w-1/2 ml-2 p-2">
                    <SaleItemsDatatable
                        :columns="selected_products_columns"
                        :options="selected_products_options"
                        :source_data="rows"
                        v-bind:load_data_from_server="false"
                        :current_row_id="current_row_id"
                        :parent_row="row"
                        @edit-record="editRecord"
                        @remove-record="removeRecord"
                        @return-item="returnItem"
                    ></SaleItemsDatatable>
                </div>
            </div>

            <Loading v-else />
        </div>
    </VueFinalModal>
</template>

<style>
.sale_modal {
    width: 90%;
    height: auto;
}
</style>

<script>
import { mapState, mapActions } from "vuex";
import { useModal, VueFinalModal } from "vue-final-modal";
import loading from "@/Misc/Loading.vue";
import helper_functions from "../Helpers/helper_functions";
import { list_controller } from "../Helpers/list_controller";
import RecordPicker from "../components/Datatable/RecordPicker";
import { notifications } from "../Helpers/notifications";
import moment from "moment";
import Purchase from "./Purchase";
import Confirm from "../components/Confirm";
import { defineAsyncComponent } from "vue";

export default {
    props: {
        options: {
            type: Object,
            default: () => ({}),
        },
        edit_id: {
            type: String,
            default: "",
        },
        submitRecordSaved: {
            type: Function,
        },
        phones: {
            type: Array,
            default: () => [],
        },
    },

    mixins: [list_controller, notifications],

    components: {
        VueFinalModal,
        SaleItemsDatatable: defineAsyncComponent({
            loader: () => import("@/Datatable/Datatable"),
            loadingComponent: loading,
        }),
    },

    data() {
        return {
            row: {
                CustomerId: "",
                PaymentMethod: "Cash",
                VAT: 0,
                InvoiceDate: moment().format("D-MMM-YYYY"),
                tradein: {
                    PurchaseInvoiceId: "",
                    purchase: null,
                },
            },
            child_row: {
                IMEI: "",
                Cost: "",
                Discount: "",
            },
            rows: [],
            children_to_delete: [],

            saving_data: false,
            checking_duplicate_imei: false,
            duplicate_imei: false,
            loading: false,
            customer_sales_loaded: false,

            add_record_title: "Add",
            current_row_id: "",

            deleting_tradein_record: false,

            selected_products_options: {
                enable_search: true,
                url: "",
                id: "selected_invoices_table",
                pagination: true,
                primary_key: "Id",
                record_name: "Phone",
                sorting: {
                    enabled: true,
                    default: "IMEI",
                    direction: "asc",
                },
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
                    th: "text-left",
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
                    th: "text-left break-words",
                },
                {
                    enabled: true,
                    key: "phone_details.Size",
                    name: "Size",
                    order: 5,
                    searching: false,
                    sorting: false,
                    td: "text-left",
                    th: "text-left break-words",
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
                    th: "text-left break-words",
                },
                {
                    enabled: true,
                    key: "route",
                    name: "Actions",
                    order: 6,
                    searching: false,
                    sorting: false,
                    th: "",
                    type: "AddSaleActions",
                },
            ],
        };
    },

    computed: {
        valid_data() {
            if (
                this.rows.length == 0 ||
                this.row_keys.indexOf("InvoiceDate") < 0 ||
                this.row["InvoiceDate"] == "" ||
                this.row_keys.indexOf("CustomerId") < 0 ||
                this.row["CustomerId"] == "" ||
                this.row["CustomerId"] == null ||
                this.row_keys.indexOf("PaymentMethod") < 0 ||
                this.row["PaymentMethod"] == "" ||
                this.row["PaymentMethod"] == null ||
                (this.row_keys.indexOf("VAT") >= 0 &&
                    parseFloat(this.row["VAT"]) < 0)
            ) {
                return false;
            }

            return true;
        },

        valid_phone_data() {
            if (
                this.child_row_keys.indexOf("Cost") < 0 ||
                this.child_row["Cost"] == "" ||
                parseFloat(this.child_row["Cost"]) == 0
            ) {
                return false;
            }

            return true;
        },

        row_keys() {
            return Object.keys(this.row);
        },

        child_row_keys() {
            return Object.keys(this.child_row);
        },

        tradeInAvailable() {
            return (this.row?.tradein?.PurchaseInvoiceId ?? "") != "";
        },

        ...mapState({
            dark_mode: (state) => state.framework.dark_mode,
            expanded_sidebar: (state) => state.framework.expanded_sidebar,
            local_settings: (state) => state.local_settings,
        }),
    },

    created() {
        this.setTableMetaData({
            columns: this.selected_products_columns,
            options: this.selected_products_options,
        });

        this.setActiveTab(this.selected_products_options.id);
    },

    mounted() {
        //Get the data from server
        if (this.edit_id != "") {
            this.add_record_title = "Add";

            this.loading = true;

            axios
                .get(route("sale.get-single"), {
                    params: {
                        Id: this.edit_id,
                    },
                })
                .then(
                    (response) => {
                        let record = response.data.response.record;

                        this.row = _.cloneDeep(record);

                        //Assign a random id to the child row.
                        _.forEach(record.children, (child_row, key) => {
                            child_row["row_id"] =
                                helper_functions.getRandomId();
                            this.rows.push(child_row);
                        });

                        this.loading = false;
                    },
                    (error) => {
                        this.loading = false;
                    }
                );
        } else {
            this.add_record_title = "Add";

            this.child_row["row_id"] = helper_functions.getRandomId();

            if (this.phones.length) {
                this.loading = true;
                axios
                    .post(route("phonestock.get-single"), {
                        Id: this.phones.pop(),
                    })
                    .then((response) => {
                        if (response.data.message == "OK") {
                            let child_row = response.data.response.record;
                            child_row["Id"] = "";
                            child_row["phone_details"] = _.clone(child_row);
                            child_row["row_id"] =
                                helper_functions.getRandomId();
                            child_row["Returned"] = false;
                            this.rows.push(child_row);
                        } else {
                            this.$notify({
                                group: "messages",
                                title: "Error",
                                type: "error",
                                text: this.formatMessage(
                                    "unknown_error",
                                    this.options.record_name
                                ),
                            });
                        }

                        this.loading = false;
                    })
                    .catch((error) => {
                        this.$notify({
                            group: "messages",
                            title: "Error",
                            type: "error",
                            text: this.formatMessage(
                                error.response.data.message,
                                this.options.record_name
                            ),
                        });
                        this.loading = false;
                    });
            }
        }
    },

    methods: {
        editRecord(child_row) {
            this.child_row = _.cloneDeep(child_row);

            this.add_record_title = "Update";

            this.current_row_id = _.clone(this.child_row["row_id"]);
        },

        removeRecord(row_id) {
            let rows = [];

            _.forIn(this.rows, (object, key) => {
                if (object["row_id"] != row_id) {
                    rows.push(_.cloneDeep(object));
                } else if (
                    Object.keys(object).indexOf("Id") >= 0 &&
                    object["Id"] != ""
                ) {
                    this.children_to_delete.push(object);
                }
            });

            this.rows = _.cloneDeep(rows);
        },

        returnItem(IMEI) {
            let rows = [];

            _.forIn(this.rows, (object, key) => {
                if (object["IMEI"] == IMEI) {
                    object["Returned"] = 1;
                }

                rows.push(_.cloneDeep(object));
            });

            this.rows = _.cloneDeep(rows);
        },

        dateSelected(date) {
            if (date != "" && date != null) {
                this.row["InvoiceDate"] = date;
            } else {
                this.row["InvoiceDate"] = "";
            }
        },

        clearDate(key) {
            this.row["InvoiceDate"] = "";
        },

        getColumnValue(column) {
            return _.get(this.row, column);
        },

        updateRecord() {
            //Validate
            if (!this.valid_phone_data) {
                return false;
            }

            let rows = [];
            _.forIn(this.rows, (object, key) => {
                if (object["IMEI"] === this.child_row["IMEI"]) {
                    rows.push(_.cloneDeep(this.child_row));
                } else {
                    rows.push(object);
                }
            });
            this.rows = _.cloneDeep(rows);

            this.child_row["IMEI"] = "";
            this.child_row["Cost"] = "";
            this.child_row["Discount"] = "";

            this.current_row_id = "";
        },

        saveAll() {
            if (this.rows.length == 0) {
                return false;
            }

            this.row["operation"] = this.edit_id == "" ? "add" : "edit";
            this.row["children"] = this.rows;
            this.row["children_to_delete"] = this.children_to_delete;

            //save the user
            this.saving_data = true;

            axios.post(route("sale.save"), this.row).then(
                (response) => {
                    if (response.data.message == "record_saved") {
                        this.$notify({
                            group: "messages",
                            title: "Success",
                            text:
                                response.data.response.records_count +
                                " " +
                                this.options.record_name +
                                (response.data.response.records_count > 1
                                    ? "s"
                                    : "") +
                                " saved successfully.",
                        });

                        this.refreshData(this.options.id);

                        const handler = this.submitRecordSaved;
                        if (typeof handler === "function") {
                            handler(response.data.response.id);
                        }
                    }

                    this.saving_data = false;

                    this.$emit("confirm");
                },
                (error) => {
                    this.saving_data = false;

                    if (error.response.data.message == "record_not_found") {
                        this.$notify({
                            group: "messages",
                            title: "Error",
                            type: "error",
                            text: this.formatMessage(
                                error.response.data,
                                this.options.record_name
                            ),
                        });
                    } else {
                        this.$notify({
                            group: "messages",
                            title: "Error",
                            type: "error",
                            text: this.formatMessage(
                                error.response.data.message,
                                this.options.record_name
                            ),
                        });

                        _.forIn(this.children_to_delete, (object, key) => {
                            this.rows.push(_.clone(object));
                        });
                    }
                }
            );
        },

        selectPhoneStock() {
            const parent = this;

            this.setPopperOpen(true);

            const { open, close } = useModal({
                component: RecordPicker,
                attrs: {
                    columns: [
                        {
                            enabled: true,
                            key: "IMEI",
                            name: "IMEI",
                            order: 1,
                            searching: false,
                            sorting: false,
                            td: "text-left break-words",
                            th: "text-left",
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
                            th: "text-left break-words",
                        },
                        {
                            enabled: true,
                            key: "Size",
                            name: "Size",
                            order: 4,
                            searching: false,
                            sorting: false,
                            td: "text-left",
                            th: "text-left break-words",
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
                            th: "text-left break-words",
                        },
                        {
                            enabled: true,
                            key: "Network",
                            name: "Network",
                            order: 6,
                            searching: false,
                            sorting: false,
                            td: "text-left",
                            th: "text-left break-words",
                        },
                        {
                            enabled: true,
                            key: "Status",
                            name: "Status",
                            order: 7,
                            searching: false,
                            sorting: false,
                            td: "text-left",
                            th: "text-left break-words",
                        },
                        {
                            enabled: true,
                            key: "route",
                            name: "Action",
                            order: 8,
                            searching: false,
                            sorting: false,
                            th: "",
                            type: "RecordPickerActions",
                        },
                    ],
                    options: {
                        enable_search: true,
                        url: route("datatable.phonestock.available"),
                        id: "phonestock_record_picker",
                        pagination: true,
                        primary_key: "Id",
                        record_name: "Phone",
                        sorting: {
                            enabled: true,
                            default: "IMEI",
                            direction: "asc",
                        },
                    },
                    submitRecordsSelected: (selected_records) => {
                        _.forEach(selected_records, (data, key) => {
                            this.rows.push({
                                IMEI: data["IMEI"],
                                Cost: data["Cost"],
                                Returned: 0,
                                Discount: "",
                                row_id: helper_functions.getRandomId(),
                                phone_details: data,
                            });
                        });
                    },
                    onConfirm() {
                        close();
                    },
                    onClosed() {
                        parent.setPopperOpen(false);

                        parent.setTableMetaData({
                            columns: parent.selected_products_columns,
                            options: parent.selected_products_options,
                        });

                        parent.setActiveTab(
                            parent.selected_products_options.id
                        );
                        parent.setTabToRefresh(
                            parent.selected_products_options.id
                        );
                    },
                },
            });

            open();
        },

        onCustomerSelected(value) {
            this.row["CustomerId"] = value;
        },

        customerSalesLoaded() {
            if (!this.edit_id && !this.customer_sales_loaded) {
                this.$nextTick(() => {
                    this.$refs.customer_sales_picker.$refs.customer_id.$el
                        .querySelector("input")
                        .focus();
                    this.customer_sales_loaded = true;
                });
            }
        },

        tradeInPhone() {
            const purchase_id = this.row?.tradein?.PurchaseInvoiceId ?? "";

            const parent = this;

            this.setPopperOpen(true);

            const { open, close } = useModal({
                component: Purchase,
                attrs: {
                    edit_id: purchase_id ? String(purchase_id) : "",
                    options: {
                        id: "purchases",
                        child_record_name: "Phone",
                        record_name: "Purchase",
                        enable_search: false,
                        pagination: false,
                        primary_key: "Id",
                        sorting: {
                            default: "UpdatedDate",
                            direction: "desc",
                            enabled: false,
                        },
                    },
                    submitRecordSaved: (purchase_invoice_id) => {
                        axios
                            .get(route("purchase.get-single"), {
                                params: {
                                    Id: purchase_invoice_id,
                                },
                            })
                            .then(
                                (response) => {
                                    let record = response.data.response.record;

                                    parent.$set(this.row, "tradein", {
                                        PurchaseInvoiceId: purchase_invoice_id,
                                        purchase: _.cloneDeep(record),
                                    });
                                },
                                (error) => {}
                            );
                    },
                },
            });

            open();
        },

        removeTradeInPhone() {
            const parent = this;

            const { open, close } = useModal({
                component: Confirm,
                attrs: {
                    title: "Delete Trade In",
                    text: "Are you sure you want to delete this Trade In?",
                    yes_handler: () => {
                        this.deleting_tradein_record = true;

                        axios
                            .post(route("tradein.delete"), {
                                purchase_id: this.row.tradein.PurchaseInvoiceId,
                            })
                            .then((response) => {
                                if (response.data.message == "record_deleted") {
                                    this.$notify({
                                        group: "messages",
                                        title: "Success",
                                        text: this.formatMessage(
                                            response.data.message,
                                            "Trade In"
                                        ),
                                    });

                                    this.row.tradein.PurchaseInvoiceId = "";
                                    this.row.tradein.purchase = null;
                                } else {
                                    this.$notify({
                                        group: "messages",
                                        title: "Error",
                                        type: "error",
                                        text: this.formatMessage(
                                            "unknown_error",
                                            this.options.record_name
                                        ),
                                    });
                                }

                                this.deleting_tradein_record = false;
                            })
                            .catch((error) => {
                                this.deleting_tradein_record = false;

                                this.$notify({
                                    group: "messages",
                                    title: "Error",
                                    type: "error",
                                    text: this.formatMessage(
                                        error.response.data.message,
                                        this.options.record_name
                                    ),
                                });
                            });
                    },
                },
            });

            open();
        },

        ...mapActions({
            setTableMetaData: "datatable/setTableMetaData",
            setActiveTab: "local_settings/setActiveTab",
            setPopperOpen: "local_settings/setPopperOpen",
            refreshData: "framework/refreshData",
            setTabToRefresh: "framework/setTabToRefresh",
            setCachedData: "local_settings/setCachedData",
            addError: "errors/addError",
        }),
    },
};
</script>
