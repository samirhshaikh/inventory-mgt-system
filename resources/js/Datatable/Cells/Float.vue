<template>
    <div class="leading-tight">
        {{ data }}
    </div>
</template>

<script>
import helper_functions from "../../Helpers/helper_functions";
import {datatable_cell} from "./datatable_cell";

export default {
    mixins: [datatable_cell],

    computed: {
        data() {
            let number = _.get(this.row, this.column, false);

            return number > 0 ? "£" + helper_functions.number_format(number, 2) : "";
        }
    }
};
</script>
