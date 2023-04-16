<template>
    <v-simple-table dense class="mt-3">
        <template v-slot:default>
            <thead>
                <tr>
                    <th>S#</th>
                    <th>Machine</th>
                    <th>Total Production (Weight)</th>
                    <th>Average Production (Weight)</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="(machine_data, i) in data" :key="machine_data.id">
                    <td>{{ i + 1 }}</td>
                    <td>{{ machine_data.name }}</td>
                    <td>
                        {{ money(machine_data.productions_sum_total_weight) }}
                    </td>
                    <td>
                        {{ money(machine_data.productions_avg_total_weight) }}
                    </td>
                </tr>
            </tbody>
        </template>
    </v-simple-table>
</template>

<script>
import CurrencyMixin from "../../../mixins/CurrencyMixin";
export default {
    props: ["data"],

    mixins: [CurrencyMixin],
};
</script>
