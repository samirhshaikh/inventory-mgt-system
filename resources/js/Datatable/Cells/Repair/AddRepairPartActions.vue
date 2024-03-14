<template>
    <div class="flex">
        <Button
            @click.native="edit"
            class="text-white bg-green-600"
            :class="{
                hidden:
                    !page.user_details.IsAdmin ||
                    row['row_id'] == current_row_id,
            }"
        >
            Edit {{ current_row_id }}
        </Button>
        <Button
            @click.native="removeRecord"
            class="text-white bg-red-400 ml-2"
            :class="{
                hidden:
                    !page.user_details.IsAdmin ||
                    row['id'] == '' ||
                    row['row_id'] == current_row_id ||
                    row['Status'] == this.phonestock.STATUS_SOLD,
            }"
            split="border-white"
        >
            Delete
        </Button>
    </div>
</template>

<script>
import Confirm from "../../../components/Confirm.vue";
import { datatable_cell } from "../datatable_cell";
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
                        parent.$emit("removeRecord", this.row["row_id"]);
                    },
                    onConfirm() {
                        close();
                    },
                    onClosed() {},
                },
            });

            open();
        },
    },
};
</script>
