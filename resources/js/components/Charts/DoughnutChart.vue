<template>
    <Doughnut :data="data" :options="options" />
</template>

<script>
import { Doughnut } from "vue-chartjs";
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from "chart.js";

ChartJS.register(ArcElement, Tooltip, Legend);

export default {
    name: "DoughnutChart",

    components: { Doughnut },

    props: {
        data: {
            type: Object,
            default: null,
        },
        options: {
            type: Object,
            default: null,
        },
        width: {
            type: String,
            default: "w-1/2",
        },
    },
};
</script>
