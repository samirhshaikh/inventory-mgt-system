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
            menu-class-name="dp-custom-menu"
            :range="range"
        >
        </VueDatePicker>
    </section>
</template>

<style>
.dp__input {
    @apply font-sans
    bg-gray-200
    text-gray-700
    border
    border-gray-400
    leading-tight
    rounded;
}
.dp__input {
    padding: 8px 0px 8px 35px;
    font-size: 14px;
}
.dp-custom-menu {
    @apply font-sans;
}
.dp-custom-menu {
    font-size: 15px;
}
</style>

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
        end_date_value: {
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
        range: {
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
            date_value: this.range ? "" + "," + "" : "",
        };
    },

    computed: {
        ...mapState({
            dark_mode: (state) => state.framework.dark_mode,
        }),
    },

    mounted() {
        this.date_value = this.range
            ? this.start_date_value + "," + this.end_date_value ?? ""
            : this.start_date_value;
    },

    methods: {
        format(date) {
            if (this.range) {
                return (
                    this.formatDate(date[0]) + "," + this.formatDate(date[1])
                );
            } else {
                return this.formatDate(date);
            }
        },

        formatDate(date) {
            if (typeof date === "undefined" || date === null) {
                return "";
            }

            const day = date.getDate();
            const month = monthNames[date.getMonth()];
            const year = date.getFullYear();

            return `${day}-${month}-${year}`;
        },

        clearDate() {
            this.date_value = this.range ? ["", ""] : "";

            this.$emit("clearDate");
        },

        dateSelected(date) {
            this.date_value = date;

            this.$emit("dateSelected", this.format(date), this.column_name);
        },
    },
};
</script>
