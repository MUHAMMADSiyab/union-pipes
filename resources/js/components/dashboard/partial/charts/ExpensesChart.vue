<template>
    <v-card class="mt-3">
        <v-card-title>
            <h6 class="text-uppercase grey--text">
                Expenses during Last 6 Months
            </h6>
        </v-card-title>

        <v-card-text>
            <apexchart
                height="350"
                :options="options"
                :series="series"
            ></apexchart>
        </v-card-text>
    </v-card>
</template>

<script>
import _ from "lodash";

export default {
    props: ["sixMonthExpenses"],

    data() {
        return {
            series: [
                {
                    data: this.sixMonthExpenses.map((expense) => expense.total),
                },
            ],
            options: {
                chart: {
                    type: "bar",
                    height: 350,
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: false,
                            zoom: false,
                            zoomin: false,
                            zoomout: false,
                            pan: false,
                            reset: false,
                            customIcons: [],
                        },
                    },
                },

                fill: {
                    type: "gradient",
                    gradient: {
                        shade: "light",
                        type: "horizontal",
                        shadeIntensity: 0.5,
                        gradientToColors: ["purple", "indigo"], // optional, if not defined - uses the shades of same color in series
                        // inverseColors: true,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 50, 100],
                        colorStops: [],
                    },
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: true,
                    },
                },
                dataLabels: {
                    enabled: false,
                },
                xaxis: {
                    categories: this.sixMonthExpenses.map(
                        (expense) => expense.month
                    ),
                },
            },
        };
    },
};
</script>
