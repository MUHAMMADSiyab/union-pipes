<template>
    <v-card :loading="formLoading" :disabled="formLoading">
        <v-card-title primary-title>New Expense Source</v-card-title>
        <v-card-subtitle>Add a New Expense Source</v-card-subtitle>

        <v-card-text class="mt-1">
            <v-form @submit.prevent="add">
                <small
                    class="red--text"
                    v-if="validation.hasErrors()"
                    v-text="validation.getMessage('name')"
                ></small>
                <v-text-field
                    name="expense_source_name"
                    label="Expense Source Name"
                    id="expense_source_name"
                    v-model="data.name"
                    dense
                    outlined
                ></v-text-field>

                <v-btn color="primary" type="submit">Add</v-btn>
            </v-form>
        </v-card-text>
    </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../mixins/ValidationMixin";

export default {
    mixins: [ValidationMixin],

    data() {
        return {
            formLoading: false,
            data: {
                name: "",
            },
        };
    },

    methods: {
        ...mapActions({ addExpenseSource: "expense_source/addExpenseSource" }),

        async add() {
            this.formLoading = true;

            await this.addExpenseSource(this.data);

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                this.data.name = "";
                this.data.account_no = "";
                this.data.branch_name = "";
                this.data.branch_code = "";
                this.data.balance = "";
                // Clear the validation messages object
                this.validation.setMessages({});
            }
        },
    },

    computed: {
        ...mapGetters({
            validationErrors: "validationErrors",
        }),
    },
};
</script>
