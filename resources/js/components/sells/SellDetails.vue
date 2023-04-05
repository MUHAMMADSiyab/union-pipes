<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4" v-if="sell">
            <h5 class="text-subtitle-1 mb-2">Sell Details</h5>

            <v-row>
                <v-col cols="12">
                    <v-row>
                        <!-- Sell / Payment Details -->
                        <v-col cols="12">
                            <SellDetails :sell="sell" />
                        </v-col>

                        <!-- Sold Items Details -->
                        <v-col cols="12">
                            <SoldItems :sold-items="sell.sold_items" />
                            <ReturnedItems
                                :returned-items="sell.returned_items"
                            />
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import CurrencyMixin from "../../mixins/CurrencyMixin";
import Navbar from "../navs/Navbar";
import SellDetails from "./partial/SellDetails.vue";
import SoldItems from "./partial/SoldItems.vue";
import ReturnedItems from "./partial/ReturnedItems.vue";

export default {
    mixins: [CurrencyMixin],

    components: {
        Navbar,
        SellDetails,
        SoldItems,
        ReturnedItems,
    },

    methods: {
        ...mapActions({
            getSell: "sell/getSell",
        }),
    },

    computed: {
        ...mapGetters({
            sell: "sell/sell",
            loading: "loading",
        }),
    },

    async mounted() {
        this.getSell(parseInt(this.$route.params.id));
    },
};
</script>
