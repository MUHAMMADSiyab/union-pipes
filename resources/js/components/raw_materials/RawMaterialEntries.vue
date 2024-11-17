<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-6 d-block" v-if="raw_material">
            <v-btn
                color="light"
                x-small
                class="mb-3 py-2 d-print-none"
                title="Back to Raw Materials"
                @click="$router.push({ name: 'raw_materials' })"
                ><v-icon small>mdi-arrow-left</v-icon></v-btn
            >
            <h5 class="text-subtitle-1 mb-2">
                Raw Material Entries for
                <strong>{{ month }}</strong>
            </h5>
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-text>
                            <v-simple-table dense bordered>
                                <thead>
                                    <tr>
                                        <th class="text-left">Quality</th>
                                        <th class="text-left">Bag</th>
                                        <th class="text-left">Kg</th>
                                        <th class="text-left">Weight</th>
                                        <th class="text-left">Rate</th>
                                        <th class="text-left">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(
                                            entry, index
                                        ) in raw_material.entries"
                                        :key="index"
                                    >
                                        <td>{{ entry.quality }}</td>
                                        <td>{{ money(entry.bag) }}</td>
                                        <td>{{ money(entry.kg) }}</td>
                                        <td>{{ money(entry.weight) }}</td>
                                        <td>{{ money(entry.rate) }}</td>
                                        <td>{{ money(entry.amount) }}</td>
                                    </tr>
                                    <tr class="font-weight-bold">
                                        <td colspan="5" class="text-center">
                                            Total Amount
                                        </td>
                                        <td>
                                            {{
                                                money(
                                                    raw_material.entries_sum_amount
                                                )
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </v-simple-table>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import CurrencyMixin from "../../mixins/CurrencyMixin";
import Navbar from "../navs/Navbar";

export default {
    mixins: [CurrencyMixin],

    components: {
        Navbar,
    },

    methods: {
        ...mapActions({
            getRawMaterial: "raw_material/getRawMaterial",
        }),
    },

    computed: {
        ...mapGetters({
            raw_material: "raw_material/raw_material",
            loading: "loading",
        }),

        month() {
            return new Date(this.raw_material.month).toLocaleDateString(
                "en-US",
                {
                    month: "long",
                    year: "numeric",
                }
            );
        },
    },

    async mounted() {
        this.getRawMaterial(parseInt(this.$route.params.id));
    },
};
</script>
