<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Expenses</h5>

            <v-row>
                <v-col cols="12">
                    <v-data-table
                        :headers="displayedHeaders"
                        :items="expenses"
                        class="elevation-1"
                        item-key="id"
                        :search="search"
                        :items-per-page="perPage"
                        :loading="loading"
                        :show-select="can('expense_delete') && !printMode"
                        loading-text="Loading expenses..."
                        :footer-props="footerProps"
                        v-model="selectedItems"
                        dense
                    >
                        <!-- S# -->
                        <template slot="item.sno" slot-scope="props">{{
                            props.index + 1
                        }}</template>

                        <!-- Amount -->
                        <template slot="item.amount" slot-scope="props">
                            <span> {{ money(props.item.amount) }} </span>
                        </template>

                        <!-- Cheque Columns Toggle -->
                        <template
                            v-if="showChequeColumns"
                            slot="item.payment.cheque_type"
                            slot-scope="props"
                            >{{ props.item.payment.cheque_type }}</template
                        >
                        <template
                            v-if="showChequeColumns"
                            slot="item.payment.cheque_no"
                            slot-scope="props"
                            >{{ props.item.payment.cheque_no }}</template
                        >
                        <template
                            v-if="showChequeColumns"
                            slot="item.payment.cheque_due_date"
                            slot-scope="props"
                            >{{ props.item.payment.cheque_due_date }}</template
                        >

                        <!-- Cheque Images (Button) -->
                        <template
                            slot="item.payment.cheque_images"
                            slot-scope="props"
                            v-if="
                                props.item.payment.cheque_images.length &&
                                showChequeColumns
                            "
                        >
                            <v-btn
                                color="light"
                                x-small
                                title="Cheque Image(s)"
                                @click="
                                    setCurrentChequeImages(
                                        props.item.payment.cheque_images
                                    )
                                "
                                ><v-icon>mdi-file-image-outline</v-icon></v-btn
                            >
                        </template>

                        <!-- Description -->
                        <template
                            slot="item.description"
                            slot-scope="props"
                            v-if="props.item.description"
                        >
                            <v-tooltip bottom>
                                <template v-slot:activator="{ on, attrs }">
                                    <span v-bind="attrs" v-on="on">
                                        {{
                                            props.item.description &&
                                            props.item.description.slice(0, 35)
                                        }}...
                                    </span>
                                </template>
                                <span>{{ props.item.description }}</span>
                            </v-tooltip>
                        </template>

                        <!-- Top -->
                        <template v-slot:top v-if="!printMode">
                            <v-btn
                                color="blue-grey darken-3"
                                small
                                class="ma-2 text-right white--text"
                                @click="toggleChequeColumns"
                                v-if="can('expense_delete')"
                                ><v-icon left>
                                    {{
                                        showChequeColumns
                                            ? "mdi-eye-off"
                                            : "mdi-eye"
                                    }}
                                </v-icon>
                                {{ showChequeColumns ? "Hide" : "Show" }} Cheque
                                Columns</v-btn
                            >

                            <v-btn
                                color="error"
                                small
                                :disabled="!selectedItems.length"
                                class="ma-2 text-right"
                                @click="deleteMultiple"
                                v-if="can('expense_delete')"
                                ><v-icon left>mdi-trash-can-outline</v-icon>
                                Delete Selected</v-btn
                            >

                            <v-btn
                                color="success"
                                small
                                link
                                to="/expenses/add"
                                class="ma-2 text-right"
                                v-if="can('expense_create')"
                            >
                                <v-icon left>mdi-account-plus-outline</v-icon>
                                New Expense</v-btn
                            >

                            <Excel
                                module="expenses"
                                :ids="selectedItems.map((item) => item.id)"
                            />
                            <CSV
                                module="expenses"
                                :ids="selectedItems.map((item) => item.id)"
                            />
                            <PDF
                                module="expenses"
                                :ids="selectedItems.map((item) => item.id)"
                            />

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
                                color="primary"
                                :to="`/expenses/edit/${props.item.id}`"
                                title="Edit"
                                v-if="can('expense_edit')"
                            >
                                <v-icon small>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn
                                x-small
                                text
                                color="red darken-2"
                                @click="setExpenseId(props.item.id)"
                                title="Delete"
                                v-if="can('expense_delete')"
                            >
                                <v-icon small>mdi-delete</v-icon>
                            </v-btn>
                        </template>
                    </v-data-table>

                    <!-- Cheque Images -->
                    <v-dialog v-model="chequeImagesDialog" width="600">
                        <ChequeImages
                            :current-cheque-images="currentChequeImages"
                            @closeDialog="closeChequeImagesDialog"
                        />
                    </v-dialog>

                    <!-- Confirmation -->
                    <Confirmation
                        ref="confirmationComponent"
                        :id="expenseId"
                        @confirmDeletion="
                            expenseId
                                ? handleExpenseDelete()
                                : handleMultipleExpensesDelete()
                        "
                    />
                </v-col>
            </v-row>

            <alert />
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DatatableMixin from "../../mixins/DatatableMixin";
import EditExpense from "./EditExpense.vue";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";
import ChequeImages from "../globals/ChequeImages.vue";
import Excel from "../globals/exports/Excel.vue";
import CSV from "../globals/exports/CSV.vue";
import PDF from "../globals/exports/PDF.vue";
import CurrencyMixin from "../../mixins/CurrencyMixin";

