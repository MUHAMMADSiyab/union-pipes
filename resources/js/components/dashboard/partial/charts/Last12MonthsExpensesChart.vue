<template>
    <v-card>
        <v-card-text>
            <apexchart
                type="bar"
                height="350"
                :options="chartOptions"
                :series="chartSeries"
            ></apexchart>
        </v-card-text>
    </v-card>
</template>

<script>
import VueApexCharts from "vue-apexcharts";

export default {
    components: {
        apexchart: VueApexCharts,
    },

    props: ["expenses", "months"],

    computed: {
        chartOptions() {
            return {
                chart: {
                    toolbar: {
                        show: false,
                    },
                },
                title: {
                    text: "Last 12 Months Expenses",
                    align: "left",
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: "55%",
                        endingShape: "rounded",
                    },
                },
                dataLabels: {
                    enabled: false,
                },
                xaxis: {
                    categories: this.months,
                    labels: {
                        style: {
                            fontSize: "12px",
                        },
                    },
                },
                yaxis: {
                    title: {
                        text: "Expense",
                    },
                    labels: {
                        style: {
                            fontSize: "12px",
                        },
                    },
                },
                fill: {
                    gradient: {
                        shade: "light",
                        type: "horizontal",
                        shadeIntensity: 0.5,
                        gradientToColors: ["#f44336", "#ffa726", "#1e88e5"],
                        inverseColors: true,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 70, 100],
                    },
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return "" + val;
                        },
                    },
                },
            };
        },
        chartSeries() {
            return this.expenses.map((expense) => {
                return {
                    name: expense.name,
                    data: Object.values(expense.totals),
                };
            });
        },
    },
};
</script>
