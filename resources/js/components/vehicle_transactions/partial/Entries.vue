<template>
  <div>
    <v-data-table
      :headers="headers"
      :items="vehicle_transaction_entries"
      class="elevation-1 mt-2"
      item-key="id"
      :search="search"
      :items-per-page="perPage"
      :loading="loading"
      :show-select="can('vehicle_transaction_delete') && !printMode"
      loading-text="Loading vehicle transactions entries..."
      :footer-props="footerProps"
      v-model="selectedItems"
    >
      <!-- S# -->
      <template slot="item.sno" slot-scope="props">{{
        props.index + 1
      }}</template>

      <!-- Amount -->
      <template slot="item.vehicle_charges" slot-scope="props">
        <span class="font-weight-bold indigo--text text--accent-4">
          {{ money(props.item.vehicle_charges) }}
        </span>
      </template>

      <template slot="item.expense" slot-scope="props">
        <span class="font-weight-bold indigo--text text--accent-4">
          {{ money(props.item.expense ? props.item.expense : 0) }}
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
          name="invoice_no"
          placeholder="Select Purchase"
          width="100"
          class="ma-2 ml-3 d-inline-block mt-3"
          id="invoice_no"
          v-model="data.invoice_no"
          @change="handlePurchaseChange"
          dense
        ></v-select>

        <v-btn
          color="error"
          small
          :disabled="!selectedItems.length"
          class="ma-2 text-right"
          @click="deleteMultiple"
          v-if="can('vehicle_transaction_delete')"
          ><v-icon left>mdi-trash-can-outline</v-icon> Delete Selected</v-btn
        >

        <Excel
          module="vehicle_transactions"
          :ids="selectedItems.map((item) => item.id)"
          :data="{ vehicle_id: entries[0].vehicle_id }"
        />
        <CSV
          module="vehicle_transactions"
          :ids="selectedItems.map((item) => item.id)"
          :data="{ vehicle_id: entries[0].vehicle_id }"
        />
        <PDF
          module="vehicle_transactions"
          :ids="selectedItems.map((item) => item.id)"
          :data="{ vehicle_id: entries[0].vehicle_id }"
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
              :to="`/vehicle_transactions/edit/${props.item.id}`"
              title="Edit"
              v-if="can('vehicle_transaction_edit')"
            >
              <v-list-item-title>Edit</v-list-item-title>
            </v-list-item>
            <v-list-item
              @click="setVehicleTransactionId(props.item.id)"
              title="Delete"
              v-if="can('vehicle_transaction_delete')"
            >
              <v-list-item-title>Delete</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </template>
    </v-data-table>

    <!-- Confirmation -->
    <Confirmation
      ref="confirmationComponent"
      :id="vehicle_transactionId"
      @confirmDeletion="
        vehicle_transactionId
          ? handleVehicleTransactionDelete()
          : handleMultipleVehicleTransactionsDelete()
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
      vehicle_transaction_entries: this.entries,
      vehicles: this.entries.map((entry) => entry.vehicle_no),
      data: {
        invoice_no: "",
      },
      headers: [
        { text: "S#", value: "sno" },
        { text: "Vehicle Charges", value: "vehicle_charges" },
        { text: "Expense", value: "expense" },
        { text: "Balance", value: "balance" },
        { text: "Driver", value: "driver" },
        { text: "Purchase (Invoice #)", value: "purchase.invoice_no" },
        { text: "Date", value: "date" },
        { text: "Actions", value: "actions", align: " d-print-none" },
      ],
      selectedItems: [],
      vehicle_transactionId: null,
    };
  },

  methods: {
    ...mapActions({
      deleteVehicleTransaction: "vehicle_transaction/deleteVehicleTransaction",
      deleteMultipleVehicleTransactions:
        "vehicle_transaction/deleteMultipleVehicleTransactions",
    }),

    setVehicleTransactionId(id) {
      this.vehicle_transactionId = id;
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleVehicleTransactionDelete() {
      await this.deleteVehicleTransaction(this.vehicle_transactionId);
      this.vehicle_transactionId = null;
      this.$refs.confirmationComponent.setDialog(false);

      this.$router.go(this.$router.currentRoute);
    },

    async deleteMultiple() {
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleMultipleVehicleTransactionsDelete() {
      const ids = this.selectedItems.map((selectedItem) => selectedItem.id);
      await this.deleteMultipleVehicleTransactions(ids);

      this.$refs.confirmationComponent.setDialog(false);
      this.selectedItems = [];

      this.$router.go(this.$router.currentRoute);
    },

    handlePurchaseChange(invoice_no) {
      const purchaseFiltered = this.entries.filter(
        (entry) => entry.purchase.invoice_no === invoice_no
      );

      this.vehicle_transaction_entries = purchaseFiltered.filter(
        this.onlyUnique
      );
    },

    onlyUnique(value, index, self) {
      return self.indexOf(value) === index;
    },
  },

  computed: {
    ...mapGetters({
      loading: "loading",
    }),
  },
};
</script>