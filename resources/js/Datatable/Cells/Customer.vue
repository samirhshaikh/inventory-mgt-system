<template>
    <div class="leading-tight">
        {{ row["customer"] }}
        <div class="block" v-if="row['ContactNo1'] != ''">{{ row['ContactNo1'] }}</div>
        <div class="block" v-if="row['ContactNo2'] != ''">{{ row['ContactNo2'] }}</div>
    </div>
</template>

<script>
import {datatable_cell} from "./datatable_cell";

export default {
    mixins: [datatable_cell]
}
</script>