export default {
    mixins: [DatatableMixin, CurrencyMixin],

    components: {
        EditExpense,
        Navbar,
        Confirmation,
        ChequeImages,
        Excel,
        CSV,
        PDF,
    },

    data() {
        return {
            showChequeColumns: false,
            currentChequeImages: null,
            chequeImagesDialog: false,
            headers: [
                { text: "S#", value: "sno", show: true },
                { text: "Source", value: "expense_source.name", show: true },
                { text: "Name", value: "name", show: true },
                { text: "Amount", value: "amount", show: true },
                {
                    text: "Payment Method",
                    value: "payment.payment_method",
                    show: true,
                },
                { text: "Date", value: "payment.payment_date", show: true },
                { text: "Bank", value: "payment.bank.name", show: true },
                {
                    text: "Cheque Type",
                    value: "payment.cheque_type",
                    show: false,
                },
                { text: "Cheque No.", value: "payment.cheque_no", show: false },
                {
                    text: "Cheque Due Date",
                    value: "payment.cheque_due_date",
                    show: false,
                },
                {
                    text: "Cheque Images",
                    value: "payment.cheque_images",
                    show: false,
                },
                { text: "Description", value: "description", show: true },
                { text: "Actions", value: "actions", align: " d-print-none" },
            ],
            selectedItems: [],
            expenseId: null,
            currentPhoto: null,
        };
    },

    methods: {
        ...mapActions({
            getExpenses: "expense/getExpenses",
            getExpense: "expense/getExpense",
            deleteExpense: "expense/deleteExpense",
            deleteMultipleExpenses: "expense/deleteMultipleExpenses",
        }),

        toggleChequeColumns() {
            this.showChequeColumns = !this.showChequeColumns;

            this.headers.forEach((header) => {
                if (
                    [
                        "payment.cheque_type",
                        "payment.cheque_no",
                        "payment.cheque_due_date",
                        "payment.cheque_images",
                    ].includes(header.value)
                ) {
                    header.show = this.showChequeColumns;
                }
            });
        },

        setCurrentChequeImages(images) {
            this.currentChequeImages = images;
            this.chequeImagesDialog = true;
        },

        closeChequeImagesDialog() {
            this.currentChequeImages = null;
            this.chequeImagesDialog = false;
        },

        setExpenseId(id) {
            this.expenseId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleExpenseDelete() {
            await this.deleteExpense(this.expenseId);
            this.expenseId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },

        async deleteMultiple() {
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleMultipleExpensesDelete() {
            const ids = this.selectedItems.map(
                (selectedItem) => selectedItem.id
            );
            await this.deleteMultipleExpenses(ids);

            this.$refs.confirmationComponent.setDialog(false);
            this.selectedItems = [];
        },
    },

    computed: {
        ...mapGetters({
            expenses: "expense/expenses",
            expense: "expense/expense",
            loading: "loading",
        }),

        displayedHeaders() {
            return this.headers.filter(
                (header) =>
                    header.show ||
                    ![
                        "payment.cheque_type",
                        "payment.cheque_no",
                        "payment.cheque_due_date",
                        "payment.cheque_images",
                    ].includes(header.value)
            );
        },
    },

    mounted() {
        // remove actions if no access is given
        if (!this.can("expense_edit") && !this.can("expense_delete")) {
            this.headers = this.headers.filter(
                (header) => header.value !== "actions"
            );
        }
        this.getExpenses();
    },
};
</script>
