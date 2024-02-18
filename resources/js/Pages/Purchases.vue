<template>
    <Layout :title="options.record_name">
        <div class="px-4 py-4">
            <div
                class="datatable_header"
                :class="{
                    'border-product-color-lighter bg-white': !dark_mode,
                    'border-product-color bg-gray-800': dark_mode,
                }"
            >
                <h1>
                    <FA :icon="['fas', 'mobile-alt']" class="mr-1"></FA>
                    {{ options.record_name }}s
                </h1>
                <div class="search_bar_container">
                    <SearchBar
                        placeholder_text="Purchases"
                        :focus_on_search_bar="focus_on_search_bar"
                        v-if="options.enable_search"
                        class="mr-1"
                        @search-data="searchData"
                        @clear-search="clearSearch"
                        @advanced-search-data-modified="triggerAdvancedSearch"
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
                @change-page="changePage"
                class="mb-3 w-full inline-flex"
                :start_page_no="page_no"
                v-show="total_records"
            ></Pagination>

            <SearchParameters
                :columns="search_columns"
                :search_data="advanced_search_data"
                v-if="search_type == 'advanced'"
                @advanced-search-data-modified="triggerAdvancedSearch"
            ></SearchParameters>

            <PurchasesDatatable
                :columns="columns"
                :child_columns="child_columns"
                :expanded_row_id="expanded_row_id"
                :options="options"
                :page_no="page_no"
                :search_type="search_type"
                :search_text="search_text"
                :advanced_search_data="advanced_search_data"
                :update_search="update_search"
                @change-total-reports="changeTotalReports"
                @change-page-no="changePage"
                @set-expanded-row-id="setExpandedRowId"
            ></PurchasesDatatable>
        </div>
    </Layout>
</template>

<script>
import { mapState, mapActions } from "vuex";
import loading from "@/Misc/Loading.vue";
import Purchase from "../DBObjects/Purchase.vue";
import { list_controller } from "../Helpers/list_controller";
import { datatable_common } from "../Helpers/datatable_common";
import SearchParameters from "../components/Search/SearchParameters";
import { defineAsyncComponent } from "vue";
import { useModal } from "vue-final-modal";

export default {
    mixins: [list_controller, datatable_common],

    props: {
        child_columns: {
            type: Array,
            default: () => [],
        },
    },

    components: {
        SearchParameters,
        PurchasesDatatable: defineAsyncComponent({
            loader: () => import("@/Datatable/Datatable"),
            loadingComponent: loading,
        }),
    },

    data() {
        return {
            focus_on_search_bar: false,
        };
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
                    class: "w-64",
                    range: true,
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
            const parent = this;

            this.setPopperOpen(true);

            const { open, close } = useModal({
                component: Purchase,
                attrs: {
                    edit_id: "",
                    options: this.options,
                    columns: this.columns,
                    onConfirm() {
                        close();
                    },
                    onClosed() {
                        parent.setPopperOpen(false);

                        parent.focus_on_search_bar = true;
                    },
                },
            });

            open();
        },

        setExpandedRowId(row_id) {
            this.expanded_row_id = row_id;
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
