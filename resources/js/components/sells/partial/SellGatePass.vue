<template>
    <v-card class="mt-2">
        <v-card-title primary-title>
            <h6 class="text-uppercase">Sell Gate Pass</h6>
        </v-card-title>
        <v-card-text>
            <v-simple-table>
                <tbody>
                    <tr>
                        <td>Receiver</td>
                        <td>{{ sell_gate_pass.receiver }}</td>
                    </tr>
                    <tr>
                        <td>Vehicle No</td>
                        <td>{{ sell_gate_pass.vehicle_no }}</td>
                    </tr>
                    <tr>
                        <td>Driver Name</td>
                        <td>{{ sell_gate_pass.driver_name }}</td>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td>{{ sell_gate_pass.date }}</td>
                    </tr>
                    <tr>
                        <td>In Time</td>
                        <td>{{ sell_gate_pass.in_time }}</td>
                    </tr>
                    <tr>
                        <td>Out Time</td>
                        <td>{{ sell_gate_pass.out_time }}</td>
                    </tr>
                    <tr>
                        <td>Items</td>
                        <td>{{ itemsJoined }}</td>
                    </tr>
                </tbody>
            </v-simple-table>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
            <v-btn color="secondary" right @click="closeDialog">Close</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
    props: ["sellId"],

    methods: {
        ...mapActions({
            getSellGatePass: "gate_pass/getSellGatePass",
        }),

        closeDialog() {
            this.$emit("closeDialog");
        },
    },

    computed: {
        ...mapGetters({
            sell_gate_pass: "gate_pass/sell_gate_pass",
        }),
        itemsJoined() {
            return this.sell_gate_pass.items
                .map(
                    (item) =>
                        `${item.particular}: ${item.quantity} (${item.remarks})`
                )
                .join(" | ");
        },
    },

    watch: {
        entry: {
            handler(newVal) {
                this.getSellGatePass(newVal);
            },
            deep: true,
        },
    },

    mounted() {
        this.getSellGatePass(this.sellId);
    },
};
</script>
