<template>
    <v-card :loading="formLoading" :disabled="formLoading">
        <v-card-title primary-title>Edit Purchase Item</v-card-title>

        <v-card-text class="mt-1">
            <v-form @submit.prevent="update">
                <small
                    class="red--text"
                    v-if="validation.hasErrors()"
                    v-text="validation.getMessage('name')"
                ></small>
                <v-text-field
                    name="purchase-item-name"
                    label="Purchase Item Name"
                    id="purchase-item-name"
                    v-model="data.name"
                    dense
                    outlined
                ></v-text-field>

                <v-btn color="success" type="submit">Update</v-btn>
                <v-btn color="secondary" @click="closeDialog">Cancel</v-btn>
            </v-form>
        </v-card-text>
    </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../mixins/ValidationMixin";

export default {
    props: ["singlePurchaseItem"],

    mixins: [ValidationMixin],

    data() {
        const { id, name } = this.singlePurchaseItem;

        return {
            formLoading: false,
            data: {
                id,
                name,
            },
        };
    },

    methods: {
        ...mapActions({
            updatePurchaseItem: "purchase_item/updatePurchaseItem",
        }),

        async update() {
            this.formLoading = true;

            await this.updatePurchaseItem(this.data);

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                // Clear the validation messages object
                this.validation.setMessages({});
                this.closeDialog();
            }
        },

        closeDialog() {
            this.$emit("closeDialog");
        },
    },

    watch: {
        singlePurchaseItem: {
            handler({ id, name }) {
                this.data.id = id;
                this.data.name = name;
            },
        },
    },

    computed: {
        ...mapGetters({
            validationErrors: "validationErrors",
        }),
    },
};
</script>
