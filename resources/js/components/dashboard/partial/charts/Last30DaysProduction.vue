<template>
    <v-card>
        <v-card-text
            ><apexchart
                type="bar"
                :options="chartOptions"
                :series="chartSeries"
        /></v-card-text>
    </v-card>
</template>

<script>
import VueApexCharts from "vue-apexcharts";

export default {
    components: {
        apexchart: VueApexCharts,
    },
    props: {
        chartData: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            chartSeries: [],
            chartOptions: {
                chart: {
                    toolbar: { show: false },
                },
                colors: ["#008FFB", "#00E396", "#FF4560"],
                dataLabels: {
                    enabled: false,
                },
                stroke: {
                    curve: "smooth",
                },
                title: {
                    text: "Last 30 Days Production by Machines",
                    align: "left",
                },
                xaxis: {
                    categories: this.chartData.days,
                    labels: {
                        show: true,
                        rotate: -45,
                        rotateAlways: false,
                        hideOverlappingLabels: true,
                        showDuplicates: false,
                        trim: true,
                        minHeight: undefined,
                        maxHeight: 120,
                    },
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return new Intl.NumberFormat("en-US").format(value);
                        },
                    },
                    title: {
                        text: "Total Weight",
                    },
                },
                tooltip: {
                    enabled: true,
                    x: {
                        show: true,
                        format: "dd-MMM",
                    },
                },
            },
        };
    },

    computed: {
        height() {
            return this.chartData.productionByMachine.length * 100;
        },
    },

    mounted() {
        this.chartSeries = this.chartData.productionByMachine.map((machine) => {
            const data = Object.entries(machine.data).map(([key, value]) => {
                return {
                    x: key,
                    y: parseFloat(value) || 0,
                };
            });

            return {
                name: machine.name,
                data,
            };
        });
    },
};
</script>
