<template>
    <div class="leading-tight">
        <a @click="collapse_expand_row" class="cursor-pointer text-xs">
            <FA class="fas" :icon="icon"></FA>
        </a>
    </div>
</template>

<script>
import helper_functions from "../../Helpers/helper_functions";
import {datatable_cell} from "./datatable_cell";

export default {
    mixins: [datatable_cell],

    computed: {
        icon() {
            return this.row["Id"] == this.expanded_row_id ? 'minus' : 'plus';
        }
    },

    methods: {
        collapse_expand_row() {
            this.$emit("setExpandedRowId", this.row["Id"] == this.expanded_row_id ? 0 : this.row["Id"]);
        }
    }
};
</script>
