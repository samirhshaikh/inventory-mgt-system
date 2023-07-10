<template>
    <section>
        <date-picker
            v-model.trim="date_value"
            :masks="masks"
            :attributes="attributes"
            :is-dark="!dark_mode"
            @input="dateSelected"
        >
            <template v-slot="{ inputValue, inputEvents }">
                <div class="flex flex-row">
                    <input
                        class="w-32 date_input"
                        autocomplete="off"
                        :value="inputValue"
                        v-on="inputEvents"
                        :class="{
                            required_field:
                                required_field &&
                                (date_value == '' || date_value == null),
                        }"
                    />
                    <button
                        class="rounded-r-md p-2 border border-gray-400 bg-blue-200/50 border-l-0 hover:bg-red-200"
                        @click.stop="clearDate"
                    >
                        <FA :icon="['fas', 'times']"></FA>
                    </button>
                </div>
            </template>
        </date-picker>
    </section>
</template>

<script>
import { mapState } from "vuex";
import moment from "moment";

export default {
    name: "CustomDatePicker",

    props: {
        start_date_value: {
            type: String,
            default: "",
        },
        required_field: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            masks: {
                input: "DD-MMM-YYYY",
            },
            attributes: [
                {
                    key: "today",
                    highlight: true,
                    dates: new Date(),
                },
            ],
            date_value: "",
        };
    },

    computed: {
        ...mapState({
            dark_mode: (state) => state.framework.dark_mode,
        }),
    },

    mounted() {
        this.date_value = this.start_date_value;
    },

    methods: {
        clearDate() {
            this.date_value = "";

            this.$emit("clearDate");
        },

        dateSelected(date) {
            if (date != "" && date != null) {
                this.date_value = moment(date).format("D-MMM-YYYY");
            } else {
                this.date_value = "";
            }

            this.$emit("dateSelected", this.date_value);
        },
    },
};
</script>
