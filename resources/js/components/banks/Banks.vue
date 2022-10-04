<template>
  <div>
    <v-data-table
      :headers="headers"
      :items="banks"
      class="elevation-1"
      item-key="id"
      :search="search"
      :items-per-page="perPage"
      :loading="loading"
      :show-select="can('bank_delete') && !printMode"
      loading-text="Loading banks..."
      :footer-props="footerProps"
      v-model="selectedItems"
    >
      <!-- S# -->
      <template slot="item.sno" slot-scope="props">{{
        props.index + 1
      }}</template>

      <!-- Amount -->
      <template slot="item.balance" slot-scope="props">
        <span> {{ money(props.item.balance) }} </span>
      </template>

      <!-- Top -->
      <template v-slot:top v-if="!printMode">
        <v-btn
          color="error"
          small
          :disabled="!selectedItems.length"
          class="ma-2 text-right"
          @click="deleteMultiple"
          v-if="can('bank_delete')"
          ><v-icon left>mdi-trash-can-outline</v-icon> Delete Selected</v-btn
        >

        <Excel module="banks" :ids="selectedItems.map((item) => item.id)" />
        <CSV module="banks" :ids="selectedItems.map((item) => item.id)" />
        <PDF module="banks" :ids="selectedItems.map((item) => item.id)" />

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
          @click="showEditDialog(props.item.id)"
          title="Edit"
          v-if="can('bank_edit')"
        >
          <v-icon small>mdi-pencil</v-icon>
        </v-btn>
        <v-btn
          x-small
          text
          color="red darken-2"
          @click="setBankId(props.item.id)"
          title="Delete"
          v-if="can('bank_delete')"
        >
          <v-icon small>mdi-delete</v-icon>
        </v-btn>
      </template>
    </v-data-table>

    <!-- Edit dialog -->
    <v-dialog v-model="editDialog" max-width="500" persistent>
      <EditBank
        :single-bank="bank"
        v-if="bank"
        @closeDialog="closeEditDialog"
      />
    </v-dialog>

    <!-- Confirmation -->
    <Confirmation
      ref="confirmationComponent"
      :id="bankId"
      @confirmDeletion="
        bankId ? handleBankDelete() : handleMultipleBanksDelete()
      "
    />
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DatatableMixin from "../../mixins/DatatableMixin";
import EditBank from "./EditBank.vue";
import Confirmation from "../globals/Confirmation";
import Excel from "../globals/exports/Excel.vue";
import CSV from "../globals/exports/CSV.vue";
import PDF from "../globals/exports/PDF.vue";
import CurrencyMixin from "../../mixins/CurrencyMixin";

export default {
  mixins: [DatatableMixin, CurrencyMixin],

  components: {
    EditBank,
    Confirmation,
    Excel,
    CSV,
    PDF,
  },

  data() {
    return {
      editDialog: false,
      paymentSetting: null,
      headers: [
        { text: "S#", value: "sno" },
        { text: "Bank Name", value: "name" },
        { text: "Bank No.", value: "account_no" },
        { text: "Branch Name", value: "branch_name" },
        { text: "Branch Code", value: "branch_code" },
        { text: "Balance", value: "balance" },
        { text: "Actions", value: "actions", align: " d-print-none" },
      ],
      selectedItems: [],

      bankId: null,
    };
  },

  methods: {
    ...mapActions({
      getBanks: "bank/getBanks",
      getBank: "bank/getBank",
      deleteBank: "bank/deleteBank",
      deleteMultipleBanks: "bank/deleteMultipleBanks",
    }),

    showEditDialog(id) {
      this.editDialog = true;

      this.getBank(id);
    },

    closeEditDialog() {
      this.editDialog = false;
    },

    setBankId(id) {
      this.bankId = id;
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleBankDelete() {
      await this.deleteBank(this.bankId);
      this.bankId = null;
      this.$refs.confirmationComponent.setDialog(false);
    },

    async deleteMultiple() {
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleMultipleBanksDelete() {
      const ids = this.selectedItems.map((selectedItem) => selectedItem.id);
      await this.deleteMultipleBanks(ids);

      this.$refs.confirmationComponent.setDialog(false);
      this.selectedItems = [];
    },
  },

  computed: {
    ...mapGetters({
      banks: "bank/banks",
      bank: "bank/bank",
      loading: "loading",
    }),
  },

  mounted() {
    // remove actions if no access is given
    if (!this.can("bank_edit") && !this.can("bank_delete")) {
      this.headers = this.headers.filter(
        (header) => header.value !== "actions"
      );
    }

    this.getBanks();
  },
};
</script>