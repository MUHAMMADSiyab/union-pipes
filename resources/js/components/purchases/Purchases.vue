<template>
  <div>
    <Navbar v-if="!printMode" />

    <print-button />

    <v-container class="mt-4">
      <h5 class="text-subtitle-1 mb-2">Purchases</h5>

      <v-row>
        <v-col cols="12">
          <v-data-table
            :headers="headers"
            :items="purchases"
            class="elevation-1"
            item-key="id"
            :search="search"
            :items-per-page="perPage"
            :loading="loading"
            :show-select="can('purchase_delete') && !printMode"
            loading-text="Loading purchases..."
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

            <!-- Cheque Images (Button) -->
            <template
              slot="item.payment.cheque_images"
              slot-scope="props"
              v-if="props.item.payment.cheque_images.length"
            >
              <v-btn
                color="light"
                x-small
                title="Cheque Image(s)"
                @click="
                  setCurrentChequeImages(props.item.payment.cheque_images)
                "
                ><v-icon>mdi-file-image-outline</v-icon></v-btn
              >
            </template>

            <!-- Actions -->
            <template slot="item.actions" slot-scope="props">
              <v-menu offset-y>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn x-small v-bind="attrs" v-on="on" text title="Action">
                    <v-icon small>mdi-dots-vertical</v-icon>
                  </v-btn>
                </template>

                <v-list dense>
                  <v-list-item
                    link
                    :to="`/purchases/edit/${props.item.id}`"
                    title="Edit"
                    v-if="can('purchase_edit')"
                  >
                    <v-list-item-title>Edit</v-list-item-title>
                  </v-list-item>
                  <v-list-item
                    link
                    :to="`/purchases/${props.item.id}`"
                    title="Details"
                    v-if="can('purchase_show')"
                  >
                    <v-list-item-title>Details</v-list-item-title>
                  </v-list-item>
                  <v-list-item
                    @click="setPurchaseId(props.item.id)"
                    title="Delete"
                    v-if="can('purchase_delete')"
                  >
                    <v-list-item-title>Delete</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </template>

            <!-- Top -->
            <template v-slot:top v-if="!printMode">
              <v-btn
                color="error"
                small
                :disabled="!selectedItems.length"
                class="ma-2 text-right"
                @click="deleteMultiple"
                ><v-icon left>mdi-trash-can-outline</v-icon> Delete
                Selected</v-btn
              >

              <v-btn
                color="success"
                small
                link
                to="/purchases/add"
                class="ma-2 text-right"
                v-if="can('purchase_create')"
              >
                <v-icon left>mdi-plus-thick</v-icon>
                New Purchase</v-btn
              >

              <!-- Export -->
              <Excel
                module="purchases"
                :ids="selectedItems.map((item) => item.id)"
              />
              <CSV
                module="purchases"
                :ids="selectedItems.map((item) => item.id)"
              />
              <PDF
                module="purchases"
                :ids="selectedItems.map((item) => item.id)"
              />

              <!-- <v-select
                :items="['Installment', 'On Cash', 'Sold', 'Unsold', 'All']"
                v-model="purchaseType"
                dense
                class="d-inline-block ml-5 mt-3"
              ></v-select> -->

              <v-text-field
                v-model="search"
                placeholder="Search"
                class="mx-4"
                append-icon="mdi-magnify"
              ></v-text-field>
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
            :id="purchaseId"
            @confirmDeletion="
              purchaseId
                ? handlePurchaseDelete()
                : handleMultiplePurchasesDelete()
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
import EditPurchase from "./EditPurchase.vue";
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
    EditPurchase,
    Navbar,
    Confirmation,
    ChequeImages,
    Excel,
    CSV,
    PDF,
  },

  data() {
    return {
      currentChequeImages: null,
      chequeImagesDialog: false,
      headers: [
        { text: "S#", value: "sno" },
        { text: "Date", value: "date" },
        { text: "Invoice#", value: "invoice_no" },
        { text: "Company", value: "company.name" },
        { text: "Petrol Qty.", value: "petrol_quantity" },
        { text: "Diesel Qty.", value: "diesel_quantity" },
        { text: "Petrol/Ltr.", value: "petrol_price" },
        { text: "Diesel/Ltr.", value: "diesel_price" },
        { text: "Total Amount", value: "total_amount" },
        { text: "Amount", value: "payment.amount" },
        { text: "Payment Method", value: "payment.payment_method" },
        { text: "Date", value: "payment.payment_date" },
        { text: "Bank", value: "payment.bank.name" },
        { text: "Cheque Type", value: "payment.cheque_type" },
        { text: "Cheque No.", value: "payment.cheque_no" },
        { text: "Cheque Due Date", value: "payment.cheque_due_date" },
        { text: "Cheque Images", value: "payment.cheque_images" },
        { text: "Actions", value: "actions", align: " d-print-none" },
      ],
      selectedItems: [],
      purchaseId: null,
      currentPhoto: null,
    };
  },

  methods: {
    ...mapActions({
      getPurchases: "purchase/getPurchases",
      getPurchase: "purchase/getPurchase",
      deletePurchase: "purchase/deletePurchase",
      deleteMultiplePurchases: "purchase/deleteMultiplePurchases",
    }),

    setCurrentChequeImages(images) {
      this.currentChequeImages = images;
      this.chequeImagesDialog = true;
    },

    closeChequeImagesDialog() {
      this.currentChequeImages = null;
      this.chequeImagesDialog = false;
    },

    setPurchaseId(id) {
      this.purchaseId = id;
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handlePurchaseDelete() {
      await this.deletePurchase(this.purchaseId);
      this.purchaseId = null;
      this.$refs.confirmationComponent.setDialog(false);
    },

    async deleteMultiple() {
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleMultiplePurchasesDelete() {
      const ids = this.selectedItems.map((selectedItem) => selectedItem.id);
      await this.deleteMultiplePurchases(ids);

      this.$refs.confirmationComponent.setDialog(false);
      this.selectedItems = [];
    },
  },

  computed: {
    ...mapGetters({
      purchases: "purchase/purchases",
      purchase: "purchase/purchase",
      loading: "loading",
    }),
  },

  mounted() {
    // remove actions if no access is given
    if (!this.can("purchase_edit") && !this.can("purchase_delete")) {
      this.headers = this.headers.filter(
        (header) => header.value !== "actions"
      );
    }
    this.getPurchases();
  },
};
</script>