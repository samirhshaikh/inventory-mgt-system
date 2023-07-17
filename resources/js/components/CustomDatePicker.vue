<template>
    <section>
        <VueDatePicker
            :model-value="date_value"
            ref="datepicker"
            @update:model-value="dateSelected"
            :enableTimePicker="false"
            :preview-format="format"
            :format="format"
            :class="{
                required_field:
                    required_field && (date_value == '' || date_value == null),
            }"
        >
        </VueDatePicker>
    </section>
</template>

<script>
import { mapState } from "vuex";
import moment from "moment";

const monthNames = [
    "Jan",
    "Feb",
    "Mar",
    "Apr",
    "May",
    "Jun",
    "Jul",
    "Aug",
    "Sep",
    "Oct",
    "Nov",
    "Dec",
];

export default {
    name: "CustomDatePicker",

    props: {
        start_date_value: {
            type: String,
            default: "",
        },
        column_name: {
            type: String,
        },
        required_field: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            // attributes: [
            //     {
            //         key: "today",
            //         highlight: true,
            //         dates: new Date(),
            //     },
            // ],
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
        format(date) {
            const day = date.getDate();
            const month = monthNames[date.getMonth()];
            const year = date.getFullYear();

            return `${day}-${month}-${year}`;
        },

        clearDate() {
            this.date_value = "";

            this.$emit("clearDate");
        },

        dateSelected(date) {
            this.date_value = date;

            this.$emit("dateSelected", this.format(date), this.column_name);
        },
    },
};
</script>
