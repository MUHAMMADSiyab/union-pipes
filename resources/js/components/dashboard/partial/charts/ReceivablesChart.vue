<template>
    <v-card>
        <v-card-text>
            <apexchart
                type="donut"
                :height="350"
                :options="chartOptions"
                :series="chartSeries"
            ></apexchart>
        </v-card-text>
    </v-card>
</template>

<script>
import VueApexCharts from "vue-apexcharts";

export default {
    name: "ReceivablesPieChart",
    components: {
        apexchart: VueApexCharts,
    },
    props: {
        receivables: {
            type: Array,
            required: true,
        },
    },
    computed: {
        chartSeries() {
            return this.receivables.map((receivable) => receivable.balance);
        },
        chartOptions() {
            return {
                chart: {
                    animations: {
                        enabled: true,
                        easing: "easeinout",
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150,
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350,
                        },
                    },
                },
                title: {
                    text: "Receivables Chart",
                    align: "left",
                },
                labels: this.receivables.map((receivable) => receivable.name),
                colors: [
                    "#008FFB",
                    "#00E396",
                    "#FEB019",
                    "#FF4560",
                    "#775DD0",
                    "#00e803",
                    "#ff4447",
                ],
                legend: {
                    show: true,
                    position: "bottom",
                },
                dataLabels: {
                    enabled: true,
                    align: "center",
                    formatter: function (val, opts) {
                        return `${opts.w.globals.series[opts.seriesIndex]}`;
                    },
                    dropShadow: {
                        enabled: true,
                        color: "#000",
                        top: 1,
                        left: 1,
                        blur: 1,
                    },
                },
                tooltip: {
                    enabled: true,
                    y: {
                        formatter: function (val) {
                            return `${val}`;
                        },
                    },
                },
                responsive: [
                    {
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: "100%",
                            },
                            legend: {
                                position: "bottom",
                            },
                        },
                    },
                ],
            };
        },
    },
};
</script>

<style scoped>
.apexcharts-tooltip {
    font-size: 16px;
}
</style>
