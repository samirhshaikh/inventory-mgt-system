/**
 * Mixin file. Commonly used methods in all data tables.
 * Methods related to search and pagination.
 */

import moment from "moment";

export const datatable_common = {
    props: {
        columns: {
            type: Array,
            default: () => [],
        },
        options: {
            type: Object,
            default: () => ({}),
        },
    },

    data() {
        return {
            total_records: 0,
            page_no: 1,
            search_type: "",
            search_text: "",
            advanced_search_data: {},
            update_search: "",
            expanded_row_id: 0,
        };
    },

    methods: {
        searchData(search_text) {
            this.search_type = "simple";
            this.search_text = search_text;
            this.advanced_search_data = {};
            this.update_search = moment().format();
        },

        clearSearch() {
            this.search_type = "cleared";
            this.search_text = "";
            this.advanced_search_data = {};
            this.update_search = moment().format();
        },

        triggerAdvancedSearch(column_data) {
            this.search_type = "advanced";
            this.search_text = "";
            this.advanced_search_data = _.cloneDeep(column_data);
            this.update_search = moment().format();
        },

        changePage(page_no) {
            this.page_no = page_no;
        },

        changeTotalReports(total_records) {
            this.total_records = total_records;
        },
    },
};
