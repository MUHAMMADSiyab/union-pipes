<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">
                Closing Report
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
                            >Generate closing report between date
                            range</v-card-subtitle
                        >

                        <v-card-text class="mt-3">
                            <v-form @submit.prevent="generate">
                                <v-row>
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
                                        xl="2"
                                        lg="2"
                                        md="2"
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

                    <ClosingReport v-if="requestProcessed" :data="reportData" />
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../../mixins/ValidationMixin";
import Navbar from "../../navs/Navbar";
import ClosingReport from "./ClosingReport.vue";

export default {
    mixins: [ValidationMixin],

    components: {
        Navbar,
        ClosingReport,
    },

    data() {
        return {
            formLoading: false,
            requestProcessed: false,
            data: {
                from_date: "",
                to_date: "",
            },
        };
    },

    methods: {
        ...mapActions({
            getClosingReportData: "report/getClosingReportData",
        }),

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

            await this.getClosingReportData(this.data);

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
            reportData: "report/reportData",
            validationErrors: "validationErrors",
        }),
    },
};
</script>
