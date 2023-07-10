<template>
    <Layout title="PhoneStock">
        <div class="px-4 py-4">
            <div
                class="flex items-stretch datatable_header"
                :class="{
                    'border-product-color-lighter bg-white': !dark_mode,
                    'border-product-color bg-gray-800': dark_mode,
                }"
            >
                <h1
                    class="pt-1 ml-2 text-product-color text-2xl tracking-tight w-full"
                >
                    <FA :icon="['fas', 'mobile-alt']" class="mr-1"></FA>
                    Phone Stock
                </h1>
                <div class="mr-2 flex flex-row">
                    <SearchBar
                        placeholder_text="Phone Stock"
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
                        New Stock
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

            <PhoneStockDatatable
                :columns="columns"
                :options="options"
                :page_no="page_no"
                :search_type="search_type"
                :search_text="search_text"
                :advanced_search_data="advanced_search_data"
                :update_search="update_search"
                @changeTotalReports="changeTotalReports"
                @changePageNo="changePage"
            ></PhoneStockDatatable>
        </div>
    </Layout>
</template>

<script>
import { mapState, mapActions } from "vuex";
import loading from "@/Misc/Loading.vue";
import PhoneStock from "../DBObjects/Purchase.vue";
import { list_controller } from "../Helpers/list_controller";
import { datatable_common } from "../Helpers/datatable_common";
import SearchParameters from "../components/Search/SearchParameters";
import { defineAsyncComponent } from "vue";

export default {
    mixins: [list_controller, datatable_common],

    components: {
        SearchParameters,
        PhoneStockDatatable: defineAsyncComponent({
            loader: () => import("@/Datatable/Datatable"),
            loadingComponent: loading,
        }),
    },

    computed: {
        search_columns() {
            return [
                {
                    key: "supplier",
                    label: "Supplier Name",
                    type: "list",
                    data: this.suppliers_simple,
                    class: "w-60",
                },
                {
                    key: "InvoiceDate",
                    label: "Invoice Date",
                    type: "date",
                    class: "w-32",
                },
                {
                    key: "InvoiceNo",
                    label: "Invoice No",
                    type: "string",
                    class: "w-32",
                },
                {
                    key: "IMEI",
                    label: "IMEI",
                    type: "string",
                    class: "w-52",
                },
                {
                    key: "StockType",
                    label: "Stock Type",
                    type: "list",
                    data: this.stock_types,
                    class: "w-32",
                },
                {
                    key: "Status",
                    label: "Stock Status",
                    type: "list",
                    data: this.stock_statuses,
                    class: "w-48",
                },
                {
                    key: "make",
                    label: "Make",
                    type: "list",
                    data: this.handset_manufacturers_simple,
                    class: "w-48",
                },
                {
                    key: "model",
                    label: "Model",
                    type: "list",
                    data: this.handset_models_simple,
                    class: "w-60",
                },
                {
                    key: "color",
                    label: "Color",
                    type: "list",
                    data: this.handset_colors_simple,
                    class: "w-64",
                },
                {
                    key: "Size",
                    label: "Size",
                    type: "list",
                    data: this.phone_sizes,
                    class: "w-32",
                },
                {
                    key: "Network",
                    label: "Network",
                    type: "list",
                    data: this.networks,
                    class: "w-48",
                },
                {
                    key: "Cost",
                    label: "Cost",
                    type: "currency",
                    class: "w-32",
                },
                {
                    key: "UpdatedDate",
                    label: "Created/Updated Date",
                    type: "date",
                    class: "w-32",
                },
            ];
        },

        ...mapState({
            dark_mode: (state) => state.framework.dark_mode,
        }),
    },

    created() {
        this.setTableMetaData({
            columns: this.columns,
            options: this.options,
        });

        this.setActiveTab(this.options.id);
    },

    methods: {
        newRecord() {
            this.setPopperOpen(true);

            this.$modal.show(
                PhoneStock,
                {
                    edit_id: "",
                    options: this.options,
                    columns: this.columns,
                },
                {
                    width: "90%",
                    height: "80%",
                }
            );
        },

        ...mapActions({
            setTableMetaData: "datatable/setTableMetaData",
            setActiveTab: "local_settings/setActiveTab",
            setPopperOpen: "local_settings/setPopperOpen",
            addError: "errors/addError",
        }),
    },
};
</script>
