<template>
    <div class="flex">
        <Button
            @click.native="returnItem"
            class="text-white bg-red-400"
            :class="{
                hidden: !$page.user_details.IsAdmin || row['Returned'] == 1
            }"
        >
            Return
        </Button>
        <span
            class="text-red-700"
            :class="{
                hidden: row['Returned'] == 0
            }"
        >Item Returned</span>
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
                    refresh: (id) => {
                        console.log(['SalePhoneActions', id]);
                        this.$emit("returnItem", id);
                    }
                },
                {
                    width: "500px",
                    height: "500px"
                }
            );
        },
    }
}
</script>
