<template>
    <div class="flex">
        <Button
            @click.native="edit"
            class="text-white bg-green-600 mr-2"
            :class="{
                hidden:
                    !page.user_details.IsAdmin ||
                    row['row_id'] == current_row_id ||
                    row['Returned'],
            }"
        >
            Edit
        </Button>
        <Button
            @click.native="removeRecord"
            class="text-white bg-red-400 mr-2"
            :class="{
                hidden:
                    !page.user_details.IsAdmin ||
                    row['row_id'] == current_row_id,
            }"
            split="border-white"
        >
            Delete
        </Button>
        <Button
            @click.native="returnItem"
            class="text-white bg-red-400"
            :class="{
                hidden:
                    !page.user_details.IsAdmin ||
                    !row.hasOwnProperty('id') ||
                    row['Returned'],
            }"
            v-if="typeof row.SalesInvoiceId !== 'undefined'"
        >
            Return
        </Button>
        <span
            class="text-red-700 leading-normal"
            :class="{
                hidden: row['Returned'] == 0,
            }"
            >Returned</span
        >
    </div>
</template>

<script>
import Confirm from "../../../components/Confirm.vue";
import ReturnItem from "../../../DBObjects/ReturnItem";
import { datatable_cell } from "../datatable_cell";
import { mapActions } from "vuex";
import { usePage } from "@inertiajs/vue3";
import { useModal } from "vue-final-modal";

const page = usePage();

export default {
    mixins: [datatable_cell],

    props: {
        current_row_id: {
            type: String,
            default: "",
        },
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
        edit() {
            this.$emit("editRecord", this.row);
        },

        removeRecord() {
            const parent = this;

            const { open, close } = useModal({
                component: Confirm,
                attrs: {
                    title: "Delete " + this.options.record_name,
                    text:
                        "Are you sure you want to delete this " +
                        _.lowerCase(this.options.record_name) +
                        "?",
                    yes_handler: () => {
                        this.$emit("removeRecord", this.row["row_id"]);
                    },
                    onConfirm() {
                        close();
                    },
                    onClosed() {},
                },
            });

            open();
        },

        returnItem() {
            const { open, close } = useModal({
                component: ReturnItem,
                attrs: {
                    SalesInvoiceId: this.parent_row["id"],
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

        ...mapActions({
            refreshData: "framework/refreshData",
            addError: "errors/addError",
        }),
    },
};
</script>
