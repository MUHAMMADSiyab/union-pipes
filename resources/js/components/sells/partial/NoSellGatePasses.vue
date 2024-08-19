<template>
    <v-card
        class="mb-2"
        style="border: 2px solid red"
        v-if="no_sell_gate_passes.length"
    >
        <v-card-subtitle>
            <v-icon class="red--text accent-3 float-right"
                >mdi-shield-alert</v-icon
            >
            <v-icon>mdi-receipt</v-icon>
            <strong> Gate passes having no sell entry </strong>
        </v-card-subtitle>

        <v-card-text>
            <v-simple-table>
                <template v-slot:default>
                    <thead>
                        <tr>
                            <th class="text-left">S#</th>
                            <th class="text-left">Date</th>
                            <th class="text-left">Receiver</th>
                            <th class="text-left">Vehicle No.</th>
                            <th class="text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(item, index) in no_sell_gate_passes"
                            :key="item.id"
                        >
                            <td>{{ index + 1 }}</td>
                            <td>{{ item.date }}</td>
                            <td>{{ item.receiver }}</td>
                            <td>{{ item.vehicle_no }}</td>
                            <td>
                                <v-btn
                                    color="light"
                                    small
                                    link
                                    :to="`/sells/add?gate_pass_id=${item.id}`"
                                    v-if="can('sell_create')"
                                >
                                    Add Sell</v-btn
                                >
                            </td>
                        </tr>
                    </tbody>
                </template>
            </v-simple-table>
        </v-card-text>
    </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
export default {
    methods: {
        ...mapActions({
            getNoSellGatePasses: "gate_pass/getNoSellGatePasses",
        }),
    },

    computed: {
        ...mapGetters({
            no_sell_gate_passes: "gate_pass/no_sell_gate_passes",
        }),
    },

    mounted() {
        this.getNoSellGatePasses();
    },
};
</script>
