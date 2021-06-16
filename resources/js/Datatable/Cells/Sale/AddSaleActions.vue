<template>
    <div class="flex">
        <Button
            @click.native="edit"
            class="text-white bg-green-600"
            :class="{
                hidden: !$page.user_details.IsAdmin || row['row_id'] == current_row_id
            }"
        >
            Edit
        </Button>
        <Button
            @click.native="returnItem"
            class="text-white bg-red-400 ml-2"
            :class="{
                hidden: !$page.user_details.IsAdmin || !row.hasOwnProperty('Id') || row['Returned']
            }"
        >
            Return
        </Button>
        <span
            class="text-red-700 ml-2"
            :class="{
                hidden: row['Returned'] == 0
            }"
        >Item Returned</span>
        <Button
            @click.native="removeRecord"
            class="text-white bg-red-400 ml-2"
            :class="{
                hidden: !$page.user_details.IsAdmin || row['row_id'] == current_row_id
            }"
            split="border-white"
        >
            Delete
        </Button>
    </div>
</template>

<script>
import Confirm from "../../../components/Confirm.vue";
import ReturnItem from "../../../DBObjects/ReturnItem";
import {datatable_cell} from "../datatable_cell";
import {mapActions} from "vuex";

export default {
    mixins: [datatable_cell],

    props: {
        current_row_id: {
            type: String,
            default: ""
        },
        parent_row: {
            default: () => ({})
        },
    },

    methods: {
        edit() {
            this.$emit('editRecord', this.row);
        },

        removeRecord() {
            this.$modal.show(
                Confirm,
                {
                    title: "Delete " + this.options.record_name,
                    text:
                        "Are you sure you want to delete this " +
                        _.lowerCase(this.options.record_name) +
                        "?",
                    yes_handler: () => {
                        this.$emit('removeRecord', this.row["row_id"]);
                    }
                },
                {
                    width: "350px",
                    height: "auto"
                }
            );
        },

        returnItem() {
            this.$modal.show(
                ReturnItem,
                {
                    SalesInvoiceId: this.parent_row["Id"],
                    SalesInvoiceNo: this.parent_row["InvoiceNo"],
                    IMEI: this.row["IMEI"],
                    refresh: (IMEI) => {
                        this.$emit("returnItem", IMEI);
                    }
                },
                {
                    width: "500px",
                    height: "500px"
                }
            );
        },

        ...mapActions({
            refreshData: "framework/refreshData",
            addError: 'errors/addError'
        })
    }
};
</script>
