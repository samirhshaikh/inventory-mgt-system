<template>
    <div class="flex">
        <Button
            @click.native="returnItem"
            class="text-white bg-red-400"
            :class="{
                hidden: !$page.user_details.IsAdmin
            }"
        >
            Return
        </Button>
        <Button
            @click.native="viewSalesInvoice"
            class="text-white bg-green-600 ml-2"
            :class="{
                hidden: !$page.user_details.IsAdmin
            }"
        >
            Invoice
        </Button>
    </div>

</template>

<script>
import ReturnItem from "../../../DBObjects/ReturnItem";
import {datatable_cell} from "../datatable_cell";
import {notifications} from "../../../Helpers/notifications";

export default {
    mixins: [datatable_cell, notifications],

    props: {
        parent_row: {
            default: () => ({})
        },
    },

    methods: {
        returnItem() {
            this.$modal.show(
                ReturnItem,
                {
                    SalesInvoiceId: this.parent_row["Id"],
                    SalesInvoiceNo: this.parent_row["InvoiceNo"],
                    IMEI: this.row["IMEI"],
                    refresh: () => {
                        this.$emit("returnItem", this.row["row_id"]);
                    }
                },
                {
                    width: "500px",
                    height: "400px"
                }
            );
        },

        viewSalesInvoice() {

        }
    }
}
</script>
