<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4">
            <v-row>
                <v-col cols="12">
                    <v-card :loading="formLoading" :disabled="formLoading">
                        <v-card-title primary-title
                            >Edit Stock Sheet</v-card-title
                        >
                        <v-card-subtitle
                            >Edit Stock Sheet Entries</v-card-subtitle
                        >

                        <v-card-text class="mt-2">
                            <v-form @submit.prevent="update">
                                <v-row>
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

                                <div
                                    v-for="(entry, index) in data.entries"
                                    :key="index"
                                    class="d-flex align-center mb-2 mt-4"
                                >
                                    <v-text-field
                                        v-model="entry.product"
                                        label="Product"
                                        small
                                        class="mr-2"
                                    />
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `entries.${index}.product`
                                            )
                                        "
                                    ></small>

                                    <v-text-field
                                        v-model="entry.quantity"
                                        label="Quantity"
                                        type="number"
                                        small
                                        class="mr-2"
                                    />
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `entries.${index}.quantity`
                                            )
                                        "
                                    ></small>

                                    <v-text-field
                                        v-model="entry.weight"
                                        label="Weight"
                                        type="number"
                                        small
                                        class="mr-2"
                                    />
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `entries.${index}.weight`
                                            )
                                        "
                                    ></small>

                                    <v-text-field
                                        v-model="entry.total_weight"
                                        label="Total Weight"
                                        type="number"
                                        small
                                        class="mr-2"
                                    />
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `entries.${index}.total_weight`
                                            )
                                        "
                                    ></small>

                                    <v-text-field
                                        v-model="entry.rate"
                                        label="Rate"
                                        type="number"
                                        small
                                        class="mr-2"
                                    />
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `entries.${index}.rate`
                                            )
                                        "
                                    ></small>

                                    <v-text-field
                                        v-model="entry.total_amount"
                                        label="Total Amount"
                                        type="number"
                                        small
                                    />
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `entries.${index}.total_amount`
                                            )
                                        "
                                    ></small>

                                    <v-btn
                                        icon
                                        @click.prevent="removeEntry(index)"
                                    >
                                        <v-icon>mdi-minus-circle</v-icon>
                                    </v-btn>
                                </div>

                                <v-btn
                                    color="green white--text"
                                    @click.prevent="addEntry"
                                    ><v-icon>mdi-plus</v-icon> Add Entry</v-btn
                                >
                                <v-btn
                                    color="primary"
                                    type="submit"
                                    class="float-end"
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

export default {
    mixins: [ValidationMixin],

    components: { Navbar },

    data() {
        return {
            formLoading: false,
            data: {
                id: null,
                month: "",
                entries: [
                    {
                        product: "",
                        quantity: 0,
                        weight: 0,
                        rate: 0,
                        total_weight: 0,
                        total_amount: 0,
                    },
                ],
            },
        };
    },

    methods: {
        ...mapActions({
            getStockSheet: "stock_sheet/getStockSheet",
            updateStockSheet: "stock_sheet/updateStockSheet",
        }),

        async update() {
            this.formLoading = true;

            await this.updateStockSheet(this.data);

            this.formLoading = false;

            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                this.validation.setMessages({});

                return this.$router.push({ name: "stock_sheets" });
            }
        },

        addEntry() {
            this.data.entries.push({
                product: "",
                quantity: 0,
                weight: 0,
                rate: 0,
                total_weight: 0,
                total_amount: 0,
            });
        },

        removeEntry(index) {
            this.data.entries.splice(index, 1);
        },
    },

    computed: {
        ...mapGetters({
            validationErrors: "validationErrors",
            stock_sheet: "stock_sheet/stock_sheet",
        }),
    },

    watch: {
        "data.entries": {
            handler(updatedEntries) {
                updatedEntries.forEach((entry) => {
                    const total_weight = entry.quantity * entry.weight;
                    const total_amount = total_weight * entry.rate;
                    entry.total_weight = total_weight;
                    entry.total_amount = total_amount;
                });
            },
            deep: true,
        },
    },

    async mounted() {
        await this.getStockSheet(this.$route.params.id);

        if (!this.stock_sheet) {
            return this.$router.push({ name: "not_found" });
        }

        this.data.id = this.stock_sheet.id;
        this.data.month = this.stock_sheet.month.slice(0, 7);
        this.data.entries = this.stock_sheet.entries;
    },
};
</script>
