<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4">
            <v-row>
                <v-col cols="12">
                    <v-card :loading="formLoading" :disabled="formLoading">
                        <v-card-title primary-title>Edit Company</v-card-title>
                        <v-card-subtitle>Edit Supplier Company</v-card-subtitle>

                        <v-card-text class="mt-1">
                            <v-form @submit.prevent="update">
                                <v-row>
                                    <v-col
                                        xl="6"
                                        lg="6"
                                        md="6"
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
                                            name="company-name"
                                            label="Company Name"
                                            id="company-name"
                                            v-model="data.name"
                                            dense
                                            outlined
                                        ></v-text-field>
                                    </v-col>

                                    <v-col
                                        xl="6"
                                        lg="6"
                                        md="6"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage('logo')
                                            "
                                        ></small>
                                        <v-file-input
                                            name="company-logo"
                                            label="Photo"
                                            id="company-logo"
                                            @change="handleFile"
                                            prepend-inner-icon="mdi-camera"
                                            prepend-icon=""
                                            dense
                                            outlined
                                            hint="Only image files | Max. size 2MB"
                                            :clearable="false"
                                        ></v-file-input>
                                    </v-col>

                                    <v-col cols="12" class="py-0">
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage(
                                                    'description'
                                                )
                                            "
                                        ></small>
                                        <v-textarea
                                            rows="2"
                                            name="company-description"
                                            label="Address"
                                            id="company-description"
                                            v-model="data.description"
                                            dense
                                            outlined
                                        ></v-textarea>
                                    </v-col>
                                </v-row>

                                <v-btn color="success" type="submit"
                                    >Update</v-btn
                                >
                            </v-form>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../mixins/ValidationMixin";
import Navbar from "../navs/Navbar";

export default {
    props: ["singleCompany"],

    mixins: [ValidationMixin],

    components: { Navbar },

    data() {
        return {
            formLoading: false,
            data: {
                id: "",
                name: "",
                description: "",
                logo: "",
            },
        };
    },

    methods: {
        ...mapActions({
            getCompany: "company/getCompany",
            updateCompany: "company/updateCompany",
        }),

        handleFile(file) {
            this.data.logo = file;
        },

        async update() {
            this.formLoading = true;

            await this.updateCompany(this.data);

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                // Clear the validation messages object
                this.validation.setMessages({});

                return this.$router.push({ name: "companies" });
            }
        },
    },

    computed: {
        ...mapGetters({
            company: "company/company",
            validationErrors: "validationErrors",
        }),
    },

    async mounted() {
        await this.getCompany(this.$route.params.id);

        if (!this.company) {
            return this.$router.push({ name: "not_found" });
        }

        this.data.id = this.company.id;
        this.data.name = this.company.name;
        this.data.description = this.company.description;
    },
};
</script>
