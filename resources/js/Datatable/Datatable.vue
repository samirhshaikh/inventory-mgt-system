<template>
    <div>
        <table
            class="table text-xs md:text-sm w-full border-collapse"
            ref="table"
        >
            <thead>
                <Row type="header">
                    <th
                        v-for="header in active_columns"
                        v-if="header.enabled"
                        class="py-2 lg:py-4 px-1 lg:px-2 font-bold uppercase border-b border-gray-200 z-10 table_header"
                        :class="[
                            header.th,
                            header.td,
                            {
                                'bg-gray-200 text-gray-700 hover:bg-gray-300': !dark_mode,
                                'bg-gray-900 text-gray-500 hover:bg-gray-800': dark_mode
                            }
                        ]"
                        :colspan="header.column_span"
                    >
                        <span
                            v-if="options.sorting.enabled && header.sorting"
                            class="flex cursor-pointer select-none"
                            @click="setTableSort(header)"
                        >
                            <div class="mr-1" v-if="current_sorting.column == header.key">
                                <FA
                                    v-if="current_sorting.direction == 'asc'"
                                    :icon="['fas', 'arrow-down']"
                                ></FA>
                                <FA
                                    v-if="current_sorting.direction == 'desc'"
                                    :icon="['fas', 'arrow-up']"
                                ></FA>
                            </div>
                            <div>{{ header.name }}</div>
                        </span>
                        <span v-else>
                            {{ header.name }}
                        </span>
                    </th>
                </Row>
            </thead>

            <tfoot v-if="Object.keys(totals).length > 0">
                <Row type="footer">
                    <td>
                        <span
                            v-if="typeof header.type === undefined"
                            v-html="getValue(totals, header.key)"
                        ></span>
                        <span
                            v-else
                            :is="header.type"
                            :row="totals"
                            :column="header.key"
                        ></span>
                    </td>
                </Row>
            </tfoot>

            <tbody>
                <Row type="body" v-if="error == true || loading == true || data.length === 0">
                    <td :colspan="active_columns.length">
                        <DatatableAlert type="info" v-if="loading == true">Loading...</DatatableAlert>

                        <DatatableAlert type="error" v-if="error == true">Some error occurred. Please try again</DatatableAlert>

                        <DatatableAlert type="info" v-if="data.length == 0 && loading == false && error == false">No rows found.</DatatableAlert>
                    </td>
                </Row>
            </tbody>

            <tbody v-for="(row, row_id) in data">
                <Row
                    type="body"
                    :class="[
                        rowClass(row),
                        {
                            'hover:bg-gray-300': !dark_mode,
                            'hover:bg-gray-700': dark_mode
                        }
                    ]"
                    :key="row.hash"
                >
                    <td
                        v-for="(header, index) in active_columns"
                        v-if="header.enabled"
                        class="px-1 lg:px-2 border-b border-gray-200 z-0"
                        :class="[
                            header.th,
                            header.td,
                            cellClass(row, header),
                            {
                                'border-gray-200': !dark_mode,
                                'border-gray-700': dark_mode
                            }
                        ]"
                        :colspan="header.column_span"
                        :key="index"
                        :data-column="header.key"
                    >
                        <span
                            v-if="typeof header.type === 'undefined'"
                            v-html="getValue(row, header.key)"
                        ></span>
                        <span
                            v-else
                            :is="header.type"
                            :row="row"
                            :column="header.key"
                            :column_details="header"
                            :options="options"
                            :current_row_id="current_row_id"
                            :selected_records="selected_records"
                            :expanded_row_id="expanded_row_id"
                            :parent_row="parent_row"
                            @editRecord="editRecord"
                            @removeRecord="removeRecord"
                            @selectRecord="selectRecord"
                            @setExpandedRowId="setExpandedRowId"
                            @returnItem="returnItem"
                        ></span>
                    </td>
                </Row>

                <!-- Child rows -->
                <Row type="header" v-show="expanded_row_id == row.Id">
                    <th
                        v-for="child_header in active_child_columns"
                        v-if="child_header.enabled"
                        class="py-2 lg:py-2 px-1 lg:px-2 uppercase border-b border-gray-200"
                        :class="[
                            child_header.th,
                            child_header.td,
                            {
                                'bg-gray-200 text-gray-700 hover:bg-gray-300': !dark_mode,
                                'bg-gray-900 text-gray-500 hover:bg-gray-800': dark_mode
                            }
                        ]"
                        :colspan="child_header.column_span"
                    >
                        <span>
                            {{ child_header.name }}
                        </span>
                    </th>
                </Row>

                <Row
                    type="body"
                    v-for="(child_row, child_id) in row.children"
                    :class="[
                        rowClass(child_row),
                        {
                            'hover:bg-gray-300': !dark_mode,
                            'hover:bg-gray-700': dark_mode
                        }
                    ]"
                    :key="child_row.hash"
                    v-show="expanded_row_id == row.Id"
                >
                    <td
                        v-for="(child_header, child_index) in active_child_columns"
                        v-if="child_header.enabled"
                        class="px-1 lg:px-2 border-b border-gray-200 z-0"
                        :class="[
                            child_header.th,
                            child_header.td,
                            cellClass(child_row, child_header),
                            {
                                'border-gray-200': !dark_mode,
                                'border-gray-700': dark_mode
                            }
                        ]"
                        :colspan="child_header.column_span"
                        :key="child_index"
                        :data-column="child_header.key"
                    >
                        <span
                            v-if="typeof child_header.type === 'undefined'"
                            v-html="getValue(child_row, child_header.key)"
                        ></span>
                        <span
                            v-else
                            :is="child_header.type"
                            :row="child_row"
                            :parent_row="row"
                            :column="child_header.key"
                            :column_details="child_header"
                            :options="options"
                            @editRecord="editRecord"
                            @removeRecord="removeRecord"
                            @selectRecord="selectRecord"
                            @returnItem="returnItem"
                        ></span>
                    </td>
                </Row>

            </tbody>
        </table>
    </div>
