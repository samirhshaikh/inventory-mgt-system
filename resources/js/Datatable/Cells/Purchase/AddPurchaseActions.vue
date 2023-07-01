<template>
    <div class="flex">
        <Button
            @click.native="edit"
            class="text-white bg-green-600"
            :class="{
                hidden:
                    !$page.user_details.IsAdmin ||
                    row['row_id'] == current_row_id,
            }"
        >
            Edit
        </Button>
        <Button
            @click.native="removeRecord"
            class="text-white bg-red-400 ml-2"
            :class="{
                hidden:
                    !$page.user_details.IsAdmin ||
                    row['Id'] == '' ||
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

export default {
    mixins: [datatable_cell],

    props: {
        current_row_id: {
            type: String,
            default: "",
        },
    },

    methods: {
        edit() {
            this.$emit("editRecord", this.row);
        },

        removeRecord() {
            this.$modal.show(
                Confirm,
                {
                    title: "Delete " + this.options.record_name,
                    text:
                        "Are you sure you want to delete this " +
                        _.lowerCase(this.options.record_name) +
                        "?",
                    yes_handler: () => {
                        this.$emit("removeRecord", this.row["row_id"]);
                    },
                },
                {
                    width: "350px",
                    height: "auto",
                }
            );
        },
    },
};
</script>
