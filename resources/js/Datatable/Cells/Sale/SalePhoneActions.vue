<template>
    <div class="flex items-center">
        <Button
            @click.native="returnItem"
            class="text-white bg-red-400 mr-2"
            :class="{
                hidden: !page.user_details.IsAdmin || row['Returned'] == 1,
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
                    !page.user_details.IsAdmin ||
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
import { usePage } from "@inertiajs/vue3";
import { useModal } from "vue-final-modal";

const page = usePage();

export default {
    mixins: [datatable_cell, notifications],

    props: {
        parent_row: {
            default: () => ({}),
        },
    },

    computed: {
        page() {
            return page.props;
        },
    },

    methods: {
        returnItem() {
            const { open, close } = useModal({
                component: ReturnItem,
                attrs: {
                    SalesInvoiceId: this.parent_row["Id"],
                    SalesInvoiceNo: this.parent_row["InvoiceNo"],
                    IMEI: this.row["IMEI"],
                    refresh: (IMEI) => {
                        this.$emit("returnItem", IMEI);
                    },
                    onConfirm() {
                        close();
                    },
                    onClosed() {},
                },
            });

            open();
        },

        tradeInDetails() {
            const parent = this;

            this.setPopperOpen(true);

            const { open, close } = useModal({
                component: Purchase,
                attrs: {
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
                        tradeIn: this.row,
                    },
                    onConfirm() {
                        close();
                    },
                    onClosed() {
                        parent.setPopperOpen(false);
                    },
                },
            });

            open();
        },

        ...mapActions({
            refreshData: "framework/refreshData",
            setPopperOpen: "local_settings/setPopperOpen",
            addError: "errors/addError",
        }),
    },
};
</script>
