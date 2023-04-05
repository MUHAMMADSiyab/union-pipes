<template>
    <div class="mt-2">
        <v-data-table
            :headers="headers"
            :items="sells"
            class="elevation-1"
            item-key="id"
            :search="search"
            :items-per-page="perPage"
            :loading="loading"
            loading-text="Loading Report Data..."
            :footer-props="footerProps"
            dense
        >
            <!-- S# -->
            <template slot="item.sno" slot-scope="props">{{
                props.index + 1
            }}</template>

            <!-- Items -->
            <template slot="item.sold_items" slot-scope="props">
                <ul>
                    <li
                        class="my-2"
                        v-for="(item, i) in props.item.sold_items"
                        :key="i"
                    >
                        {{ item.product_name }}
                        <span class="text-right float-right">{{
                            money(item.grand_total)
                        }}</span>
                    </li>
                </ul>
            </template>

            <!-- Amounts -->
            <template slot="item.overall_grand_total" slot-scope="props">
                <span>
                    {{ money(props.item.overall_grand_total) }}

                    <small class="d-block purple--text"
                        >( {{ money(props.item.total_amount) }} before
                        discount)</small
                    >
                </span>
            </template>
            <template slot="item.paid" slot-scope="props">
                <span> {{ money(props.item.paid) }} </span>
            </template>
            <template slot="item.balance" slot-scope="props">
                <span class="font-weight-bold">
                    {{ money(props.item.balance) }}
                </span>
            </template>
        </v-data-table>
    </div>
</template>

<script>
import DatatableMixin from "../../../mixins/DatatableMixin";
import CurrencyMixin from "../../../mixins/CurrencyMixin";
import { mapGetters } from "vuex";

export default {
    mixins: [DatatableMixin, CurrencyMixin],

    props: ["sells"],

    data() {
        return {
            headers: [
                { text: "S#", value: "sno" },
                { text: "Customer", value: "customer_name" },
                { text: "Items", value: "sold_items" },
                { text: "Total", value: "overall_grand_total" },
                { text: "Paid", value: "paid" },
                { text: "Balance", value: "balance" },
            ],
        };
    },

    computed: {
        ...mapGetters({
            loading: "loading",
        }),
    },
};
</script>
