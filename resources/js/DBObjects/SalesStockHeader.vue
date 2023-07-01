<template>
    <tr type="header" v-show="expanded_row_id == row.Id">
        <th
            v-for="child_header in active_child_columns"
            v-if="child_header.enabled"
            class="py-2 lg:py-2 px-1 lg:px-2 uppercase border-b border-gray-200"
            :class="[
                child_header.th,
                child_header.td,
                {
                    'bg-gray-200 text-gray-700 hover:bg-gray-300': !dark_mode,
                    'bg-gray-900 text-gray-500 hover:bg-gray-800': dark_mode,
                },
            ]"
            :colspan="child_header.column_span"
        >
            <span>
                {{ child_header.name }}
            </span>
        </th>
    </tr>
</template>

<script>
import { mapActions, mapState, mapGetters } from "vuex";

export default {
    props: {
        active_child_columns: {
            type: Array,
            default: () => [],
        },
        row: {
            type: Object,
            default: () => ({}),
        },
        options: {
            type: Object,
            default: () => ({}),
        },
        expanded_row_id: {
            type: Number,
            default: 0,
        },
    },

    computed: {
        ...mapState({
            dark_mode: (state) => state.framework.dark_mode,
            refresh_data: (state) => state.framework.refresh_data,
            datatable: (state) => state.datatable,
            framework: (state) => state.framework,
            local_settings: (state) => state.local_settings,
            active_tab: (state) => state.local_settings.active_tab,
            tab_to_refresh: (state) => state.framework.tab_to_refresh,
        }),
    },

    methods: {
        getValue(row, column) {
            return _.get(row, column);
        },

        rowClass(row) {
            let light_theme = {
                0: "bg-white",
                1: "bg-gray-100",
            };

            let dark_theme = {
                0: "bg-transparent",
                1: "bg-gray-700",
            };

            let classes = [];
            classes.push(
                this.dark_mode
                    ? dark_theme[row._level]
                    : light_theme[row._level]
            );

            if (
                this.expanded_row_id &&
                row._level == 0 &&
                this.expanded_row_id != row.Id
            ) {
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

        returnItem(row_id) {
            this.$emit("returnItem", row_id);
        },
    },
};
</script>
