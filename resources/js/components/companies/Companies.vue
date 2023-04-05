<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Supplier Companies</h5>

            <v-row>
                <v-col
                    xl="4"
                    lg="4"
                    md="4"
                    sm="12"
                    cols="12"
                    v-for="(company, i) in companies"
                    :key="i"
                >
                    <v-card class="mx-auto" max-width="344">
                        <v-img :src="company.logo" height="200px"></v-img>

                        <v-card-title>
                            {{ company.name }}
                        </v-card-title>

                        <v-card-subtitle v-if="company.description">
                            {{ company.description }}
                        </v-card-subtitle>

                        <v-card-actions>
                            <v-btn
                                x-small
                                text
                                color="secondary"
                                :to="`/companies/edit/${company.id}`"
                                title="Edit"
                                v-if="can('company_edit')"
                            >
                                <v-icon small>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn
                                x-small
                                text
                                color="red darken-2"
                                @click="setCompanyId(company.id)"
                                title="Delete"
                                v-if="can('company_delete')"
                            >
                                <v-icon small>mdi-delete</v-icon>
                            </v-btn>
                            <v-btn
                                x-small
                                text
                                color="info darken-2"
                                :to="`/companies/${company.id}/ledger_entries`"
                                title="Ledger Entries"
                            >
                                <v-icon small>mdi-account-cash-outline</v-icon>
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>

                <!-- Confirmation -->
                <Confirmation
                    ref="confirmationComponent"
                    :id="companyId"
                    @confirmDeletion="
                        companyId
                            ? handleCompanyDelete()
                            : handleMultipleCompaniesDelete()
                    "
                />
            </v-row>

            <alert />
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import EditCompany from "./EditCompany.vue";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";

export default {
    components: {
        EditCompany,
        Navbar,
        Confirmation,
    },

    data() {
        return {
            allCompanies: [],
            companyId: null,
        };
    },

    methods: {
        ...mapActions({
            getCompanies: "company/getCompanies",
            getCompany: "company/getCompany",
            deleteCompany: "company/deleteCompany",
            deleteMultipleCompanies: "company/deleteMultipleCompanies",
        }),

        setCompanyId(id) {
            this.companyId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleCompanyDelete() {
            await this.deleteCompany(this.companyId);
            this.companyId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },

        async deleteMultiple() {
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleMultipleCompaniesDelete() {
            const ids = this.selectedItems.map(
                (selectedItem) => selectedItem.id
            );
            await this.deleteMultipleCompanies(ids);

            this.$refs.confirmationComponent.setDialog(false);
            this.selectedItems = [];
        },
    },

    computed: {
        ...mapGetters({
            companies: "company/companies",
            company: "company/company",
            loading: "loading",
        }),
    },

    async mounted() {
        await this.getCompanies();

        this.allCompanies = this.companies;
    },
};
</script>
