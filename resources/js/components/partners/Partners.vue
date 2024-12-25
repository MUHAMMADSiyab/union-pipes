<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Partners</h5>

            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-text>
                            <v-text-field
                                v-model="searchKeyword"
                                label="Search by Partner Name"
                                outlined
                                dense
                            ></v-text-field>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Permanent Partners -->
            <v-row>
                <v-col
                    v-for="(partner, index) in partners"
                    :key="index"
                    cols="12"
                    sm="6"
                    md="4"
                    lg="3"
                    xl="2"
                >
                    <div
                        class="d-flex flex-column align-center pa-2"
                        style="background-color: #fff; border-radius: 8px"
                    >
                        <div class="mb-2">
                            <v-avatar
                                size="80"
                                color="grey"
                                class="white--text"
                            >
                                <v-img :src="partner.photo" contain></v-img>
                            </v-avatar>
                        </div>
                        <div class="text-center mb-2">
                            <div class="font-weight-bold">
                                {{ partner.name }}
                            </div>
                            <div
                                class="text--secondary mt-1"
                                style="font-size: 12px"
                            >
                                CNIC: {{ partner.cnic }}
                            </div>
                            <div
                                class="text--secondary"
                                style="font-size: 12px"
                            >
                                Phone: {{ partner.phone }}
                            </div>
                        </div>
                        <div class="d-flex justify-center">
                            <v-btn
                                small
                                color="primary"
                                icon
                                :to="`/partners/edit/${partner.id}`"
                                title="Edit"
                                v-if="can('partner_edit')"
                            >
                                <v-icon small>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn
                                small
                                color="error"
                                icon
                                @click="setPartnerId(partner.id)"
                                title="Delete"
                                v-if="can('partner_delete')"
                            >
                                <v-icon small>mdi-delete</v-icon>
                            </v-btn>
                            <v-btn
                                small
                                color="info darken-2"
                                icon
                                :to="`/partner_withdrawals?partner_id=${partner.id}`"
                                :title="`${partner.name}'s Withdrawals`"
                            >
                                <v-icon small>mdi-account-cash-outline</v-icon>
                            </v-btn>
                        </div>
                    </div>
                </v-col>
            </v-row>

            <!-- Confirmation -->
            <Confirmation
                ref="confirmationComponent"
                :id="partnerId"
                @confirmDeletion="
                    partnerId
                        ? handlePartnerDelete()
                        : handleMultiplePartnersDelete()
                "
            />

            <alert />
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import EditPartner from "./EditPartner.vue";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";

export default {
    components: {
        EditPartner,
        Navbar,
        Confirmation,
    },

    data() {
        return {
            searchKeyword: "",
            partnerId: null,
        };
    },

    methods: {
        ...mapActions({
            getPartners: "partner/getPartners",
            searchPartners: "partner/searchPartners",
            getPartner: "partner/getPartner",
            deletePartner: "partner/deletePartner",
            deleteMultiplePartners: "partner/deleteMultiplePartners",
        }),

        setPartnerId(id) {
            this.partnerId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handlePartnerDelete() {
            await this.deletePartner(this.partnerId);
            this.partnerId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },

        async deleteMultiple() {
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleMultiplePartnersDelete() {
            const ids = this.selectedItems.map(
                (selectedItem) => selectedItem.id
            );
            await this.deleteMultiplePartners(ids);

            this.$refs.confirmationComponent.setDialog(false);
            this.selectedItems = [];
        },
    },

    computed: {
        ...mapGetters({
            partners: "partner/partners",
            partner: "partner/partner",
            loading: "loading",
        }),
    },

    watch: {
        searchKeyword: {
            handler(searchValue) {
                this.searchPartners({
                    searchKeyword: searchValue,
                });
            },
        },
    },

    mounted() {
        this.getPartners();
    },
};
</script>

<style scoped>
.v-avatar {
    border-radius: 50%;
}
</style>
