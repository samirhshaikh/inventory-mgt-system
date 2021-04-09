<template>
    <div className="leading-tight">
        {{ row["phone"]["manufacturer"]["Name"] }} - {{ row["model"]["Name"] }} - {{ row["color"]["Name"] }}
    </div>
</template>

<script>
import {datatable_cell} from "./datatable_cell";

export default {
    mixins: [datatable_cell]
}
</script>
