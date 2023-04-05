<template>
    <v-card>
        <v-card-subtitle>Last 12 Months Sells</v-card-subtitle>
        <v-card-text>
            <apexchart
                type="area"
                :options="chartOptions"
                :series="chartSeries"
                height="400"
            />
        </v-card-text>
    </v-card>
</template>

<script>
import VueApexCharts from "vue-apexcharts";

export default {
    components: {
        apexchart: VueApexCharts,
    },
    props: {
        sellData: {
            type: Object,
            required: true,
        },
    },
    computed: {
        chartOptions() {
            return {
                chart: {
                    type: "area",
                    height: 400,
                    toolbar: {
                        show: false,
                    },
                },
                dataLabels: {
                    enabled: false,
                },
                stroke: {
                    curve: "smooth",
                },
                xaxis: {
                    categories: Object.keys(this.sellData),
                    labels: {
                        style: {
                            fontSize: "12px",
                        },
                    },
                },
                yaxis: {
                    title: {
                        text: "Total Amount",
                    },
                    labels: {
                        style: {
                            fontSize: "12px",
                        },
                    },
                },
                colors: ["#a700ef"],
                fill: {
                    type: "gradient",
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.9,
                        stops: [0, 90, 100],
                    },
                },
                tooltip: {
                    x: {
                        show: true,
                        format: "MMM yyyy",
                    },
                },
            };
        },
        chartSeries() {
            return [
                {
                    name: "Total Amount",
                    data: Object.values(this.sellData),
                },
            ];
        },
    },
};
</script>
