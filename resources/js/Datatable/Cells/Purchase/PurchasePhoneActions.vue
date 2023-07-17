<template>
    <div class="flex">
        <Button
            @click.native="sellItem"
            class="text-white bg-green-600"
            :class="{
                hidden:
                    !page.user_details.IsAdmin ||
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
import { usePage } from "@inertiajs/vue3";
import { useModal } from "vue-final-modal";

const page = usePage();

export default {
    mixins: [datatable_cell, common_functions],

    computed: {
        page() {
            return page.props;
        },
    },

    methods: {
        sellItem() {
            const parent = this;

            this.setPopperOpen(true);

            const { open, close } = useModal({
                component: Sale,
                attrs: {
                    edit_id: "",
                    options: {
                        id: "sales",
                        record_name: "Sale",
                    },
                    phones: [this.row.Id],
                    submitRecordSaved: (invoice_id) => {
                        parent.setActiveTab(this.options.id);
                        parent.setTabToRefresh(this.options.id);

                        //Open Print Invoice dialog
                        parent.viewSalesInvoice(invoice_id);
                    },
                    onConfirm() {
                        close();
                    },
                    onClosed() {},
                },
            });

            open();
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
