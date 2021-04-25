<template>
    <div class="flex">
        <Button
            @click.native="selectRecord"
            class="text-white "
            :class="{
                'bg-green-600': !row_selected,
                'bg-red-400': row_selected
            }"
        >
            {{ row_selected ? "UnSelect" : "Select" }}
        </Button>
    </div>
</template>

<script>
import {datatable_cell} from "../datatable_cell";
import moment from "moment";

export default {
    mixins: [datatable_cell],

    props: {
        selected_records: {
            type: Object,
            default: () => ({})
        }
    },

    computed: {
        row_keys() {
            return Object.keys(this.row);
        },
    },

    data() {
        return {
            row_selected: false
        }
    },

    mounted() {
        this.row_selected = this.selected_records.hasOwnProperty(this.row["Id"]);
    },

    methods: {
        selectRecord() {
            this.row_selected = !this.row_selected;

            _.set(this.row, 'row_selected', this.row_selected);

            this.$emit('selectRecord', this.row, this.row_selected);
        }
    }
};
</script>
