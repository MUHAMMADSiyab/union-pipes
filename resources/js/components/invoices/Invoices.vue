<template>
  <div>
    <Navbar v-if="!printMode" />

    <v-container class="mt-4">
      <v-row>
        <v-col xl="6" lg="6" md="6" sm="12" cols="12" v-if="!printMode">
          <v-data-table
            :headers="headers"
            :items="invoices"
            class="elevation-1"
            item-key="id"
            :search="search"
            :items-per-page="perPage"
            :loading="loading"
            :show-select="can('invoice_delete') && !printMode"
            loading-text="Loading invoices..."
            :footer-props="footerProps"
            v-model="selectedItems"
            @click:row="handleRowClick"
            dense
          >
            <!-- S# -->
            <template slot="item.sno" slot-scope="props">{{
              props.index + 1
            }}</template>

            <!-- Top -->
            <template v-slot:top v-if="!printMode">
              <v-btn
                color="error"
                small
                :disabled="!selectedItems.length"
                class="ma-2 text-right"
                @click="deleteMultiple"
                v-if="can('invoice_delete')"
                ><v-icon left>mdi-trash-can-outline</v-icon> Delete
                Selected</v-btn
              >

              <v-btn
                color="success"
                small
                link
                to="/invoices/add"
                class="ma-2 text-right"
                v-if="can('invoice_create')"
              >
                <v-icon left>mdi-account-plus-outline</v-icon>
                New Invoice</v-btn
              >

              <v-btn
                color="info"
                small
                link
                class="ma-2 text-right"
                @click="print"
              >
                Print Invoice</v-btn
              >

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
                :to="`/invoices/edit/${props.item.id}`"
                title="Edit"
                v-if="can('invoice_edit')"
              >
                <v-icon small>mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                x-small
                text
                color="red darken-2"
                @click="setUtilityId(props.item.id)"
                title="Delete"
                v-if="can('invoice_delete')"
              >
                <v-icon small>mdi-delete</v-icon>
              </v-btn>
            </template>
          </v-data-table>

          <!-- Confirmation -->
          <Confirmation
            ref="confirmationComponent"
            :id="invoiceId"
            @confirmDeletion="
              invoiceId ? handleInvoiceDelete() : handleMultipleInvoicesDelete()
            "
          />
        </v-col>

        <v-col v-bind="getPrintClass(6, can('bank_create'))" cols="12">
          <Invoice
            :invoice="currentInvoice"
            v-if="currentInvoice"
            transition="scale-transition"
            origin="center center"
          />
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";
import DatatableMixin from "../../mixins/DatatableMixin";
import Invoice from "./partial/Invoice.vue";

export default {
  mixins: [DatatableMixin],

  components: { Navbar, Confirmation, Invoice },

  data() {
    return {
      headers: [
        { text: "S#", value: "sno" },
        { text: "Invoice #", value: "invoice_no" },
        { text: "Buyer", value: "buyer" },
        { text: "Date", value: "date" },
        { text: "Actions", value: "actions", align: " d-print-none" },
      ],
      selectedItems: [],
      invoiceId: null,
      currentInvoice: null,
    };
  },

  methods: {
    ...mapActions({
      getInvoices: "invoice/getInvoices",
      deleteInvice: "invoice/deleteInvice",
      deleteMultipleInvoices: "invoice/deleteMultipleInvoices",
    }),

    handleRowClick(currentItem) {
      this.currentInvoice = currentItem;
    },

    setInvoiceId(id) {
      this.invoiceId = id;
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleInvoiceDelete() {
      await this.deleteUtility(this.invoiceId);
      this.invoiceId = null;
      this.$refs.confirmationComponent.setDialog(false);
    },

    async deleteMultiple() {
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleMultipleInvoicesDelete() {
      const ids = this.selectedItems.map((selectedItem) => selectedItem.id);
      await this.deleteMultipleInvoices(ids);

      this.$refs.confirmationComponent.setDialog(false);
      this.selectedItems = [];
    },
  },

  computed: {
    ...mapGetters({
      invoices: "invoice/invoices",
      loading: "loading",
    }),
  },

  async mounted() {
    // remove actions if no access is given
    if (!this.can("invoice_edit") && !this.can("invoice_delete")) {
      this.headers = this.headers.filter(
        (header) => header.value !== "actions"
      );
    }

    await this.getInvoices();

    if (this.invoices.length) {
      this.currentInvoice = this.invoices[0];
    }
  },
};
</script>