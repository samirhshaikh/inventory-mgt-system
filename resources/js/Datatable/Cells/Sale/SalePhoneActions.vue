<template>
    <div class="flex">
        <Button
            @click.native="returnItem"
            class="text-white bg-red-400 mr-2"
            :class="{
                hidden: !$page.user_details.IsAdmin || row['Returned'] == 1,
            }"
        >
            Return
        </Button>
        <span
            class="text-red-700 leading-normal mr-2"
            :class="{
                hidden: row['Returned'] == 0,
            }"
            >Returned</span
        >
        <Button
            @click.native="tradeInDetails"
            class="text-white bg-green-600 mr-2"
            :class="{
                hidden:
                    !$page.user_details.IsAdmin ||
                    !parent_row.hasOwnProperty('tradein'),
            }"
        >
            Trade In Details
        </Button>
    </div>
</template>

<script>
import ReturnItem from "../../../DBObjects/ReturnItem";
import { datatable_cell } from "../datatable_cell";
import { notifications } from "../../../Helpers/notifications";
import { mapActions } from "vuex";
import Purchase from "../../../DBObjects/Purchase";

export default {
    mixins: [datatable_cell, notifications],

    props: {
        parent_row: {
            default: () => ({}),
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
                    refresh: (IMEI) => {
                        this.$emit("returnItem", IMEI);
                    },
                },
                {
                    width: "500px",
                    height: "500px",
                }
            );
        },

        tradeInDetails() {
            this.setPopperOpen(true);

            this.$modal.show(
                Purchase,
                {
                    edit_id: String(this.parent_row.tradein.PurchaseInvoiceId),
                    options: {
                        id: "purchases",
                        child_record_name: "Phone",
                        record_name: "Purchase",
                        enable_search: false,
                        pagination: false,
                        primary_key: "Id",
                        sorting: {
                            default: "UpdatedDate",
                            direction: "desc",
                            enabled: false,
                        },
                    },
                },
                {
                    width: "90%",
                    height: "90%",
                }
            );
        },

        ...mapActions({
            refreshData: "framework/refreshData",
            setPopperOpen: "local_settings/setPopperOpen",
            addError: "errors/addError",
        }),
    },
};
</script>