</template>

<script>
import { mapActions, mapState, mapGetters } from "vuex";

export default {
    props: {
        columns: {
            type: Array,
            default: () => ([])
        },
        child_columns: {
            type: Array,
            default: () => ([])
        },
        options: {
            type: Object,
            default: () => ({})
        },
        search_text: {
            type: String,
            default: ''
        },
        page_no: {
            type: Number,
            default: 1
        },
        source_data: {
            type: Array,
            default: () => ([])
        },
        load_data_from_server: {
            type: Boolean,
            default: true
        },
        current_row_id: {
            type: String,
            default: ""
        },
        expanded_row_id: {
            type: Number,
            default: 0
        },
        advanced_search_data: {
            type: Object,
            default: () => ({})
        },
        search_type: {
            type: String,
            default: ""
        },
        update_search: {
            type: String,
            default: ""
        },
        selected_records: {
            type: Object,
            default: () => ({})
        },
        parent_row: {
            type: Object,
            default: () => ({})
        }
    },

    data() {
        return {
            data: [],
            totals: [],
            error: false,
            data_loaded: false,
            loading: false
        };
    },

    computed: {
        ...mapState({
            dark_mode: state => state.framework.dark_mode,
            refresh_data: state => state.framework.refresh_data,
            datatable: state => state.datatable,
            framework: state => state.framework,
            local_settings: state => state.local_settings,
            active_tab: state => state.local_settings.active_tab,
            tab_to_refresh: state => state.framework.tab_to_refresh
        }),

        ...mapGetters({
            sorting: "datatable/sorting"
        }),

        all_columns() {
            return this.columns;

            // if (
            //     !this.active_tab ||
            //     typeof this.datatable.meta[this.active_tab] === "undefined"
            // ) {
            //     return this.columns;
            // }
            //
            // return this.datatable.meta[this.active_tab].columns;
        },

        all_child_columns() {
            return this.child_columns;
        },

        active_columns() {
            return this.all_columns
                .filter(item => item.enabled)
                .sort((a, b) => a.order - b.order);
        },

        active_child_columns() {
            return this.child_columns
                .filter(item => item.enabled)
                .sort((a, b) => a.order - b.order);
        },

        current_sorting() {
            return this.sorting(this.active_tab);
        },
    },

    created() {
        this.setTableMetaData({
            columns: this.columns,
            child_columns: this.child_columns,
            options: this.options
        });

        if (this.load_data_from_server) {
            this.getData();
        } else {
            this.data = this.source_data;
            this.totals = [];
        }

        if (
            typeof this.options === "undefined" ||
            typeof this.options.url === "undefined"
        ) {
            this.error = true;
            return;
        }
    },

    methods: {
        getValue(row, column) {
            return _.get(row, column);
        },

        getData(force_data_load) {
            if (!this.load_data_from_server || (this.data_loaded && !force_data_load)) {
                return;
            }

            this.setExpandedRowId(0);

            this.error = false;
            this.data = [];
            this.totals = [];

            this.toggleLoading();

            let data = {};
            data.page_no = this.page_no;
            if (this.search_type === "advanced") {
                data.search_data = this.advanced_search_data;
                data.search_type = "advanced";
            } else {
                data.search_text = this.search_text;
                data.search_type = "simple";
            }

            axios
                .get(this.options.url, {
                    params: data
                })
                .then(response => {
                    this.data = response.data.rows;
                    this.totals = response.data.totals || [];
                    this.toggleLoading();

                    this.$emit('changeTotalReports', response.data.total_rows);
                    this.$emit('changePageNo', response.data.page_no);
                })
                .catch(response => {
                    this.error = true;
                    this.toggleLoading();
                });

            this.data_loaded = true;
        },

        setTableSort(header) {
            this.setExpandedRowId(0);

            let payload = {
                tab: this.options.id,
                column: header.key
            };

            this.setSorting(payload);
        },

        rowClass(row) {
            let light_theme = {
                0: "bg-white",
                1: "bg-gray-100",
            };

            let dark_theme = {
                0: "bg-transparent",
                1: "bg-gray-700"
            };

            let classes = [];
            classes.push(
                this.dark_mode
                    ? dark_theme[row._level]
                    : light_theme[row._level]
            );

            if (this.expanded_row_id && row._level == 0 && this.expanded_row_id != row.Id) {
                classes.push("opacity-10");
            }

            return classes;
        },

        cellClass(row, header) {
            let value = this.getValue(row, header.key);

            let classes = [];

            classes.push("py-1 lg:py-3 align-top");

            if (typeof value === "undefined" || value == null) {
                return classes;
            }

            if (
                header.rules &&
                header.rules.includes("number") &&
                parseFloat(value) < 0
            ) {
                classes.push("text-red-500");
            }

            if (
                header.rules &&
                header.rules.included("money") &&
                value.includes("-")
            ) {
                classes.push("text-red-500");
            }

            if (typeof value === "string" && value.includes("*")) {
                classes.push("text-green-500");
            }

            return classes;
        },

        editRecord(row) {
            this.$emit('editRecord', row);
        },

        removeRecord(row_id) {
            this.$emit('removeRecord', row_id);
        },

        selectRecord(row_id, select_record) {
            this.$emit('selectRecord', row_id, select_record);
        },

        setExpandedRowId(row_id) {
            this.$emit('setExpandedRowId', row_id);
        },

        returnItem(IMEI) {
            this.$emit('returnItem', IMEI);

            let rows = [];

            _.forIn(this.data, (object, key) => {
                let children = [];

                _.forIn(object["children"], (child_object, child_key) => {
                    if (child_object["IMEI"] == IMEI) {
                        child_object['Returned'] = 1;
                    }

                    children.push(_.cloneDeep(child_object));
                });
                object["children"] = _.cloneDeep(children);

                rows.push(_.cloneDeep(object));
            });

            this.data = _.cloneDeep(rows);
        },

        toggleLoading() {
            this.loading = !this.loading;
        },

        ...mapActions({
            setTableMetaData: "datatable/setTableMetaData",
            setSorting: "datatable/setSorting",
            setCachedData: "local_settings/setCachedData"
        })
    },

    watch: {
        active_tab: function() {
            if (this.active_tab == this.options.id) {
                this.getData(true);
            }
        },

        refresh_data: function() {
            if (this.tab_to_refresh === this.active_tab) {
                this.data_loaded = false;
                this.getData(true);
            }
        },

        page_no: function(new_value, old_value) {
            this.setExpandedRowId(0);
            this.data_loaded = false;
            this.getData(true);
        },

        source_data: function(new_value, old_value) {
            this.data = new_value;
        },

        update_search: function(value) {
            this.data_loaded = false;
            this.getData(true);
        }
    }
};
</script>
