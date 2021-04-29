<template>
    <div class="z-0">
        <toggle-button
            :value="data"
            :labels="{ checked: 'Yes', unchecked: 'No' }"
            @change="toggleValue(row)"
            :disabled="!isAdmin"
        />
    </div>
</template>

<script>
import { mapActions } from "vuex";
import {datatable_cell} from "./datatable_cell";
import {notifications} from "../../Helpers/notifications";

export default {
    mixins: [datatable_cell, notifications],

    computed: {
        data() {
            return _.get(this.row, this.column, false);
        },
    },

    methods: {
        toggleValue(row) {
            _.set(this.row, this.column, !_.get(this.row, this.column, false));

            if (this.column_details["route"] != "") {
                axios
                    .post(route(this.column_details["route"]), {
                        Id: _.get(this.row, this.options.primary_key),
                        value: _.get(this.row, this.column, false) ? 1 : 0
                    })
                    .then(response => {
                        if (response.data.message == "status_changed") {
                            this.$notify({
                                group: "messages",
                                title: "Success",
                                text: this.formatMessage(response.data.message, this.options.record_name)
                            });

                            //Reset the cache
                            if (
                                this.options.hasOwnProperty("cache_data") &&
                                this.options.cache_data
                            ) {
                                this.resetCachedData(this.options.id);
                            }
                        }
                    })
                    .catch(error => {
                        this.deleting_record = false;

                        if (error.response.data.message == "record_not_found") {
                            this.$notify({
                                group: "messages",
                                title: "Error",
                                type: "error",
                                text: this.formatMessage(error.response.data.message, this.options.record_name)
                            });
                        }
                    });
            }
        },

        ...mapActions({
            resetCachedData: "local_settings/resetCachedData",
            addError: "errors/addError"
        })
    }
};
</script>
