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
            @click.native="returnItem"
            class="text-white bg-red-400"
            :class="{
                hidden: !$page.user_details.IsAdmin || row.Status != this.phonestock.STATUS_SOLD
            }"
        >
            Return
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
import Sales from "../../DBObjects/Sales.vue";
import { mapActions } from "vuex";
import Confirm from "../../components/Confirm.vue";
import {datatable_cell} from "./datatable_cell";
import ReturnItem from "../../DBObjects/ReturnItem";

export default {
    mixins: [datatable_cell],

    methods: {
        sellItem() {
            this.setPopperOpen(true);

            this.$modal.show(
                Sales,
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

        returnItem() {
            this.setPopperOpen(true);

            this.$modal.show(
                ReturnItem,
                {
                    IMEI: this.row["IMEI"],
                    refresh: () => {
                        this.refreshData(this.options.id);
                    }
                },
                {
                    width: "500px",
                    height: "400px"
                }
            );
        },

        viewSalesInvoice() {

        },

        ...mapActions({
            refreshData: "framework/refreshData",
            setPopperOpen: "local_settings/setPopperOpen",
            addError: "errors/addError"
        })
    }
};
</script>
