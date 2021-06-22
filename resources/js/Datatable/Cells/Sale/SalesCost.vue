<template>
    <div class="leading-tight">
        {{ data }}
    </div>
</template>

<script>
import helper_functions from "../../../Helpers/helper_functions";
import {datatable_cell} from "../datatable_cell";

export default {
    mixins: [datatable_cell],

    computed: {
        data() {
            let total_cost = 0;
            _.forEach(this.row[this.column], (row, key) => {
                if (!row['Returned']) {
                    total_cost += parseFloat(row['Cost']);
                }
            });

            return total_cost > 0 ? "£" + helper_functions.number_format(total_cost, 2) : "";
        }
    }
};
</script>
