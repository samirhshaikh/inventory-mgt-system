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
                        Search {{ options.record_name }}
                    </h1>
                    <div
                        class="float-right flex justify-end mr-2 text-white w-64"
                    >
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
                            @click.native="$emit('close')"
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
                                'bg-green-600': timer && Object.keys(selected_records).length,
                                'bg-gray-600 text-gray-500 cursor-not-allowed': timer && !Object.keys(selected_records).length
                            }"
                        >
                            Submit {{ timer && Object.keys(selected_records).length }}
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

                <Datatable
                    :columns="columns"
                    :options="options"
                    :page_no="page_no"
                    :search_type="search_type"
                    :search_text="search_text"
                    :advanced_search_data="advanced_search_data"
                    :update_search="update_search"
                    :selected_records="selected_records"
                    @changeTotalReports="changeTotalReports"
                    @changePageNo="changePage"
                    @selectRecord="selectRecord"
                ></Datatable>
            </div>
        </div>
    </div>
</template>

<script>
import {mapActions, mapState} from "vuex";
import SearchParameters from "../Search/SearchParameters";
import lazyLoadComponent from "@/Helpers/lazyLoadComponent.js";
import loading from "@/Misc/Loading.vue";
import {list_controller} from "../../DBObjects/list_controller";
import {datatable_common} from "../../Pages/datatable_common";
import helper_functions from "../../store/modules/helper_functions";
import moment from "moment";

export default {
    mixins: [list_controller, datatable_common],

    props: {
        submitRecordsSelected: {
            type: Function
        }
    },

    components: {
        SearchParameters,
        Datatable: lazyLoadComponent({
            componentFactory: () => import("@/Datatable/Datatable"),
            loading: loading
        })
    },

    data() {
        return {
            selected_records: {},
            timer: moment().format()
        }
    },

    computed: {
        search_columns() {
            return [
                {
                    key: "IMEI",
                    label: "IMEI",
                    type: "string",
                    class: "w-52"
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
                    key: "ModelNo",
                    label: "Model No",
                    type: "string",
                    class: "w-48"
                },
                {
                    key: "Cost",
                    label: "Cost",
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
            dark_mode: state => state.framework.dark_mode,
            local_settings: state => state.local_settings
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

                this.$modal.hide(this.$parent.name);
            }
        },

        updateTimer() {
            this.timer = moment().format();
        },

        ...mapActions({
            setTableMetaData: 'datatable/setTableMetaData',
            setActiveTab: 'local_settings/setActiveTab',
            setPopperOpen: "local_settings/setPopperOpen",
            addError: 'errors/addError'
        })
    }
}
</script>
