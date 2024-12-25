<template>
    <div>
        <Navbar v-if="!printMode" />
        <print-button />
        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Partner Withdrawals</h5>
            <!-- Partner Selection -->
            <v-row>
                <v-col cols="12">
                    <v-select
                        v-model="selectedPartner"
                        :items="partners"
                        item-text="name"
                        item-value="id"
                        label="Select Partner"
                        dense
                        filled
                    >
                        <template v-slot:selection="{ item }">
                            <v-list-item-content>
                                <v-list-item-title>{{
                                    item.name
                                }}</v-list-item-title>
                            </v-list-item-content>
                        </template>
                    </v-select>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12">
                    <v-data-table
                        :headers="displayedHeaders"
                        :items="filteredPartnerWithdrawals"
                        class="elevation-1"
                        item-key="id"
                        :loading="loading"
                        :options.sync="options"
                        :server-items-length="total"
                        :search="search"
                        :show-select="
                            can('partner_withdrawal_delete') && !printMode
                        "
                        loading-text="Loading partner_withdrawals..."
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
                            <span>{{ money(props.item.amount) }}</span>
                        </template>
                        <!-- Cheque Columns Toggle -->
                        <template
                            v-if="showChequeColumns"
                            slot="item.payment.cheque_type"
                            slot-scope="props"
                        >
                            {{ props.item.payment.cheque_type }}
                        </template>
                        <template
                            v-if="showChequeColumns"
                            slot="item.payment.cheque_no"
                            slot-scope="props"
                        >
                            {{ props.item.payment.cheque_no }}
                        </template>
                        <template
                            v-if="showChequeColumns"
                            slot="item.payment.cheque_due_date"
                            slot-scope="props"
                        >
                            {{ props.item.payment.cheque_due_date }}
                        </template>
                        <!-- Cheque Images (Button) -->
                        <template
                            v-if="
                                props.item.payment.cheque_images.length &&
                                showChequeColumns
                            "
                            slot="item.payment.cheque_images"
                            slot-scope="props"
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
                            >
                                <v-icon>mdi-file-image-outline</v-icon>
                            </v-btn>
                        </template>
                        <!-- Description -->
                        <template
                            v-if="props.item.description"
                            slot="item.description"
                            slot-scope="props"
                        >
                            <v-tooltip bottom>
                                <template v-slot:activator="{ on, attrs }">
                                    <span v-bind="attrs" v-on="on"
                                        >{{
                                            props.item.description &&
                                            props.item.description.slice(0, 35)
                                        }}...</span
                                    >
                                </template>
                                <span>{{ props.item.description }}</span>
                            </v-tooltip>
                        </template>
                        <!-- Top Actions -->
                        <template v-slot:top v-if="!printMode">
                            <v-btn
                                color="blue-grey darken-3"
                                small
                                class="ma-2 text-right white--text"
                                @click="toggleChequeColumns"
                                v-if="can('partner_withdrawal_delete')"
                            >
                                <v-icon left>{{
                                    showChequeColumns
                                        ? "mdi-eye-off"
                                        : "mdi-eye"
                                }}</v-icon>
                                {{ showChequeColumns ? "Hide" : "Show" }} Cheque
                                Columns
                            </v-btn>
                            <v-btn
                                color="error"
                                small
                                :disabled="!selectedItems.length"
                                class="ma-2 text-right"
                                @click="deleteMultiple"
                                v-if="can('partner_withdrawal_delete')"
                            >
                                <v-icon left>mdi-trash-can-outline</v-icon>
                                Delete Selected
                            </v-btn>
                            <v-btn
                                color="success"
                                small
                                link
                                to="/partner_withdrawals/add"
                                class="ma-2 text-right"
                                v-if="can('partner_withdrawal_create')"
                            >
                                <v-icon left>mdi-account-plus-outline</v-icon>
                                New Partner Withdrawal
                            </v-btn>
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
                                :to="`/partner_withdrawals/edit/${props.item.id}`"
                                title="Edit"
                                v-if="can('partner_withdrawal_edit')"
                            >
                                <v-icon small>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn
                                x-small
                                text
                                color="red darken-2"
                                @click="setPartnerWithdrawalId(props.item.id)"
                                title="Delete"
                                v-if="can('partner_withdrawal_delete')"
                            >
                                <v-icon small>mdi-delete</v-icon>
                            </v-btn>
                        </template>
                    </v-data-table>

                    <!-- Cheque Images Dialog -->
                    <v-dialog v-model="chequeImagesDialog" width="600">
                        <ChequeImages
                            :current-cheque-images="currentChequeImages"
                            @closeDialog="closeChequeImagesDialog"
                        />
                    </v-dialog>

                    <!-- Confirmation -->
                    <Confirmation
                        ref="confirmationComponent"
                        :id="partner_withdrawalId"
                        @confirmDeletion="
                            partner_withdrawalId
                                ? handlePartnerWithdrawalDelete()
                                : handleMultiplePartnerWithdrawalsDelete()
                        "
                    />
                </v-col>
            </v-row>

            <!-- Alert Component -->
            <alert />
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DatatableMixin from "../../mixins/DatatableMixin";
import EditPartnerWithdrawal from "./EditPartnerWithdrawal.vue";
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
        EditPartnerWithdrawal,
        Navbar,
        Confirmation,
        ChequeImages,
        Excel,
        CSV,
        PDF,
    },
    data() {
        return {
            options: {},
            showChequeColumns: false,
            currentChequeImages: null,
            chequeImagesDialog: false,
            selectedPartner: null,
            headers: [
                { text: "S#", value: "sno", show: true },
                { text: "Partner", value: "partner.name", show: true },
                { text: "Title", value: "title", show: true },
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
                { text: "Actions", value: "actions", align: "d-print-none" },
            ],
            selectedItems: [],
            partner_withdrawalId: null,
            currentPhoto: null,
        };
    },
    methods: {
        ...mapActions({
            getPartnerWithdrawals: "partner_withdrawal/getPartnerWithdrawals",
            searchPartnerWithdrawals:
                "partner_withdrawal/searchPartnerWithdrawals",
            getPartners: "partner/getPartners",
            deletePartnerWithdrawal:
                "partner_withdrawal/deletePartnerWithdrawal",
            deleteMultiplePartnerWithdrawals:
                "partner_withdrawal/deleteMultiplePartnerWithdrawals",
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
        setPartnerWithdrawalId(id) {
            this.partner_withdrawalId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },
        async handlePartnerWithdrawalDelete() {
            await this.deletePartnerWithdrawal(this.partner_withdrawalId);
            this.partner_withdrawalId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },
        async deleteMultiple() {
            this.$refs.confirmationComponent.setDialog(true);
        },
        async handleMultiplePartnerWithdrawalsDelete() {
            const ids = this.selectedItems.map(
                (selectedItem) => selectedItem.id
            );
            await this.deleteMultiplePartnerWithdrawals(ids);
            this.$refs.confirmationComponent.setDialog(false);
            this.selectedItems = [];
        },

        getSelectedPartnerFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get("partner_id");
        },
    },
    computed: {
        ...mapGetters({
            partner_withdrawals: "partner_withdrawal/partner_withdrawals",
            loading: "partner_withdrawal/loading",
            total: "partner_withdrawal/total",
            partners: "partner/partners",
        }),

        filteredPartnerWithdrawals() {
            if (this.selectedPartner) {
                return this.partner_withdrawals.filter(
                    (withdrawal) =>
                        withdrawal.partner.id === parseInt(this.selectedPartner)
                );
            }

            return this.partner_withdrawals;
        },

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
    watch: {
        options: {
            handler() {
                this.getPartnerWithdrawals(this.options);
            },
            deep: true,
        },

        search: {
            handler(newVal) {
                this.options.search = newVal;
                this.searchPartnerWithdrawals(this.options);
            },
        },
    },

    async mounted() {
        // Fetch partners when component mounts
        await this.getPartners();

        this.selectedPartner =
            this.partners.find(
                (partner) =>
                    partner.id === parseInt(this.getSelectedPartnerFromUrl())
            )?.id || null;

        // Remove actions if no access is given
        if (
            !this.can("partner_withdrawal_edit") &&
            !this.can("partner_withdrawal_delete")
        ) {
            this.headers = this.headers.filter(
                (header) => header.value !== "actions"
            );
        }
    },
};
</script>
