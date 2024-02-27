<template>
    <div class="flex">
        <Button
            @click.native="viewSalesInvoice(row.id)"
            icon="file-alt"
            split="border-white"
            class="text-white bg-green-600 mr-2"
            :class="{
                hidden: !page.user_details.IsAdmin,
            }"
        >
            Invoice
        </Button>
        <Button
            @click.native="edit"
            icon="pen"
            split="border-white"
            class="text-white bg-green-600 mr-2"
            :class="{
                hidden: !page.user_details.IsAdmin,
            }"
            >Edit</Button
        >
        <Button
            @click.native="remove"
            class="text-white bg-red-400 mr-2"
            :class="{
                hidden: !page.user_details.IsAdmin,
            }"
            :icon="deleting_record ? 'sync-alt' : 'trash'"
            :icon_class="deleting_record ? 'fa-spin' : ''"
            split="border-white"
        >
            {{ deleting_record ? "Deleting" : "Delete" }}
        </Button>
    </div>
</template>

<script>
import Sale from "../../../DBObjects/Sale.vue";
import { mapActions, mapState } from "vuex";
import Confirm from "../../../components/Confirm.vue";
import { datatable_cell } from "../datatable_cell";
import { notifications } from "../../../Helpers/notifications";
import { common_functions } from "../../../Helpers/common_functions";
import Invoice from "./Invoice";
import { usePage } from "@inertiajs/vue3";
import { useModal } from "vue-final-modal";

const page = usePage();

export default {
    mixins: [datatable_cell, notifications, common_functions],

    computed: {
        page() {
            return page.props;
        },
    },

    methods: {
        edit() {
            const parent = this;

            this.setPopperOpen(true);

            const { open, close } = useModal({
                component: Sale,
                attrs: {
                    edit_id: String(this.row.id),
                    options: this.options,
                    submitRecordSaved: (invoice_id) => {
                        this.setTableMetaData({
                            columns: this.columns,
                            options: this.options,
                        });

                        this.setActiveTab(this.options.id);
                        this.setTabToRefresh(this.options.id);

                        close();

                        //Open Print Invoice dialog
                        this.viewSalesInvoice(invoice_id);
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

        remove() {
            const { open, close } = useModal({
                component: Confirm,
                attrs: {
                    title: "Delete " + this.options.record_name,
                    text:
                        "Are you sure you want to delete this " +
                        _.lowerCase(this.options.record_name) +
                        "?",
                    yes_handler: () => {
                        this.deleting_record = true;

                        axios
                            .post(route("sale.delete"), {
                                id: this.row.id,
                            })
                            .then((response) => {
                                if (response.data.message == "record_deleted") {
                                    this.$notify({
                                        group: "messages",
                                        title: "Success",
                                        text: this.formatMessage(
                                            response.data.message,
                                            this.options.record_name
                                        ),
                                    });

                                    this.refreshData(this.options.id);
                                } else {
                                    this.$notify({
                                        group: "messages",
                                        title: "Error",
                                        type: "error",
                                        text: this.formatMessage(
                                            "unknown_error",
                                            this.options.record_name
                                        ),
                                    });
                                }

                                this.deleting_record = false;
                            })
                            .catch((error) => {
                                this.deleting_record = false;

                                this.$notify({
                                    group: "messages",
                                    title: "Error",
                                    type: "error",
                                    text: this.formatMessage(
                                        error.response.data.message,
                                        this.options.record_name
                                    ),
                                });
                            });
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
