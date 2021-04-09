<template>
    <div class="leading-tight">
        {{ row["InvoiceDate"] }}
        <div class="block" v-if="row['InvoiceNo'] != ''">{{ row['InvoiceNo'] }}</div>
    </div>
</template>

<script>
import {datatable_cell} from "./datatable_cell";

export default {
    mixins: [datatable_cell]
}
</script>
