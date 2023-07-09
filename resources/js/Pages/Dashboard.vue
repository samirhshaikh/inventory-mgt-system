<template>
    <Layout>
        <div class="px-4 py-4">
            <div
                class="flex items-stretch datatable_header"
                :class="{
                    'border-product-color-lighter bg-white': !dark_mode,
                    'border-product-color bg-gray-800': dark_mode,
                }"
            >
                <h1
                    class="pt-1 ml-2 text-product-color text-2xl tracking-tight w-full"
                >
                    <FA :icon="['fas', 'home']" class="mr-1"></FA>
                    Dashboard
                </h1>
                <div class="mr-2 flex flex-row">
                    <Button
                        @click.native="getData"
                        icon="redo-alt"
                        :icon_class="loading ? 'fa-spin' : ''"
                        split="border-white"
                        class="text-white bg-green-600 float-right"
                    >
                        Refresh
                    </Button>
                </div>
            </div>

            <div
                class="flex h-full p-8"
                :class="{
                    'bg-gray-700 text-white': dark_mode,
                }"
                v-if="!loading"
            >
                <div class="flex flex-row justify-between w-full">
                    <div class="w-1/2" style="height: 400px">
                        <BarChart
                            :data="stockChartData"
                            :options="chartOptions"
                            class="w-1/2"
                        />
                    </div>

                    <div class="w-1/2" style="height: 400px">
                        <DoughnutChart
                            :data="costData"
                            :options="chartOptions"
                            class="w-1/2"
                        />
                    </div>
                </div>
            </div>

            <Loading v-else />
        </div>
    </Layout>
</template>

<script>
import { mapState } from "vuex";
import { usePage } from "@inertiajs/vue3";

const page = usePage();

export default {
    props: [],

    data() {
        return {
            loading: false,
            error: false,

            stockChartData: {},
            costData: {},
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "bottom",
                    },
                    title: {
                        display: true,
                        text: "Inventory",
                    },
                },
            },
        };
    },

    created() {
        this.getData();
    },

    computed: {
        ...mapState({
            dark_mode: (state) => state.framework.dark_mode,
            expanded_sidebar: (state) => state.framework.expanded_sidebar,
            local_settings: (state) => state.local_settings,
        }),
    },

    methods: {
        getData() {
            this.loading = true;

            axios.get(route("dashboard.get-stats")).then(
                (response) => {
                    this.stockChartData = {
                        labels: Object.keys(
                            response.data.response.inventory.stock
                        ),
                        datasets: [
                            {
                                label: "Stock",
                                data: _.map(
                                    response.data.response.inventory.stock,
                                    function (item) {
                                        return item;
                                    }
                                ),
                                backgroundColor: [
                                    "rgb(253, 64, 105)",
                                    "rgb(54, 162, 235)",
                                    "rgb(34, 207, 207)",
                                    "rgb(253, 206, 96)",
                                ],
                            },
                        ],
                    };

                    this.costData = {
                        labels: Object.keys(
                            response.data.response.inventory.cost
                        ),
                        datasets: [
                            {
                                label: "Stock Value",
                                data: _.map(
                                    response.data.response.inventory.cost,
                                    function (item) {
                                        return item;
                                        // return new Intl.NumberFormat('en-IN', {maximumSignificantDigits: 3, maximumFractionDigits: 2}).format(item);
                                    }
                                ),
                                backgroundColor: [
                                    "rgb(253, 64, 105)",
                                    "rgb(54, 162, 235)",
                                    "rgb(34, 207, 207)",
                                    "rgb(253, 206, 96)",
                                ],
                            },
                        ],
                    };

                    this.loading = false;
                },
                (error) => {
                    this.error = true;
                    this.loading = false;
                }
            );
        },
    },
};
</script>
