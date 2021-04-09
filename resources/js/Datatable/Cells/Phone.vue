<template>
    <div class="leading-tight">
        {{ data["manufacturer"]["Name"] }} - {{ data["model"]["Name"] }} - {{ data["color"]["Name"] }}
    </div>
</template>

<script>
import {datatable_cell} from "./datatable_cell";

export default {
    mixins: [datatable_cell],

    computed: {
        data() {
            if (this.column == '') {
                return this.row;
            } else {
                return this.row[this.column];
            }
        }
    }
}
</script>
