<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">
                Purchased Items Report
                <span v-if="data.from_date && data.to_date"
                    >from {{ formatDate(data.from_date) }} to
                    {{ formatDate(data.to_date) }}</span
                >
            </h5>

            <v-row>
                <v-col cols="12">
                    <v-card
                        :loading="formLoading"
                        :disabled="formLoading"
                        v-if="!printMode"
                    >
                        <v-card-subtitle
                            >Generate purchase report between date
                            range</v-card-subtitle
                        >

                        <v-card-text class="mt-3">
                            <v-form @submit.prevent="generate">
                                <v-row>
                                    <v-col cols="6">
                                        <v-switch
                                            v-model="allCompanies"
                                            label="All Companies"
                                        ></v-switch>
                                    </v-col>
                                </v-row>

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
                                                validation.getMessage(
                                                    'from_date'
                                                )
                                            "
                                        ></small>
                                        <v-menu
                                            max-width="290px"
                                            min-width="auto"
                                        >
                                            <template v-slot:activator="{ on }">
                                                <v-text-field
                                                    v-model="data.from_date"
                                                    v-on="on"
                                                    label="From Date"
                                                    prepend-inner-icon="mdi-calendar"
                                                    dense
                                                    outlined
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                v-model="data.from_date"
                                                label="From Date"
                                                no-title
                                                outlined
                                                dense
                                                show-current
                                            ></v-date-picker>
                                        </v-menu>
                                    </v-col>

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
                                                validation.getMessage('to_date')
                                            "
                                        ></small>
                                        <v-menu
                                            max-width="290px"
                                            min-width="auto"
                                        >
                                            <template v-slot:activator="{ on }">
                                                <v-text-field
                                                    v-model="data.to_date"
                                                    v-on="on"
                                                    label="To Date"
                                                    prepend-inner-icon="mdi-calendar"
                                                    dense
                                                    outlined
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                v-model="data.to_date"
                                                label="To Date"
                                                no-title
                                                outlined
                                                dense
                                                show-current
                                            ></v-date-picker>
                                        </v-menu>
                                    </v-col>

                                    <v-col
                                        xl="5"
                                        lg="5"
                                        md="5"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage(
                                                    'companies'
                                                )
                                            "
                                        ></small>
                                        <v-select
                                            v-model="data.companies"
                                            :items="companies"
                                            item-value="id"
                                            item-text="name"
                                            :menu-props="{ maxHeight: '400' }"
                                            label="Select Suppliers"
                                            multiple
                                            clearable
                                            dense
                                            outlined
                                        >
                                        </v-select>
                                    </v-col>

                                    <v-col
                                        xl="1"
                                        lg="1"
                                        md="1"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <v-btn color="primary" type="submit"
                                            ><v-icon>mdi-magnify</v-icon></v-btn
                                        >
                                    </v-col>
                                </v-row>
                            </v-form>
                        </v-card-text>
                    </v-card>

                    <PurchasedItemsReport
                        v-if="
                            reportData &&
                            reportData.data &&
                            reportData.data.length
                        "
                        :purchased-items="reportData.data"
                        :totals="reportData.totals"
                    />

                    <h4
                        class="ml-3 mt-3"
                        v-if="
                            requestProcessed &&
                            (!reportData || !reportData.data.length)
                        "
                    >
                        No records
                    </h4>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../../mixins/ValidationMixin";
import Navbar from "../../navs/Navbar";
import PurchasedItemsReport from "./PurchasedItemsReport.vue";

export default {
    mixins: [ValidationMixin],

    components: {
        Navbar,
        PurchasedItemsReport,
    },

    data() {
        return {
            formLoading: false,
            requestProcessed: false,
            allCompanies: false,
            data: {
                from_date: "",
                to_date: "",
                companies: [],
            },
        };
    },

    methods: {
        ...mapActions({
            getCompanies: "company/getCompanies",
            getPurchasedItemsReportData: "report/getPurchasedItemsReportData",
        }),

        remove(item, field) {
            const index = this.data[field].findIndex((pm) => pm === item);

            this.data[field].splice(index, 1);
            this.data[field] = [...this.data[field]];
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            const options = {
                year: "numeric",
                month: "long",
                day: "numeric",
            };
            return date.toLocaleString("en-US", options);
        },

        async generate() {
            this.formLoading = true;

            await this.getPurchasedItemsReportData(this.data);

            this.formLoading = false;
            this.requestProcessed = true;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                // Clear the validation messages object
                this.validation.setMessages({});
            }
        },
    },

    computed: {
        ...mapGetters({
            companies: "company/companies",
            reportData: "report/reportData",
            validationErrors: "validationErrors",
        }),
    },

    watch: {
        allCompanies: {
            handler(newVal) {
                if (newVal) {
                    this.data.companies = [
                        ...this.companies.map((company) => company.id),
                    ];
                } else {
                    this.data.companies = [];
                }
            },
        },
    },

    mounted() {
        this.getCompanies();
    },
};
</script>
