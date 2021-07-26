<template>
    <Layout :title="options.record_name + 's'">
        <div class="px-4 py-4">
            <div
                class="flex items-stretch datatable_header"
                :class="{
                    'border-product-color-lighter bg-white': !dark_mode,
                    'border-product-color bg-gray-800': dark_mode
                }"
            >
                <h1
                    class="pt-1 ml-2 text-product-color text-2xl tracking-tight w-full"
                >
                    <FA :icon="['fas', 'mobile-alt']" class="mr-1"></FA>
                    {{ options.record_name }}s
                </h1>
                <div class="mr-2 flex flex-row">
                    <SearchBar
                        :placeholder_text="options.record_name + 's'"
                        v-if="options.enable_search"
                        class="mr-1"
                        @searchData="searchData"
                        @clearSearch="clearSearch"
                        @triggerAdvancedSearch="triggerAdvancedSearch"
                        :columns="search_columns"
                    ></SearchBar>
                    <Button
                        @click.native="newRecord"
                        icon="plus"
                        split="border-white"
                        class="text-white bg-green-600 float-right"
                    >
                        New {{ options.record_name }}
                    </Button>
                </div>
            </div>

            <Pagination
                :total_records="total_records"
                @changePage="changePage"
                class="mb-3 w-full inline-flex"
                :start_page_no="page_no"
                v-show="total_records"
            ></Pagination>

            <SearchParameters
                :columns="search_columns"
                :search_data="advanced_search_data"
                v-if="search_type == 'advanced'"
                @advancedSearchDataModified="triggerAdvancedSearch"
            ></SearchParameters>

            <SalesDatatable
                :columns="columns"
                :child_columns="child_columns"
                :expanded_row_id="expanded_row_id"
                :options="options"
                :page_no="page_no"
                :search_type="search_type"
                :search_text="search_text"
                :advanced_search_data="advanced_search_data"
                :update_search="update_search"
                @changeTotalReports="changeTotalReports"
                @changePageNo="changePage"
                @setExpandedRowId="setExpandedRowId"
            ></SalesDatatable>
        </div>
    </Layout>
</template>

<script>
import {mapState, mapActions} from "vuex";
import lazyLoadComponent from "@/Helpers/lazyLoadComponent.js";
import loading from "@/Misc/Loading.vue";
import Sale from "../DBObjects/Sale.vue";
import {list_controller} from "../Helpers/list_controller";
import {datatable_common} from "../Helpers/datatable_common";
import {common_functions} from "../Helpers/common_functions";
import SearchParameters from "../components/Search/SearchParameters";

export default {
    mixins: [list_controller, datatable_common, common_functions],

    props: {
        child_columns: {
            type: Array,
            default: () => ([])
        }
    },

    components: {
        SearchParameters,
        SalesDatatable: lazyLoadComponent({
            componentFactory: () => import("@/Datatable/Datatable"),
            loading: loading
        })
    },

    computed: {
        search_columns() {
            return [
                {
                    key: "customer",
                    label: "Customer Name",
                    type: "string",
                    data: this.customers_simple,
                    class: "w-60"
                },
                {
                    key: "InvoiceDate",
                    label: "Invoice Date",
                    type: "date",
                    class: "w-32"
                },
                {
                    key: "InvoiceNo",
                    label: "Invoice No",
                    type: "string",
                    class: "w-32"
                },
                {
                    key: "IMEI",
                    label: "IMEI",
                    type: "string",
                    class: "w-52"
                },
                {
                    key: "PaymentMethod",
                    label: "Payment Type",
                    type: "list",
                    data: this.payment_types,
                    class: "w-32"
                },
                {
                    key: "make",
                    label: "Make",
                    type: "list",
                    data: this.handset_manufacturers_simple,
                    class: "w-48"
                },
                {
                    key: "model",
                    label: "Model",
                    type: "list",
                    data: this.handset_models_simple,
                    class: "w-60"
                },
                {
                    key: "color",
                    label: "Color",
                    type: "list",
                    data: this.handset_colors_simple,
                    class: "w-64"
                },
                {
                    key: "Size",
                    label: "Size",
                    type: "list",
                    data: this.phone_sizes,
                    class: "w-32"
                },
                {
                    key: "Network",
                    label: "Network",
                    type: "list",
                    data: this.networks,
                    class: "w-48"
                },
                {
                    key: "TotalAmount",
                    label: "Amount",
                    type: "currency",
                    class: "w-32"
                },
                {
                    key: "UpdatedDate",
                    label: "Created/Updated Date",
                    type: "date",
                    class: "w-32"
                }
            ];
        },

        ...mapState({
            dark_mode: state => state.framework.dark_mode
        })
    },

    created() {
        this.setTableMetaData({
            columns: this.columns,
            options: this.options
        });

        this.setActiveTab(this.options.id);
    },

    methods: {
        newRecord() {
            this.setPopperOpen(true);

            //New to refresh the page on save and delete

            this.$modal.show(
                Sale,
                {
                    edit_id: "",
                    options: this.options,
                    columns: this.columns,
                    submitRecordSaved: (invoice_id) => {
                        this.setTableMetaData({
                            columns: this.columns,
                            options: this.options
                        });

                        this.setActiveTab(this.options.id);
                        this.setTabToRefresh(this.options.id);

                        //Open Print Invoice dialog
                        this.viewSalesInvoice(invoice_id);
                    }
                },
                {
                    width: "90%",
                    height: "80%"
                },
                {
                    "closed": event => {

                    }
                }
            );
        },

        setExpandedRowId(row_id) {
            this.expanded_row_id = row_id;
        },

        ...mapActions({
            setTableMetaData: 'datatable/setTableMetaData',
            setActiveTab: 'local_settings/setActiveTab',
            setTabToRefresh: 'framework/setTabToRefresh',
            setPopperOpen: "local_settings/setPopperOpen",
            addError: 'errors/addError'
        })
    }
};
</script>
