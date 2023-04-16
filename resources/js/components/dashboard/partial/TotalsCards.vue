<template>
    <v-row>
        <v-col xl="3" lg="3" md="3" sm="12" cols="12">
            <v-card>
                <v-card-text>
                    <Card
                        color="light-green"
                        title="Materials Purchased"
                        :number="totals.materials_purchased"
                        icon="mdi-archive-arrow-down-outline"
                    />
                </v-card-text>
            </v-card>
        </v-col>

        <v-col xl="3" lg="3" md="3" sm="12" cols="12">
            <v-card>
                <v-card-text>
                    <Card
                        color="indigo"
                        title="Total Pipe Sold"
                        :number="totals.pipe_sold"
                        icon="mdi-archive-arrow-up-outline"
                    />
                </v-card-text>
            </v-card>
        </v-col>

        <v-col xl="3" lg="3" md="3" sm="12" cols="12">
            <v-card>
                <v-card-text>
                    <Card
                        color="pink"
                        title="Total Stock Weight"
                        :number="totals.stock_weight_available"
                        icon="mdi-warehouse"
                    />
                </v-card-text>
            </v-card>
        </v-col>

        <v-col xl="3" lg="3" md="3" sm="12" cols="12">
            <v-card>
                <v-card-text>
                    <Card
                        color="orange"
                        title="Total Expenses"
                        :number="totals.total_expenses"
                        icon="mdi-currency-eur"
                    />
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import Card from "./Card.vue";

export default {
    props: ["totals"],

    components: { Card },
};
</script>
