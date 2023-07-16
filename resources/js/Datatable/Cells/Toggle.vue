<template>
    <div class="z-0 flex items-center">
        <input
            type="checkbox"
            class="toggle"
            :checked="data"
            @change="toggleValue"
            :disabled="!page.user_details.IsAdmin"
        /><span class="label-text ml-1" v-if="labelled">{{
            data ? checkedLabel : uncheckedLabel
        }}</span>
    </div>
</template>

<script>
import { mapActions } from "vuex";
import { datatable_cell } from "./datatable_cell";
import { notifications } from "../../Helpers/notifications";
import { usePage } from "@inertiajs/vue3";

const page = usePage();

const defaultLabels = {
    checked: "Yes",
    unchecked: "No",
};

export default {
    mixins: [datatable_cell, notifications],

    props: {
        labelled: {
            type: Boolean,
            default: true,
        },
        labels: {
            type: Object,
            default: () => defaultLabels,
        },
    },

    computed: {
        page() {
            return page.props;
        },

        data() {
            return _.get(this.row, this.column, false);
        },

        checkedLabel() {
            return Object.keys(this.labels).indexOf("checked") >= 0
                ? this.labels.checked
                : "Yes";
        },

        uncheckedLabel() {
            return Object.keys(this.labels).indexOf("unchecked") >= 0
                ? this.labels.unchecked
                : "No";
        },
    },

    methods: {
        toggleValue() {
            _.set(this.row, this.column, !_.get(this.row, this.column, false));

            if (this.column_details["route"] != "") {
                axios
                    .post(route(this.column_details["route"]), {
                        Id: _.get(this.row, this.options.primary_key),
                        value: _.get(this.row, this.column, false) ? 1 : 0,
                    })
                    .then((response) => {
                        if (response.data.message == "status_changed") {
                            // this.$notify({
                            //     group: "messages",
                            //     title: "Success",
                            //     text: this.formatMessage(
                            //         response.data.message,
                            //         this.options.record_name
                            //     ),
                            // });

                            //Reset the cache
                            if (
                                this.options.hasOwnProperty("cache_data") &&
                                this.options.cache_data
                            ) {
                                this.resetCachedData(this.options.id);
                            }
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                        // if (error.response.data.message == "record_not_found") {
                        //     this.$notify({
                        //         group: "messages",
                        //         title: "Error",
                        //         type: "error",
                        //         text: this.formatMessage(
                        //             error.response.data.message,
                        //             this.options.record_name
                        //         ),
                        //     });
                        // }
                    });
            }
        },

        ...mapActions({
            resetCachedData: "local_settings/resetCachedData",
            addError: "errors/addError",
        }),
    },
};
</script>
