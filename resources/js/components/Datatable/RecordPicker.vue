<template>
    <VueFinalModal
        class="flex justify-center items-center"
        :content-class="[
            'record_picker_modal relative p-4 rounded-lg',
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
                    Search {{ options.record_name }}
                </h1>
                <div class="search_bar_container">
                    <SearchBar
                        :placeholder_text="options.record_name + 's'"
                        v-if="options.enable_search"
                        class="mr-1"
                        @search-data="searchData"
                        @clear-search="clearSearch"
                        @advanced-search-data-modified="triggerAdvancedSearch"
                        :columns="search_columns"
                    ></SearchBar>
                    <Button
                        @click.native="$emit('closed')"
                        icon="times"
                        split="border-white"
                        class="bg-red-600"
                    >
                        Close
                    </Button>
                    <Button
                        @click.native="submitRecords"
                        icon="plus"
                        split="border-white"
                        class="text-white ml-1"
                        :class="{
                            'bg-green-600':
                                timer && Object.keys(selected_records).length,
                            'bg-gray-600 text-gray-500 cursor-not-allowed':
                                timer && !Object.keys(selected_records).length,
                        }"
                    >
                        Submit
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

            <Datatable
                :columns="columns"
                :options="options"
                :page_no="page_no"
                :search_type="search_type"
                :search_text="search_text"
                :advanced_search_data="advanced_search_data"
                :update_search="update_search"
                :selected_records="selected_records"
                @change-total-reports="changeTotalReports"
                @change-page-no="changePage"
                @select-record="selectRecord"
            ></Datatable>
        </div>
    </VueFinalModal>
</template>

<style>
.record_picker_modal {
    width: 85%;
    min-height: 90%;
    max-height: 90%;
    overflow-y: auto;
}
</style>

<script>
import { mapActions, mapState } from "vuex";
import { VueFinalModal } from "vue-final-modal";
import loading from "@/Misc/Loading.vue";
import { list_controller } from "../../Helpers/list_controller";
import { datatable_common } from "../../Helpers/datatable_common";
import moment from "moment";
import { defineAsyncComponent } from "vue";

export default {
    mixins: [list_controller, datatable_common],

    props: {
        submitRecordsSelected: {
            type: Function,
        },
    },

    components: {
        VueFinalModal,
        Datatable: defineAsyncComponent({
            loader: () => import("@/Datatable/Datatable"),
            loadingComponent: loading,
        }),
    },

    data() {
        return {
            selected_records: {},
            timer: moment().format(),
        };
    },

    computed: {
        search_columns() {
            return [
                {
                    key: "IMEI",
                    label: "IMEI",
                    type: "string",
                    class: "w-52",
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
            local_settings: (state) => state.local_settings,
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
        selectRecord(row, select_record) {
            if (select_record) {
                if (!this.selected_records.hasOwnProperty(row["Id"])) {
                    this.selected_records[row["Id"]] = row;
                }
            } else {
                delete this.selected_records[row["Id"]];
            }

            this.updateTimer();
        },

        submitRecords() {
            const handler = this.submitRecordsSelected;
            if (typeof handler === "function") {
                handler(this.selected_records);

                this.$emit("confirm");
            }
        },

        updateTimer() {
            this.timer = moment().format();
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
