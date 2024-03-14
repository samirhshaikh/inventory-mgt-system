<template>
    <VueFinalModal
        class="flex justify-center items-center"
        :content-class="[
            'repair_modal relative p-4 rounded-lg',
            {
                'bg-gray-700': dark_mode,
                'bg-white': !dark_mode,
            },
        ]"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
    >
        {{ current_row_id }}
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
                                    Invoice No
                                </label>
                                <div class="flex flex-row items-center">
                                    <span
                                        v-if="
                                            row_keys.indexOf('id') < 0 ||
                                            row['id'] == 0 ||
                                            row['id'] == ''
                                        "
                                        >Auto Generated</span
                                    >
                                    <span
                                        v-else
                                        :class="{
                                            'text-gray-700': !dark_mode,
                                            'text-white': dark_mode,
                                        }"
                                        >{{ row["InvoiceNo"] }}</span
                                    >
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
                                    Customer
                                </label>
                                <CustomerPicker
                                    :selected_value="row['CustomerId']"
                                    :required_field="
                                        row['CustomerId'] == '' ||
                                        row['CustomerId'] == null
                                    "
                                    :enable_add="true"
                                    :enable_edit="true"
                                    @on-option-selected="onCustomerSelected"
                                    @on-data-load-complete="customersLoaded"
                                    ref="customers_picker"
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
                                    @date-selected="setInvoiceDate"
                                    @clearDate="clearDate('InvoiceDate')"
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
                                    Received Date
                                </label>

                                <CustomDatePicker
                                    :start_date_value="row['ReceivedDate']"
                                    v-bind:required_field="true"
                                    @date-selected="setReceivedDate"
                                    @clearDate="clearDate('ReceivedDate')"
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

                            <div
                                class="w-full md:w-1/2 form_field_container items-center"
                            >
                                <label
                                    class="form_field_label"
                                    :class="{
                                        'text-gray-700': !dark_mode,
                                        'text-white': dark_mode,
                                    }"
                                    >Service Cost
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
                                    v-model.number="row['Amount']"
                                    autocomplete="off"
                                    ref="amount"
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
                                    VAT
                                </label>
                                <div class="flex flex-row items-center">
                                    <input
                                        class="w-20 generic_input mr-1"
                                        type="number"
                                        v-model.number="row['VAT']"
                                        autocomplete="off"
                                        ref="cost"
                                    />
                                    <span
                                        :class="{
                                            'text-gray-700': !dark_mode,
                                            'text-white': dark_mode,
                                        }"
                                        >%</span
                                    >
                                </div>
                            </div>

                            <div
                                class="w-full datatable_header"
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
                                    Device Details
                                </h2>
                            </div>

                            <div class="w-full form_field_container">
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
                                    <input
                                        class="w-52 generic_input"
                                        type="text"
                                        v-model.trim="row['IMEI']"
                                        v-on:blur="validateIMEI"
                                        :class="{
                                            required_field:
                                                imei_validation_message != '',
                                        }"
                                        autocomplete="off"
                                        ref="imei"
                                    />

                                    <Loading
                                        class="ml-2 mt-3 text-sm"
                                        v-if="validating_imei"
                                        loading_message="Checking IMEI..."
                                    />
                                </div>

                                <p
                                    class="form_field_message"
                                    :class="{
                                        hidden:
                                            imei_validation_message == '' ||
                                            imei_validation_message ==
                                                'Required',
                                    }"
                                >
                                    {{ imei_validation_message }}
                                </p>
                            </div>

                            <div class="w-full md:w-1/2 form_field_container">
                                <label
                                    class="form_field_label"
                                    :class="{
                                        'text-gray-700': !dark_mode,
                                        'text-white': dark_mode,
                                    }"
                                >
                                    Make
                                </label>
                                <div
                                    class="flex flex-row items-center w-4/5"
                                    v-if="!loading_handset_manufacturers"
                                >
                                    <v-select
                                        :value="row['MakeId']"
                                        label="Name"
                                        v-model="row['MakeId']"
                                        :reduce="
                                            (manufacturer) => manufacturer.id
                                        "
                                        :options="handset_manufacturers"
                                        class="w-full generic_vs_select"
                                        :class="{
                                            required_field:
                                                row['MakeId'] == '' ||
                                                row['MakeId'] == null,
                                        }"
                                    ></v-select>

                                    <Button
                                        @click.native="addHandsetManufacturer"
                                        icon="plus"
                                        split="border-white"
                                        class="ml-1 bg-green-600 text-white"
                                    >
                                        New
                                    </Button>
                                </div>

                                <Loading v-else />
                            </div>

                            <div class="w-full md:w-1/2 form_field_container">
                                <label
                                    class="form_field_label"
                                    :class="{
                                        'text-gray-700': !dark_mode,
                                        'text-white': dark_mode,
                                    }"
                                >
                                    Model
                                </label>
                                <div
                                    class="flex flex-row items-center w-4/5"
                                    v-if="!loading_handset_models"
                                >
                                    <v-select
                                        :value="row['ModelId']"
                                        label="Name"
                                        v-model="row['ModelId']"
                                        :reduce="(model) => model.id"
                                        :options="handset_models"
                                        class="w-full generic_vs_select"
                                        :class="{
                                            required_field:
                                                row['ModelId'] == '' ||
                                                row['ModelId'] == null,
                                        }"
                                    ></v-select>

                                    <Button
                                        @click.native="addHandsetModel"
                                        icon="plus"
                                        split="border-white"
                                        class="ml-1 bg-green-600 text-white"
                                    >
                                        New
                                    </Button>
                                </div>
                                <Loading v-else />
                            </div>

                            <div class="w-full md:w-1/2 form_field_container">
                                <label
                                    class="form_field_label"
                                    :class="{
                                        'text-gray-700': !dark_mode,
                                        'text-white': dark_mode,
                                    }"
                                >
                                    Color
                                </label>
                                <div
                                    class="flex flex-row items-center w-4/5"
                                    v-if="!loading_handset_colors"
                                >
                                    <v-select
                                        :value="row['ColorId']"
                                        v-model="row['ColorId']"
                                        :reduce="(color) => color.id"
                                        label="Name"
                                        :options="handset_colors"
                                        class="w-full generic_vs_select"
                                        :class="{
                                            required_field:
                                                row['ColorId'] == '' ||
                                                row['ColorId'] == null,
                                        }"
                                    ></v-select>

                                    <Button
                                        @click.native="addHandsetColor"
                                        icon="plus"
                                        split="border-white"
                                        class="ml-1 bg-green-600 text-white"
                                    >
                                        New
                                    </Button>
                                </div>
                                <Loading v-else />
                            </div>

                            <div class="w-full"></div>

                            <div
                                class="w-full datatable_header"
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
                                    Other Details
                                </h2>
                            </div>

                            <div class="w-full form_field_container">
                                <label
                                    class="form_field_label"
                                    :class="{
                                        'text-gray-700': !dark_mode,
                                        'text-white': dark_mode,
                                    }"
                                >
                                    Notes
                                </label>
                                <textarea
                                    class="w-3/4 generic_input"
                                    v-model.trim="row['Notes']"
                                    rows="3"
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
                                    Reason For Not Repair
                                </label>
                                <textarea
                                    class="w-3/4 generic_input"
                                    v-model.trim="row['ReasonForNotRepair']"
                                    rows="3"
                                />
                            </div>

                            <RecordStamp :row="row" v-if="edit_id != ''" />
                        </div>
                    </form>
                </div>

                <div
                    class="w-1/2 ml-2 p-2"
                    :class="{
                        'text-gray-900': !dark_mode,
                        'text-white': dark_mode,
                    }"
                >
                    <div class="flex justify-end mb-1">
                        <Button
                            @click.native="addPart"
                            icon="search"
                            split="border-white"
                            class="ml-1 bg-green-600 text-white"
                            v-if="current_row_id == ''"
                        >
                            Add Part
                        </Button>
                    </div>

                    <SaleItemsDatatable
                        :columns="repair_parts_columns"
                        :options="repair_parts_options"
                        :source_data="rows"
                        v-bind:load_data_from_server="false"
                        :current_row_id="current_row_id"
                        :parent_row="row"
                        @edit-record="editRecord"
                        @remove-record="removeRecord"
                    ></SaleItemsDatatable>
                </div>
            </div>

            <Loading v-else />
        </div>
    </VueFinalModal>
