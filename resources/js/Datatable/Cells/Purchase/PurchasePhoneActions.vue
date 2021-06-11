<template>
    <div class="flex">
        <Button
            @click.native="sellItem"
            class="text-white bg-green-600"
            :class="{
                hidden: !$page.user_details.IsAdmin || row.Status == this.phonestock.STATUS_SOLD
            }"
        >
            Sell
        </Button>
        <Button
            @click.native="viewSalesInvoice"
            class="text-white bg-green-600 ml-2"
            :class="{
                hidden: !$page.user_details.IsAdmin || row.Status != this.phonestock.STATUS_SOLD
            }"
        >
            Invoice
        </Button>
    </div>
</template>

<script>
import Sale from "../../../DBObjects/Sale.vue";
import { mapActions } from "vuex";
import {datatable_cell} from "../datatable_cell";
import {common_functions} from "../../../Helpers/common_functions";

export default {
    mixins: [datatable_cell, common_functions],

    methods: {
        sellItem() {
            this.setPopperOpen(true);

            this.$modal.show(
                Sale,
                {
                    edit_id: String(this.row.Id),
                    options: this.options,
                    phones: [
                        this.row.IMEI
                    ]
                },
                {
                    width: "90%",
                    height: "80%"
                }
            );
        },

        viewSalesInvoice() {
            // this.viewSalesInvoice();
        },

        ...mapActions({
            refreshData: "framework/refreshData",
            setPopperOpen: "local_settings/setPopperOpen",
            addError: "errors/addError"
        })
    }
};
</script>
