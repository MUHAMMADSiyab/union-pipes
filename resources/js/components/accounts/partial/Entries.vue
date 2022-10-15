<template>
  <div>
    <v-data-table
      :headers="headers"
      :items="account_entries"
      class="elevation-1 mt-2"
      item-key="id"
      :search="search"
      :items-per-page="perPage"
      :loading="loading"
      :show-select="can('account_delete') && !printMode"
      loading-text="Loading account entries..."
      :footer-props="footerProps"
      v-model="selectedItems"
    >
      <!-- S# -->
      <template slot="item.sno" slot-scope="props">{{
        props.index + 1
      }}</template>

      <!-- Logo -->
      <template slot="item.logo" slot-scope="props" v-if="props.item.logo">
        <v-img
          :aspect-ratio="16 / 9"
          width="50"
          :src="props.item.logo"
          class="rounded"
          @click="showLogoDialog(props.item.logo)"
        ></v-img>
      </template>

      <!-- Amount -->
      <template slot="item.total_amount" slot-scope="props">
        <span class="font-weight-bold indigo--text text--accent-4">
          {{ money(props.item.total_amount) }}
        </span>
      </template>

      <template slot="item.payment.amount" slot-scope="props">
        <span class="font-weight-bold indigo--text text--accent-4">
          {{ money(props.item.payment ? props.item.payment.amount : 0) }}
        </span>
      </template>

      <template slot="item.balance" slot-scope="props">
        <span class="font-weight-bold indigo--text text--accent-4">
          {{ money(props.item.balance) }}
        </span>
      </template>

      <!-- Top -->
      <template v-slot:top v-if="!printMode">
        <v-select
          :items="vehicles"
          name="vehicle_no"
          placeholder="Select Vehicle"
          width="100"
          class="ma-2 ml-3 d-inline-block mt-3"
          id="vehicle_no"
          v-model="data.vehicle_no"
          @change="handleVehicleChange"
          dense
        ></v-select>

        <v-btn
          color="error"
          small
          :disabled="!selectedItems.length"
          class="ma-2 text-right"
          @click="deleteMultiple"
          v-if="can('account_delete')"
          ><v-icon left>mdi-trash-can-outline</v-icon> Delete Selected</v-btn
        >

        <v-btn
          color="success"
          small
          link
          to="/accounts/add"
          class="ma-2 text-right"
          v-if="can('account_create')"
        >
          <v-icon left>mdi-account-plus-outline</v-icon>
          New Entry</v-btn
        >

        <Excel
          module="accounts"
          :ids="selectedItems.map((item) => item.id)"
          :data="{ customer_id: entries[0].customer_id }"
        />
        <CSV
          module="accounts"
          :ids="selectedItems.map((item) => item.id)"
          :data="{ customer_id: entries[0].customer_id }"
        />
        <PDF
          module="accounts"
          :ids="selectedItems.map((item) => item.id)"
          :data="{ customer_id: entries[0].customer_id }"
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
        <v-menu offset-y>
          <template v-slot:activator="{ on, attrs }">
            <v-btn x-small v-bind="attrs" v-on="on" text title="Action">
              <v-icon small>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>

          <v-list dense>
            <v-list-item
              link
              :to="`/accounts/edit/${props.item.id}`"
              title="Edit"
              v-if="can('account_edit')"
            >
              <v-list-item-title>Edit</v-list-item-title>
            </v-list-item>
            <v-list-item
              @click="setAccountId(props.item.id)"
              title="Delete"
              v-if="can('account_delete')"
            >
              <v-list-item-title>Delete</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
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
      :id="accountId"
      @confirmDeletion="
        accountId ? handleAccountDelete() : handleMultipleAccountsDelete()
      "
    />
  </div>
</template>

<script>
import DatatableMixin from "../../../mixins/DatatableMixin";
import Confirmation from "../../globals/Confirmation.vue";
import Navbar from "../../navs/Navbar";
import Excel from "../../globals/exports/Excel.vue";
import CSV from "../../globals/exports/CSV.vue";
import PDF from "../../globals/exports/PDF.vue";
import CurrencyMixin from "../../../mixins/CurrencyMixin";
import { mapActions, mapGetters } from "vuex";
import ChequeImages from "../../globals/ChequeImages.vue";
import vehicle from "../../../store/modules/vehicle";

export default {
  props: ["entries"],

  mixins: [DatatableMixin, CurrencyMixin],

  components: {
    Navbar,
    Confirmation,
    Excel,
    CSV,
    PDF,
    ChequeImages,
  },

  data() {
    return {
      account_entries: this.entries,
      vehicles: this.entries.map((entry) => entry.vehicle_no),
      data: {
        vehicle_no: "",
      },
      currentChequeImages: null,
      chequeImagesDialog: false,
      headers: [
        { text: "S#", value: "sno" },
        { text: "Vehicle", value: "vehicle_no" },
        { text: "Product", value: "product" },
        { text: "Product Rate", value: "product_price" },
        { text: "Product Quantity (Ltrs.)", value: "product_quantity" },
        { text: "Total Amount", value: "total_amount" },
        { text: "Total Paid", value: "payment.amount" },
        { text: "Balance", value: "balance" },
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
      accountId: null,
    };
  },

  methods: {
    ...mapActions({
      deleteAccount: "account/deleteAccount",
      deleteMultipleAccounts: "account/deleteMultipleAccounts",
    }),

    setCurrentChequeImages(images) {
      this.currentChequeImages = images;
      this.chequeImagesDialog = true;
    },

    closeChequeImagesDialog() {
      this.currentChequeImages = null;
      this.chequeImagesDialog = false;
    },

    setAccountId(id) {
      this.accountId = id;
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleAccountDelete() {
      await this.deleteAccount(this.accountId);
      this.accountId = null;
      this.$refs.confirmationComponent.setDialog(false);

      this.$router.go(this.$router.currentRoute);
    },

    async deleteMultiple() {
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleMultipleAccountsDelete() {
      const ids = this.selectedItems.map((selectedItem) => selectedItem.id);
      await this.deleteMultipleAccounts(ids);

      this.$refs.confirmationComponent.setDialog(false);
      this.selectedItems = [];

      this.$router.go(this.$router.currentRoute);
    },

    handleVehicleChange(vehicle_no) {
      this.account_entries = this.entries.filter(
        (entry) => entry.vehicle_no === vehicle_no
      );
    },
  },

  computed: {
    ...mapGetters({
      loading: "loading",
    }),
  },
};
</script>