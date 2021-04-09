<template>
    <div class="leading-tight">
        {{ data }}
        <div class="block" v-if="row['ContactNo2'] != ''">{{ row['ContactNo2'] }}</div>
    </div>
</template>

<script>
import {datatable_cell} from "./datatable_cell";

export default {
    mixins: [datatable_cell]
};
</script>
