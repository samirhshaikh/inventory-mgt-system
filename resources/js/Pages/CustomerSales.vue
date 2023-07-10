<template>
    <Layout :title="options.record_name + 's'">
        <div class="px-4 py-4">
            <div
                class="datatable_header"
                :class="{
                    'border-product-color-lighter bg-white': !dark_mode,
                    'border-product-color bg-gray-800': dark_mode,
                }"
            >
                <h1>
                    <FA :icon="['fas', 'users']" class="mr-1 capitalize"></FA>
                    {{ options.record_name }}s
                </h1>
                <div class="search_bar_container">
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

            <CustomerSalesDatatable
                :columns="columns"
                :options="options"
                :page_no="page_no"
                :search_type="search_type"
                :search_text="search_text"
                :advanced_search_data="advanced_search_data"
                :update_search="update_search"
                @changeTotalReports="changeTotalReports"
                @changePageNo="changePage"
            ></CustomerSalesDatatable>
        </div>
    </Layout>
</template>

<script>
import { mapState, mapActions } from "vuex";
import loading from "@/Misc/Loading.vue";
import CustomerSale from "../DBObjects/CustomerSale.vue";
import { datatable_common } from "../Helpers/datatable_common";
import { defineAsyncComponent } from "vue";

export default {
    mixins: [datatable_common],

    components: {
        CustomerSalesDatatable: defineAsyncComponent({
            loader: () => import("@/Datatable/Datatable"),
            loadingComponent: loading,
        }),
    },

    computed: {
        search_columns() {
            return [
                {
                    key: "CustomerName",
                    label: "Customer Name",
                    type: "string",
                    class: "w-60",
                },
                {
                    key: "ContactNo",
                    label: "Contact No",
                    type: "string",
                    class: "w-48",
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
                CustomerSale,
                {
                    edit_id: "",
                    options: this.options,
                },
                {
                    width: "750px",
                    height: "600px",
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
