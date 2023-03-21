<template>
    <div>
        <v-data-table
            :headers="headers"
            :items="expense_sources"
            class="elevation-1"
            item-key="id"
            :search="search"
            :items-per-page="perPage"
            :loading="loading"
            :show-select="can('expense_delete') && !printMode"
            loading-text="Loading expense_sources..."
            :footer-props="footerProps"
            v-model="selectedItems"
        >
            <!-- S# -->
            <template slot="item.sno" slot-scope="props">{{
                props.index + 1
            }}</template>

            <!-- Amount -->
            <template slot="item.balance" slot-scope="props">
                <span> {{ money(props.item.balance) }} </span>
            </template>

            <!-- Top -->
            <template v-slot:top v-if="!printMode">
                <v-btn
                    color="error"
                    small
                    :disabled="!selectedItems.length"
                    class="ma-2 text-right"
                    @click="deleteMultiple"
                    v-if="can('expense_delete')"
                    ><v-icon left>mdi-trash-can-outline</v-icon> Delete
                    Selected</v-btn
                >

                <Excel
                    module="expense_sources"
                    :ids="selectedItems.map((item) => item.id)"
                />
                <CSV
                    module="expense_sources"
                    :ids="selectedItems.map((item) => item.id)"
                />
                <PDF
                    module="expense_sources"
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
                    @click="showEditDialog(props.item.id)"
                    title="Edit"
                    v-if="can('expense_edit')"
                >
                    <v-icon small>mdi-pencil</v-icon>
                </v-btn>
                <v-btn
                    x-small
                    text
                    color="red darken-2"
                    @click="setExpenseSourceId(props.item.id)"
                    title="Delete"
                    v-if="can('expense_delete')"
                >
                    <v-icon small>mdi-delete</v-icon>
                </v-btn>
            </template>
        </v-data-table>

        <!-- Edit dialog -->
        <v-dialog v-model="editDialog" max-width="500" persistent>
            <EditExpenseSource
                :single-expense-source="expense_source"
                v-if="expense_source"
                @closeDialog="closeEditDialog"
            />
        </v-dialog>

        <!-- Confirmation -->
        <Confirmation
            ref="confirmationComponent"
            :id="expense_source_id"
            @confirmDeletion="
                expense_source_id
                    ? handleExpenseSourceDelete()
                    : handleMultipleExpenseSourcesDelete()
            "
        />
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DatatableMixin from "../../mixins/DatatableMixin";
import EditExpenseSource from "./EditExpenseSource.vue";
import Confirmation from "../globals/Confirmation";
import Excel from "../globals/exports/Excel.vue";
import CSV from "../globals/exports/CSV.vue";
import PDF from "../globals/exports/PDF.vue";
import CurrencyMixin from "../../mixins/CurrencyMixin";

export default {
    mixins: [DatatableMixin, CurrencyMixin],

    components: {
        EditExpenseSource,
        Confirmation,
        Excel,
        CSV,
        PDF,
    },

    data() {
        return {
            editDialog: false,
            paymentSetting: null,
            headers: [
                { text: "S#", value: "sno" },
                { text: "ExpenseSource Name", value: "name" },
                { text: "Actions", value: "actions", align: " d-print-none" },
            ],
            selectedItems: [],

            expense_source_id: null,
        };
    },

    methods: {
        ...mapActions({
            getExpenseSources: "expense_source/getExpenseSources",
            getExpenseSource: "expense_source/getExpenseSource",
            deleteExpenseSource: "expense_source/deleteExpenseSource",
            deleteMultipleExpenseSources:
                "expense_source/deleteMultipleExpenseSources",
        }),

        showEditDialog(id) {
            this.editDialog = true;

            this.getExpenseSource(id);
        },

        closeEditDialog() {
            this.editDialog = false;
        },

        setExpenseSourceId(id) {
            this.expense_source_id = id;
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleExpenseSourceDelete() {
            await this.deleteExpenseSource(this.expense_source_id);
            this.expense_source_id = null;
            this.$refs.confirmationComponent.setDialog(false);
        },

        async deleteMultiple() {
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleMultipleExpenseSourcesDelete() {
            const ids = this.selectedItems.map(
                (selectedItem) => selectedItem.id
            );
            await this.deleteMultipleExpenseSources(ids);

            this.$refs.confirmationComponent.setDialog(false);
            this.selectedItems = [];
        },
    },

    computed: {
        ...mapGetters({
            expense_sources: "expense_source/expense_sources",
            expense_source: "expense_source/expense_source",
            loading: "loading",
        }),
    },

    mounted() {
        // remove actions if no access is given
        if (!this.can("expense_edit") && !this.can("expense_delete")) {
            this.headers = this.headers.filter(
                (header) => header.value !== "actions"
            );
        }

        this.getExpenseSources();
    },
};
</script>
