<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4">
            <v-row>
                <v-col cols="12">
                    <v-card :loading="formLoading" :disabled="formLoading">
                        <v-card-title primary-title>New Machine</v-card-title>
                        <v-card-subtitle>Add a New Machine</v-card-subtitle>

                        <v-card-text class="mt-1">
                            <v-form @submit.prevent="add">
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
                                            v-text="
                                                validation.getMessage('name')
                                            "
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

                                <v-btn color="primary" type="submit">Add</v-btn>
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
                name: "",
                per_kg_price: "",
            },
        };
    },

    methods: {
        ...mapActions({ addMachine: "machine/addMachine" }),

        async add() {
            this.formLoading = true;

            await this.addMachine(this.data);

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                this.data.name = "";
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
