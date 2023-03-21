<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Expense Sources</h5>

            <v-row>
                <v-col
                    xl="4"
                    lg="4"
                    md="4"
                    sm="12"
                    cols="12"
                    v-if="!printMode && can('expense_create')"
                >
                    <AddExpenseSource />
                </v-col>

                <v-col
                    v-bind="getPrintClass(8, can('expense_create'))"
                    cols="12"
                >
                    <ExpenseSources />
                </v-col>
            </v-row>

            <alert />
        </v-container>
    </div>
</template>

<script>
import Navbar from "../navs/Navbar";
import AddExpenseSource from "./AddExpenseSource.vue";
import ExpenseSources from "./ExpenseSources.vue";

export default {
    components: {
        Navbar,
        AddExpenseSource,
        ExpenseSources,
    },
};
</script>
