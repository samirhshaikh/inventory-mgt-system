<template>
    <Layout title="Handset Colors">
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
                    <FA :icon="['fas', options.icon]" class="mr-1"></FA>
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
                        v-bind:advanced_search="false"
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

            <ObjectType1Datatable
                :columns="columns"
                :options="options"
                :page_no="page_no"
                :search_type="search_type"
                :search_text="search_text"
                :advanced_search_data="advanced_search_data"
                :update_search="update_search"
                @changeTotalReports="changeTotalReports"
                @changePageNo="changePage"
            ></ObjectType1Datatable>
        </div>
    </Layout>
</template>

<script>
import {mapState, mapActions} from "vuex";
import lazyLoadComponent from "@/Helpers/lazyLoadComponent.js";
import loading from "@/Misc/Loading.vue";
import ObjectTypeName from "../DBObjects/ObjectTypeName.vue";
import {datatable_common} from "../Helpers/datatable_common";

export default {
    mixins: [datatable_common],

    components: {
        ObjectType1Datatable: lazyLoadComponent({
            componentFactory: () => import("@/Datatable/Datatable"),
            loading: loading
        })
    },

    computed: {
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

            this.$modal.show(
                ObjectTypeName,
                {
                    edit_id: "",
                    options: this.options
                },
                {
                    width: "650px",
                    height: "600px"
                }
            );
        },

        ...mapActions({
            setTableMetaData: 'datatable/setTableMetaData',
            setActiveTab: 'local_settings/setActiveTab',
            setPopperOpen: "local_settings/setPopperOpen",
            addError: 'errors/addError'
        })
    }
};
</script>