</template>

<style>
.repair_modal {
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
import { notifications } from "../Helpers/notifications";
import moment from "moment";
import { defineAsyncComponent } from "vue";
import PartDetails from "../Datatable/Cells/Repair/PartDetails";

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
                InvoiceDate: moment().format("D-MMM-YYYY"),
                ReceivedDate: moment().format("D-MMM-YYYY"),
                PaymentMethod: "Cash",
                Amount: 0,
                VAT: 0,
                IMEI: "",
                MakeId: "",
                ModelId: "",
                ColorId: "",
                Notes: "",
                ReasonForNotRepair: "",
            },
            rows: [],
            children_to_delete: [],

            saving_data: false,
            validating_imei: false,
            invalid_imei: false,
            loading: false,
            customers_loaded: false,

            add_record_title: "Add",
            current_row_id: "",

            repair_parts_options: {
                enable_search: true,
                url: "",
                id: "selected_parts_table",
                pagination: true,
                primary_key: "id",
                record_name: "Part",
                sorting: {
                    enabled: true,
                    default: "Part",
                    direction: "asc",
                },
            },
            repair_parts_columns: [
                {
                    enabled: true,
                    key: "supplier.SupplierName",
                    name: "Supplier",
                    order: 1,
                    searching: false,
                    sorting: false,
                    td: "break-words",
                    th: "text-left",
                },
                {
                    enabled: true,
                    key: "part.Name",
                    name: "Part",
                    order: 2,
                    searching: false,
                    sorting: false,
                    th: "text-left break-words",
                },
                {
                    enabled: true,
                    key: "Cost",
                    name: "Cost",
                    order: 3,
                    searching: false,
                    sorting: false,
                    type: "Float",
                    th: "text-left break-words",
                },
                {
                    enabled: true,
                    key: "route",
                    name: "Actions",
                    order: 4,
                    searching: false,
                    sorting: false,
                    th: "",
                    type: "AddRepairPartActions",
                },
            ],
        };
    },

    mounted() {
        //Get the data from server
        if (this.edit_id != "") {
            this.add_record_title = "Add";

            this.loading = true;

            axios
                .get(route("repair.get-single"), {
                    params: {
                        id: this.edit_id,
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
            // this.child_row["row_id"] = helper_functions.getRandomId();
        }
    },

    created() {
        this.setTableMetaData({
            columns: this.repair_parts_columns,
            options: this.repair_parts_options,
        });

        this.setActiveTab(this.repair_parts_options.id);
    },

    methods: {
        validateIMEI() {
            if (this.row_keys.indexOf("IMEI") < 0 || this.row["IMEI"] == "") {
                return false;
            }

            this.validating_imei = true;
            this.invalid_imei = false;

            axios
                .post(route("phonestock.validate-imei"), {
                    IMEI: this.row["IMEI"],
                    check_duplicate: false,
                })
                .then(
                    (response) => {
                        this.invalid_imei = false;

                        this.validating_imei = false;
                    },
                    (error) => {
                        this.validating_imei = false;

                        if (error.response.data.message == "invalid_imei") {
                            this.invalid_imei = true;
                        }
                    }
                );
        },

        addPart() {
            this.current_row_id = "";
            this.addEditRepairPart({
                part_id: "",
                supplier_id: "",
                cost: "",
            });
        },

        editRecord(child_row) {
            this.current_row_id = _.clone(child_row["row_id"]);
            this.addEditRepairPart({
                part_id: child_row.part.id,
                supplier_id: child_row.supplier.id,
                cost: child_row.Cost,
            });
        },

        addEditRepairPart(part_details) {
            const parent = this;

            this.setPopperOpen(true);

            const { open, close } = useModal({
                component: PartDetails,
                attrs: {
                    data: part_details,
                    onSave: (part_details) => {
                        const child_row = {};
                        let object = this.parts.find(
                            (item) => item.id == part_details.part_id
                        );
                        if (object) {
                            child_row.part = {
                                id: part_details.part_id,
                                Name: object.Name,
                            };
                        }

                        object = this.parts_suppliers.find(
                            (item) => item.id == part_details.supplier_id
                        );
                        if (object) {
                            child_row.supplier = {
                                id: part_details.supplier_id,
                                SupplierName: object.SupplierName,
                            };
                        }

                        child_row["Cost"] = part_details["cost"];
                        child_row["_level"] = "1";

                        let rows = [];
                        let existing_row = false;
                        _.forIn(this.rows, (object, key) => {
                            if (object["row_id"] === this.current_row_id) {
                                rows.push(_.cloneDeep(child_row));
                                existing_row = true;
                            } else {
                                rows.push(object);
                            }
                        });
                        if (!existing_row) {
                            child_row.row_id = helper_functions.getRandomId();
                            rows.push(_.cloneDeep(child_row));
                        }

                        this.current_row_id = "";

                        this.rows = _.cloneDeep(rows);
                    },
                    onConfirm() {
                        close();
                    },
                    onClosed() {
                        parent.current_row_id = "";
                        parent.setPopperOpen(false);
                    },
                },
            });

            open();
        },

        removeRecord(row_id) {
            let rows = [];

            _.forIn(this.rows, (object, key) => {
                if (object["row_id"] != row_id) {
                    rows.push(_.cloneDeep(object));
                } else if (
                    Object.keys(object).indexOf("id") >= 0 &&
                    object["id"] != ""
                ) {
                    this.children_to_delete.push(object);
                }
            });

            this.rows = _.cloneDeep(rows);
        },

        setInvoiceDate(date) {
            if (date != "" && date != null) {
                this.row["InvoiceDate"] = date;
            } else {
                this.row["InvoiceDate"] = "";
            }
        },

        setReceivedDate(date) {
            if (date != "" && date != null) {
                this.row["ReceivedDate"] = date;
            } else {
                this.row["ReceivedDate"] = "";
            }
        },

        clearDate(key) {
            this.row[key] = "";
        },

        getColumnValue(column) {
            return _.get(this.row, column);
        },

        saveAll() {
            // if (this.rows.length == 0) {
            //     return false;
            // }

            this.row["operation"] = this.edit_id == "" ? "add" : "edit";
            this.row["children"] = this.rows;
            this.row["children_to_delete"] = this.children_to_delete;

            //save the user
            this.saving_data = true;

            axios.post(route("repair.save"), this.row).then(
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

        onCustomerSelected(value) {
            this.row["CustomerId"] = value;
        },

        customersLoaded() {
            if (!this.edit_id && !this.customers_loaded) {
                this.$nextTick(() => {
                    // this.$refs.customers_picker.$refs.customer_id.$el
                    //     .querySelector("input")
                    //     .focus();
                    this.customers_loaded = true;
                });
            }
        },

        numberFormat(value) {
            return helper_functions.number_format(value, 2);
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

    computed: {
        valid_data() {
            if (
                // this.rows.length == 0 ||
                this.row_keys.indexOf("InvoiceDate") < 0 ||
                this.row["InvoiceDate"] == "" ||
                this.row_keys.indexOf("CustomerId") < 0 ||
                this.row["CustomerId"] == "" ||
                this.row["CustomerId"] == null ||
                this.row_keys.indexOf("PaymentMethod") < 0 ||
                this.row["PaymentMethod"] == "" ||
                this.row["PaymentMethod"] == null ||
                (this.row_keys.indexOf("VAT") >= 0 &&
                    parseFloat(this.row["VAT"]) < 0) ||
                this.row["MakeId"] == "" ||
                this.row["MakeId"] == null ||
                this.row["ModelId"] == "" ||
                this.row["ModelId"] == null ||
                this.row["ColorId"] == "" ||
                this.row["ColorId"] == null
            ) {
                return false;
            }

            return true;
        },

        imei_validation_message() {
            if (this.invalid_imei) {
                return "Invalid IMEI.";
            }
            // else if (
            //     this.row_keys.indexOf("IMEI") < 0 ||
            //     this.row["IMEI"] === ""
            // ) {
            //     return "Required";
            // }

            return "";
        },

        row_keys() {
            return Object.keys(this.row);
        },

        ...mapState({
            dark_mode: (state) => state.framework.dark_mode,
            expanded_sidebar: (state) => state.framework.expanded_sidebar,
            local_settings: (state) => state.local_settings,
            refresh_suppliers: (state) => state.framework.refresh_suppliers,
            refresh_handset_models: (state) =>
                state.framework.refresh_handset_models,
            refresh_handset_manufacturers: (state) =>
                state.framework.refresh_handset_manufacturers,
            refresh_handset_colors: (state) =>
                state.framework.refresh_handset_colors,
        }),
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
        },
    },
};
</script>
