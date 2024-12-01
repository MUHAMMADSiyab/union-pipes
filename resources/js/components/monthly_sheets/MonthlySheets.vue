<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Monthly Sheets</h5>
            <v-data-table
                :headers="headers"
                :items="monthly_sheets"
                class="elevation-1"
                item-key="id"
                :search="search"
                :items-per-page="perPage"
                :loading="loading"
                :show-select="can('monthly_sheet_delete') && !printMode"
                loading-text="Loading monthly_sheets..."
                :footer-props="footerProps"
                v-model="selectedItems"
            >
                <!-- S# -->
                <template slot="item.sno" slot-scope="props">{{
                    props.index + 1
                }}</template>

                <!-- Month -->
                <template slot="item.month" slot-scope="props">
                    <span>
                        {{
                            new Date(props.item.month).toLocaleDateString(
                                "en-US",
                                { month: "long", year: "numeric" }
                            )
                        }}
                    </span>
                </template>
                <template slot="item.previous_month_total" slot-scope="props">
                    <strong>{{ previousMonthName(props.item.month) }}:</strong>
                    {{ money(props.item.previous_month_total) }}
                </template>
                <template slot="item.totals" slot-scope="props">
                    <strong>{{ money(props.item.totals) }}</strong>
                </template>

                <!-- Top -->
                <template v-slot:top v-if="!printMode">
                    <v-btn
                        color="error"
                        small
                        :disabled="!selectedItems.length"
                        class="ma-2 text-right"
                        @click="deleteMultiple"
                        v-if="can('monthly_sheet_delete')"
                        ><v-icon left>mdi-trash-can-outline</v-icon> Delete
                        Selected</v-btn
                    >

                    <v-btn
                        color="success"
                        small
                        link
                        to="/monthly_sheets/add"
                        class="ma-2 text-right"
                        v-if="can('monthly_sheet_create')"
                    >
                        <v-icon left>mdi-monthly_sheet-plus</v-icon>
                        New Monthly Sheet Entry</v-btn
                    >

                    <!-- <Excel
                        module="monthly_sheets"
                        :ids="selectedItems.map((item) => item.id)"
                    />
                    <CSV
                        module="monthly_sheets"
                        :ids="selectedItems.map((item) => item.id)"
                    />
                    <PDF
                        module="monthly_sheets"
                        :ids="selectedItems.map((item) => item.id)"
                    /> -->

                    <v-text-field
                        v-model="search"
                        placeholder="Search"
                        class="mx-4"
                        append-icon="mdi-magnify"
                    ></v-text-field>
                </template>

                <!-- Actions -->
                <template slot="item.actions" slot-scope="props">
                    <v-btn
                        x-small
                        text
                        color="indigo"
                        :to="`/monthly_sheets/${props.item.id}`"
                        title="Monthly Sheet Entries"
                        v-if="can('monthly_sheet_show')"
                    >
                        <v-icon small>mdi-format-list-checkbox</v-icon>
                    </v-btn>
                    <v-btn
                        x-small
                        text
                        color="primary"
                        :to="`/monthly_sheets/edit/${props.item.id}`"
                        title="Edit"
                        v-if="can('monthly_sheet_edit')"
                    >
                        <v-icon small>mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn
                        x-small
                        text
                        color="red darken-2"
                        @click="setMonthlySheetId(props.item.id)"
                        title="Delete"
                        v-if="can('monthly_sheet_delete')"
                    >
                        <v-icon small>mdi-delete</v-icon>
                    </v-btn>
                </template>
            </v-data-table>

            <!-- Confirmation -->
            <Confirmation
                ref="confirmationComponent"
                :id="monthlySheetId"
                @confirmDeletion="
                    monthlySheetId
                        ? handleMonthlySheetDelete()
                        : handleMultipleMonthlySheetsDelete()
                "
            />
        </v-container>
        <alert />
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DatatableMixin from "../../mixins/DatatableMixin";
import Navbar from "../navs/Navbar";
import Confirmation from "../globals/Confirmation";
import Excel from "../globals/exports/Excel.vue";
import CSV from "../globals/exports/CSV.vue";
import PDF from "../globals/exports/PDF.vue";
import CurrencyMixin from "../../mixins/CurrencyMixin";
import { props } from "qrcode.vue";

export default {
    mixins: [DatatableMixin, CurrencyMixin],

    components: {
        Navbar,
        Confirmation,
        Excel,
        CSV,
        PDF,
    },

    data() {
        return {
            headers: [
                { text: "S#", value: "sno" },
                { text: "Month", value: "month" },
                {
                    text: "Previous Month Total",
                    value: "previous_month_total",
                },
                {
                    text: "Total Profit/Loss for Current Month",
                    value: "totals",
                },
                { text: "Actions", value: "actions", align: " d-print-none" },
            ],
            selectedItems: [],

            monthlySheetId: null,
        };
    },

    methods: {
        ...mapActions({
            getMonthlySheets: "monthly_sheet/getMonthlySheets",
            getMonthlySheet: "monthly_sheet/getMonthlySheet",
            deleteMonthlySheet: "monthly_sheet/deleteMonthlySheet",
            deleteMultipleMonthlySheets:
                "monthly_sheet/deleteMultipleMonthlySheets",
        }),

        setMonthlySheetId(id) {
            this.monthlySheetId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleMonthlySheetDelete() {
            await this.deleteMonthlySheet(this.monthlySheetId);
            this.monthlySheetId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },

        async deleteMultiple() {
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleMultipleMonthlySheetsDelete() {
            const ids = this.selectedItems.map(
                (selectedItem) => selectedItem.id
            );
            await this.deleteMultipleMonthlySheets(ids);

            this.$refs.confirmationComponent.setDialog(false);
            this.selectedItems = [];
        },

        previousMonthName(currentMonth) {
            if (currentMonth) {
                const date = new Date(currentMonth);
                date.setMonth(date.getMonth() - 1);
                return date.toLocaleString("en-US", {
                    month: "long",
                    year: "numeric",
                });
            }
            return "Previous Month";
        },
    },

    computed: {
        ...mapGetters({
            monthly_sheets: "monthly_sheet/monthly_sheets",
            monthly_sheet: "monthly_sheet/monthly_sheet",
            loading: "loading",
        }),
    },

    mounted() {
        // remove actions if no access is given
        if (
            !this.can("monthly_sheet_edit") &&
            !this.can("monthly_sheet_delete")
        ) {
            this.headers = this.headers.filter(
                (header) => header.value !== "actions"
            );
        }

        this.getMonthlySheets();
    },
};
</script>
