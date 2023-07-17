<template>
    <Bar :data="data" :options="options" />
</template>

<script>
import { Bar } from "vue-chartjs";

import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
} from "chart.js";
ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
);

export default {
    name: "BarChart",

    components: { Bar },

    props: {
        data: {
            type: Object,
            default: null,
            required: true,
        },
        options: {
            type: Object,
            default: () => {},
        },
        width: {
            type: String,
            default: "w-1/2",
        },
    },
};
</script>
