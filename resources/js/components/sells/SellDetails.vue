<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container
            class="mt-6 d-block pa-0 full-width-invoice"
            fluid
            v-if="sell"
        >
            <h3 class="text-center text-decoration-underline mb-3">
                Sell Invoice
            </h3>
            <v-row>
                <v-col cols="12">
                    <v-card class="elevation-0 full-width-card">
                        <v-card-text id="watermark-container">
                            <div id="watermark" class="d-none d-print-block">
                                {{ app_setting?.app_name || "PipeSync" }}
                            </div>

                            <table class="text--center">
                                <tr>
                                    <td
                                        width="50%"
                                        style="
                                            border-left: 0 !important;
                                            border-bottom: 1px solid gray !important;
                                        "
                                    >
                                        Bill No.
                                        <ins
                                            ><strong>{{
                                                sell.invoice_no
                                            }}</strong></ins
                                        >
                                    </td>
                                    <td
                                        width="50%"
                                        align="right"
                                        style="
                                            border-left: 0 !important;
                                            border-bottom: 1px solid gray !important;
                                        "
                                    >
                                        Date:
                                        <ins
                                            ><strong>{{
                                                sell.date
                                            }}</strong></ins
                                        >
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        width="100%"
                                        colspan="2"
                                        style="
                                            border-left: 0 !important;
                                            border-bottom: 1px solid gray !important;
                                        "
                                    >
                                        To:
                                        <ins
                                            ><strong>{{
                                                sell.customer.name
                                            }}</strong></ins
                                        >
                                    </td>
                                </tr>
                            </table>

                            <table
                                cellspacing="0"
                                id="items-table"
                                class="text-center"
                            >
                                <tr>
                                    <th
                                        style="
                                            border-bottom: 2px solid gray !important;
                                        "
                                    >
                                        Particulars
                                    </th>
                                    <th
                                        style="
                                            border-bottom: 2px solid gray !important;
                                        "
                                    >
                                        Rate
                                    </th>

                                    <th
                                        style="
                                            border-bottom: 2px solid gray !important;
                                        "
                                    >
                                        Quantity
                                    </th>
                                    <th
                                        style="
                                            border-bottom: 2px solid gray !important;
                                        "
                                    >
                                        Amount
                                    </th>
                                </tr>

                                <tr
                                    v-for="item in sell.sold_items"
                                    :key="item.id"
                                    id="sold-items-row"
                                >
                                    <td width="50%" align="left">
                                        <span class="pl-2">{{
                                            item.product.product_full_name
                                        }}</span>
                                    </td>
                                    <td>{{ money(item.rate) }}</td>
                                    <td>{{ money(item.quantity) }}</td>
                                    <td>{{ money(item.total) }}</td>
                                </tr>
                                <tr height="250">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr id="footer-total">
                                    <th
                                        class="text-center"
                                        style="
                                            border-top: 2px solid gray !important;
                                        "
                                    ></th>
                                    <th
                                        style="
                                            border-top: 2px solid gray !important;
                                        "
                                    >
                                        Total
                                    </th>

                                    <th
                                        style="
                                            border-top: 2px solid gray !important;
                                        "
                                    >
                                        {{ money(totalQuantitySum) }}
                                    </th>
                                    <th
                                        style="
                                            border-top: 2px solid gray !important;
                                        "
                                    >
                                        {{ money(totalAmountSum) }}
                                    </th>
                                </tr>
                                <tr>
                                    <th
                                        style="
                                            border-top: 2px solid gray !important;
                                        "
                                    >
                                        <em
                                            >Amount after discount of
                                            {{ sell.discount }}% ({{
                                                money(sell.discount_amount)
                                            }})</em
                                        >
                                    </th>
                                    <th
                                        class="indigo--text"
                                        colspan="3"
                                        style="
                                            border-top: 2px solid gray !important;
                                        "
                                    >
                                        {{
                                            money(sell.discounted_total_amount)
                                        }}
                                    </th>
                                </tr>
                            </table>

                            <table style="margin-top: 80px" width="100%">
                                <tr>
                                    <td
                                        style="border-left: 0 !important"
                                        align="right"
                                    >
                                        Authorized Signature
                                        ___________________________
                                    </td>
                                </tr>
                            </table>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <v-row class="d-print-none">
                <v-col cols="12">
                    <ReturnedItems :returned-items="sell.returned_items" />
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import CurrencyMixin from "../../mixins/CurrencyMixin";
import Navbar from "../navs/Navbar";
import ReturnedItems from "./partial/ReturnedItems";

export default {
    mixins: [CurrencyMixin],

    components: {
        Navbar,
        ReturnedItems,
    },

    methods: {
        ...mapActions({
            getSell: "sell/getSell",
            getAppSetting: "setting/getAppSetting",
        }),
    },

    computed: {
        ...mapGetters({
            sell: "sell/sell",
            app_setting: "setting/app_setting",
            loading: "loading",
        }),

        totalQuantitySum() {
            return this.sell.sold_items.reduce(
                (acc, cur) => acc + parseInt(cur.quantity),
                0
            );
        },

        totalAmountSum() {
            return this.sell.sold_items.reduce(
                (acc, cur) => acc + parseInt(cur.total),
                0
            );
        },
    },

    async mounted() {
        this.getAppSetting();
        this.getSell(parseInt(this.$route.params.id));
    },
};
</script>

<style scoped>
@media only print {
    .v-card {
        box-shadow: none !important;
        border: 0 !important;
    }
}

#items-table {
    margin-top: 40px;
    border: 2px solid gray;
}

td,
th {
    border-bottom: 0 !important;
    border-left: 2px solid gray;
}
#watermark-container {
    position: relative;
}
#watermark {
    width: fit-content;
    height: 120px;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    margin: auto;
    font-size: 4rem;
    rotate: -45deg;
    text-align: center;
    opacity: 0.2;
}

.full-width-invoice {
    padding: 0 !important;
    max-width: 100% !important;
}

.full-width-card {
    width: 100% !important;
    box-shadow: none !important;
    border: none !important;
}

/* Optional: table full width and print-friendliness */
#items-table,
#items-table th,
#items-table td {
    width: 100%;
    table-layout: fixed;
    word-wrap: break-word;
}

/* Improve alignment for invoice tables */
table {
    width: 100%;
    border-collapse: collapse;
}

td,
th {
    padding: 8px;
    text-align: center;
}

@media only print {
    .v-card,
    .full-width-card {
        box-shadow: none !important;
        border: none !important;
    }

    .v-container,
    .full-width-invoice {
        max-width: 100% !important;
        padding: 0 !important;
    }

    table {
        page-break-inside: avoid;
    }
}
</style>
