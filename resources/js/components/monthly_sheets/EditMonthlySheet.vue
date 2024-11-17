<template>
    <div>
        <Navbar v-if="!printMode" />
        <v-container class="mt-4">
            <v-row>
                <v-col cols="12">
                    <v-card :loading="formLoading" :disabled="formLoading">
                        <v-card-title primary-title
                            >Edit Monthly Profit/Loss Entry</v-card-title
                        >
                        <v-card-subtitle
                            >Edit Monthly Profit/Loss Entries</v-card-subtitle
                        >
                        <v-card-text class="mt-2">
                            <v-form @submit.prevent="update">
                                <v-row class="mb-3">
                                    <v-col
                                        xl="3"
                                        lg="3"
                                        md="3"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage('month')
                                            "
                                        ></small>
                                        <v-menu
                                            max-width="290px"
                                            min-width="auto"
                                        >
                                            <template v-slot:activator="{ on }">
                                                <v-text-field
                                                    v-model="data.month"
                                                    v-on="on"
                                                    label="Month"
                                                    prepend-inner-icon="mdi-calendar"
                                                    dense
                                                    filled
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                v-model="data.month"
                                                label="Month"
                                                type="month"
                                                no-title
                                                outlined
                                                dense
                                                show-current
                                            ></v-date-picker>
                                        </v-menu>
                                    </v-col>
                                </v-row>

                                <!-- Total Pipe, Raw Material & Assets Section -->
                                <h3>Total Pipe, Raw Material & Assets</h3>
                                <div
                                    v-for="(entry, index) in data.assets"
                                    :key="`asset_${index}`"
                                    class="d-flex align-center mb-2 mt-4"
                                >
                                    <v-text-field
                                        v-model="entry.description"
                                        label="Description"
                                        small
                                        class="mr-2"
                                    />
                                    <v-text-field
                                        v-model="entry.amount"
                                        label="Amount"
                                        type="number"
                                        small
                                        class="mr-2"
                                    />
                                    <v-btn
                                        icon
                                        @click.prevent="removeAsset(index)"
                                    >
                                        <v-icon>mdi-minus-circle</v-icon>
                                    </v-btn>
                                </div>
                                <v-btn
                                    color="green white--text mb-6"
                                    small
                                    @click.prevent="addAsset"
                                >
                                    <v-icon>mdi-plus</v-icon> Add asset
                                </v-btn>

                                <!-- Total for Assets -->
                                <div class="totals-row">
                                    Total Amount of Assets, Non-Assets & Market
                                    <span class="float-right">{{
                                        totalAssets
                                    }}</span>
                                </div>

                                <!-- Payable Amount Section -->
                                <h3>Payable Amount</h3>
                                <div
                                    v-for="(entry, index) in data.payables"
                                    :key="`payable_${index}`"
                                    class="d-flex align-center mb-2 mt-4"
                                >
                                    <v-text-field
                                        v-model="entry.description"
                                        label="Description"
                                        small
                                        class="mr-2"
                                    />
                                    <v-text-field
                                        v-model="entry.amount"
                                        label="Amount"
                                        type="number"
                                        small
                                        class="mr-2"
                                    />
                                    <v-btn
                                        icon
                                        @click.prevent="removePayable(index)"
                                    >
                                        <v-icon>mdi-minus-circle</v-icon>
                                    </v-btn>
                                </div>
                                <v-btn
                                    color="green white--text mb-6"
                                    small
                                    @click.prevent="addPayable"
                                >
                                    <v-icon>mdi-plus</v-icon> Add payable amount
                                </v-btn>

                                <!-- Total for Payables -->
                                <div class="totals-row">
                                    Total Payable Amount
                                    <span class="float-right">{{
                                        money(totalPayables)
                                    }}</span>
                                </div>

                                <!-- Total Amount Calculation -->

                                <div class="totals-row">
                                    {{ currentMonth }} Total
                                    <span class="float-right">{{
                                        money(totalAssets - totalPayables)
                                    }}</span>
                                </div>

                                <!-- Previous Month Total Input -->
                                <div class="mt-4 mb-2">
                                    <v-text-field
                                        v-model.number="data.previousMonthTotal"
                                        :label="`${previousMonthName} Total`"
                                        type="number"
                                        hint="Previous Month Total Amount"
                                    />
                                </div>

                                <div class="totals-row">
                                    Total Profit/Loss
                                    <span class="float-right">{{
                                        money(
                                            totalAssets -
                                                totalPayables -
                                                data.previousMonthTotal
                                        )
                                    }}</span>
                                </div>

                                <v-btn color="primary" type="submit"
                                    >Save</v-btn
                                >
                            </v-form>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
            <alert />
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../mixins/ValidationMixin";
import Navbar from "../navs/Navbar";
import CurrencyMixin from "../../mixins/CurrencyMixin";

export default {
    mixins: [ValidationMixin, CurrencyMixin],
    components: { Navbar },
    data() {
        return {
            formLoading: false,
            data: {
                previousMonthTotal: 0,
                month: "",
                assets: [{ description: "", amount: 0 }],
                payables: [{ description: "", amount: 0 }],
            },
        };
    },
    computed: {
        ...mapGetters({
            validationErrors: "validationErrors",
            monthly_sheet: "monthly_sheet/monthly_sheet",
        }),
        totalAssets() {
            return this.data.assets.reduce(
                (total, asset) => total + Number(asset.amount),
                0
            );
        },
        totalPayables() {
            return this.data.payables.reduce(
                (total, payable) => total + Number(payable.amount),
                0
            );
        },

        currentMonth() {
            if (this.data.month) {
                return new Date(
                    this.data.month.concat("-01")
                ).toLocaleDateString("en-US", {
                    month: "long",
                    year: "numeric",
                });
            }
            return "";
        },

        previousMonthName() {
            if (this.data.month) {
                const date = new Date(this.data.month.concat("-01"));
                date.setMonth(date.getMonth() - 1);
                return date.toLocaleString("en-US", {
                    month: "long",
                    year: "numeric",
                });
            }
            return "Previous Month";
        },
    },
    methods: {
        ...mapActions({
            getMonthlySheet: "monthly_sheet/getMonthlySheet",
            updateMonthlySheet: "monthly_sheet/updateMonthlySheet",
        }),
        async update() {
            this.formLoading = true;
            await this.updateMonthlySheet(this.data);
            this.formLoading = false;
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                this.validation.setMessages({});
            }
        },
        addAsset() {
            this.data.assets.push({ description: "", amount: 0 });
        },
        removeAsset(index) {
            this.data.assets.splice(index, 1);
        },
        addPayable() {
            this.data.payables.push({ description: "", amount: 0 });
        },
        removePayable(index) {
            this.data.payables.splice(index, 1);
        },
    },

    async mounted() {
        await this.getMonthlySheet(this.$route.params.id);

        if (!this.monthly_sheet) {
            return this.$router.push({ name: "not_found" });
        }

        this.data.id = this.monthly_sheet.id;
        this.data.month = this.monthly_sheet.month.slice(0, 7);
        this.data.previousMonthTotal = this.monthly_sheet.previous_month_total;
        this.data.assets = this.monthly_sheet.entries
            .filter((entry) => entry.category === "asset")
            .map(({ description, amount }) => ({ description, amount }));
        this.data.payables = this.monthly_sheet.entries
            .filter((entry) => entry.category === "payable")
            .map(({ description, amount }) => ({ description, amount }));
    },
};
</script>

<style scoped>
.totals-row {
    background: #d6edff;
    padding: 8px;
    color: #003a66;
    font-weight: bold;
    margin-bottom: 30px;
    margin-top: 10px;
}
</style>
