<template>
    <div class="leading-tight">
        {{ phones }}
    </div>
</template>

<script>
import {datatable_cell} from "./datatable_cell";

export default {
    mixins: [datatable_cell],

    computed: {
        phones() {
            return this.data.split(",").join("<br>");
        }
    }
}
</script>
