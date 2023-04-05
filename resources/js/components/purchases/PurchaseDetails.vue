<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4" v-if="purchase">
            <h5 class="text-subtitle-1 mb-2">Purchase Details</h5>

            <v-row>
                <v-col cols="12">
                    <v-row>
                        <!-- Purchase / Payment Details -->
                        <v-col cols="12">
                            <PurchaseDetails :purchase="purchase" />
                        </v-col>

                        <!-- Purchased Items Details -->
                        <v-col cols="12">
                            <PurchasedItems
                                :purchased-items="purchase.purchased_items"
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
import PurchaseDetails from "./partial/PurchaseDetails.vue";
import PurchasedItems from "./partial/PurchasedItems.vue";

export default {
    mixins: [CurrencyMixin],

    components: {
        Navbar,
        PurchaseDetails,
        PurchasedItems,
    },

    methods: {
        ...mapActions({
            getPurchase: "purchase/getPurchase",
        }),
    },

    computed: {
        ...mapGetters({
            purchase: "purchase/purchase",
            loading: "loading",
        }),
    },

    async mounted() {
        this.getPurchase(parseInt(this.$route.params.id));
    },
};
</script>
