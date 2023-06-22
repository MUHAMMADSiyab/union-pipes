<template>
    <v-card :loading="formLoading" :disabled="formLoading">
        <v-card-title primary-title>New Machine</v-card-title>
        <v-card-subtitle>Edit Machine</v-card-subtitle>

        <v-card-text class="mt-1">
            <v-form @submit.prevent="update">
                <v-row>
                    <v-col
                        xl="12"
                        lg="12"
                        md="12"
                        sm="12"
                        cols="12"
                        class="py-0"
                    >
                        <small
                            class="red--text"
                            v-if="validation.hasErrors()"
                            v-text="validation.getMessage('name')"
                        ></small>
                        <v-text-field
                            name="machine-name"
                            label="Machine Name"
                            id="machine-name"
                            v-model="data.name"
                            dense
                            outlined
                        ></v-text-field>
                    </v-col>
                </v-row>

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
    props: ["singleMachine"],

    mixins: [ValidationMixin],

    data() {
        const { id, name } = this.singleMachine;

        return {
            formLoading: false,
            data: {
                id,
                name,
            },
        };
    },

    methods: {
        ...mapActions({ updateMachine: "machine/updateMachine" }),

        async update() {
            this.formLoading = true;

            await this.updateMachine(this.data);

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
        singleMachine: {
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
