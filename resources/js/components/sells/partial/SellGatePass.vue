<template>
    <v-card class="mt-2" v-if="gate_pass || sell_gate_pass">
        <v-card-title primary-title>
            <h6 class="text-uppercase">Sell Gate Pass</h6>
        </v-card-title>
        <v-card-text>
            <v-simple-table dense>
                <tbody>
                    <tr>
                        <td>Receiver</td>
                        <td>
                            {{
                                isSellGatePass
                                    ? sell_gate_pass.receiver
                                    : gate_pass.receiver
                            }}
                        </td>
                    </tr>
                    <tr>
                        <td>Vehicle No</td>
                        <td>
                            {{
                                isSellGatePass
                                    ? sell_gate_pass.vehicle_no
                                    : gate_pass.vehicle_no
                            }}
                        </td>
                    </tr>
                    <tr>
                        <td>Driver Name</td>
                        <td>
                            {{
                                isSellGatePass
                                    ? sell_gate_pass.driver_name
                                    : gate_pass.driver_name
                            }}
                        </td>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td>
                            {{
                                isSellGatePass
                                    ? sell_gate_pass.date
                                    : gate_pass.date
                            }}
                        </td>
                    </tr>
                    <tr>
                        <td>In Time</td>
                        <td>
                            {{
                                isSellGatePass
                                    ? sell_gate_pass.in_time
                                    : gate_pass.in_time
                            }}
                        </td>
                    </tr>
                    <tr>
                        <td>Out Time</td>
                        <td>
                            {{
                                isSellGatePass
                                    ? sell_gate_pass.out_time
                                    : gate_pass.out_time
                            }}
                        </td>
                    </tr>
                    <tr>
                        <td>Items</td>
                        <td>{{ itemsJoined }}</td>
                    </tr>
                </tbody>
            </v-simple-table>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions v-if="isSellGatePass">
            <v-btn color="secondary" right @click="closeDialog">Close</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
    props: ["sellId", "isSellGatePass"],

    methods: {
        ...mapActions({
            getSellGatePass: "gate_pass/getSellGatePass",
            getGatePass: "gate_pass/getGatePass",
        }),

        closeDialog() {
            this.$emit("closeDialog");
        },

        mappedItems(items) {
            return items
                .map(
                    (item) =>
                        `${item.particular}: ${item.quantity} (${item.remarks})`
                )
                .join(" | ");
        },
    },

    computed: {
        ...mapGetters({
            sell_gate_pass: "gate_pass/sell_gate_pass",
            gate_pass: "gate_pass/gate_pass",
        }),
        itemsJoined() {
            if (this.isSellGatePass) {
                return this.mappedItems(this.sell_gate_pass.items);
            }

            return this.mappedItems(this.gate_pass.items);
        },
    },

    watch: {
        sellId: {
            handler(newVal) {
                if (this.isSellGatePass) {
                    this.getSellGatePass(newVal);
                } else {
                    const gatePassId = window.location.search.split("=")[1];
                    if (gatePassId) {
                        this.getGatePass(gatePassId);
                    }
                }
            },
            deep: true,
        },
    },

    mounted() {
        if (this.isSellGatePass) {
            this.getSellGatePass(this.sellId);
        } else {
            const gatePassId = window.location.search.split("=")[1];
            if (gatePassId) {
                this.getGatePass(gatePassId);
            }
        }
    },
};
</script>
