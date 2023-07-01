<template>
    <div class="flex">
        <Button
            @click.native="sellItem"
            class="text-white bg-green-600"
            :class="{
                hidden:
                    !$page.user_details.IsAdmin ||
                    row.Status == this.phonestock.STATUS_SOLD,
            }"
        >
            Sell
        </Button>
    </div>
</template>

<script>
import Sale from "../../../DBObjects/Sale.vue";
import { mapActions } from "vuex";
import { datatable_cell } from "../datatable_cell";
import { common_functions } from "../../../Helpers/common_functions";

export default {
    mixins: [datatable_cell, common_functions],

    methods: {
        sellItem() {
            this.setPopperOpen(true);

            this.$modal.show(
                Sale,
                {
                    edit_id: "",
                    options: {
                        id: "sales",
                        record_name: "Sale",
                    },
                    phones: [this.row.Id],
                    submitRecordSaved: (invoice_id) => {
                        this.setActiveTab(this.options.id);
                        this.setTabToRefresh(this.options.id);

                        //Open Print Invoice dialog
                        this.viewSalesInvoice(invoice_id);
                    },
                },
                {
                    width: "90%",
                    height: "80%",
                }
            );
        },

        ...mapActions({
            setTableMetaData: "datatable/setTableMetaData",
            setActiveTab: "local_settings/setActiveTab",
            setTabToRefresh: "framework/setTabToRefresh",
            refreshData: "framework/refreshData",
            setPopperOpen: "local_settings/setPopperOpen",
            addError: "errors/addError",
        }),
    },
};
</script>
